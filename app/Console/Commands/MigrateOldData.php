<?php

namespace App\Console\Commands;

use App\Models\Division;
use App\Models\Resident;
use App\Models\Stage;
use App\Models\Submission;
use App\Models\TaskCategory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrateOldData extends Command
{
    protected $signature = 'app:migrate-old-data';
    protected $description = 'Migrate data from the old CodeIgniter database to the new Laravel database';

    private $userMap = [], $residentMap = [], $dosenMap = [], $categoryMap = [], $divisionMap = [], $stageMap = [];

    public function handle()
    {
        if (!$this->confirm('This will wipe users, residents, and submissions tables before migrating. Do you wish to continue?')) {
            $this->info('Migration cancelled.');
            return;
        }

        $this->info('Starting data migration...');

        Schema::disableForeignKeyConstraints();
        User::truncate();
        Resident::truncate();
        Submission::truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('resident_stage')->truncate();
        Schema::enableForeignKeyConstraints();

        $this->buildMasterDataMaps();

        DB::transaction(function () {
            $this->migrateUsersAndProfiles();
            $this->migrateSubmissions();
            $this->migrateStageHistory();
        });

        $this->info('Data migration completed successfully!');
    }

    private function sanitizeDate($dateString)
    {
        if (empty($dateString) || $dateString === '0000-00-00 00:00:00') {
            return null;
        }
        try {
            return Carbon::parse($dateString);
        } catch (\Exception $e) {
            return null;
        }
    }

    private function buildMasterDataMaps()
    {
        $this->line('Building master data maps...');
        $oldCategories = DB::connection('mysql_old')->table('kategori_ilmiah')->get();
        foreach ($oldCategories as $oldCat) {
            $newCat = TaskCategory::where('name', 'like', trim($oldCat->kategori).'%')->first();
            if ($newCat) $this->categoryMap[$oldCat->id] = $newCat->id;
        }

        $oldDivisions = DB::connection('mysql_old')->table('divisi')->get();
        foreach ($oldDivisions as $oldDiv) {
            $newDiv = Division::where('name', 'like', $oldDiv->divisi.'%')->first();
            if ($newDiv) $this->divisionMap[$oldDiv->id] = $newDiv->id;
        }

        $this->stageMap = [
            1 => Stage::where('name', 'Tahap I')->first()->id,
            2 => Stage::where('name', 'Tahap II')->first()->id,
            3 => Stage::where('name', 'Tahap II')->first()->id,
            4 => Stage::where('name', 'Tahap III')->first()->id,
            5 => Stage::where('name', 'Tesis')->first()->id,
        ];
    }

    private function migrateUsersAndProfiles()
    {
        $this->line('Migrating users, residents, and lecturers...');
        $oldUsers = DB::connection('mysql_old')->table('ci_users')->get();
        $progressBar = $this->output->createProgressBar(count($oldUsers));

        foreach ($oldUsers as $oldUser) {
            if (User::where('email', $oldUser->email)->exists()) {
                $this->warn("Skipping user with duplicate email: {$oldUser->email}");
                $progressBar->advance();
                continue;
            }

            $oldProfile = null;
            $name = $oldUser->username;
            if ($oldUser->role == 2) {
                $oldProfile = DB::connection('mysql_old')->table('dosen')->where('user_id', $oldUser->id)->first();
                if ($oldProfile) $name = $oldProfile->nama_lengkap;
            }
            if ($oldUser->role == 3) {
                $oldProfile = DB::connection('mysql_old')->table('residen')->where('user_id', $oldUser->id)->first();
                if ($oldProfile) $name = $oldProfile->nama_lengkap;
            }
            
            if (empty($name)) $name = $oldUser->email;

            $newUser = User::create([
                'name' => $name,
                'email' => $oldUser->email,
                'password' => $oldUser->password,
                'created_at' => $this->sanitizeDate($oldUser->created_at),
                'updated_at' => $this->sanitizeDate($oldUser->updated_at),
            ]);
            $this->userMap[$oldUser->id] = $newUser->id;

            if ($oldUser->role == 1) $newUser->assignRole('Admin');
            if ($oldUser->role == 2) {
                $newUser->assignRole('Dosen');
                if ($oldProfile) {
                    $this->dosenMap[$oldProfile->id] = $newUser->id;
                }
            }
            if ($oldUser->role == 3) {
                $newUser->assignRole('Residen');
                if ($oldProfile) {
                    if (empty($oldProfile->nim) || Resident::where('nim', $oldProfile->nim)->exists()) {
                        $progressBar->advance();
                        continue;
                    }
                    $newResident = Resident::create([
                        'user_id' => $newUser->id,
                        'nim' => $oldProfile->nim,
                        'batch' => $oldProfile->angkatan,
                        'start_date' => $this->sanitizeDate($oldUser->created_at),
                    ]);
                    $this->residentMap[$oldProfile->id] = $newResident->id;
                }
            }
            $progressBar->advance();
        }
        $progressBar->finish();
        $this->newLine(2);
    }

    private function migrateSubmissions()
    {
        $this->line('Migrating submissions...');
        $oldSubmissions = DB::connection('mysql_old')->table('ilmiah')->get();
        $progressBar = $this->output->createProgressBar(count($oldSubmissions));

        foreach ($oldSubmissions as $oldSub) {
            $newResidentId = $this->residentMap[$oldSub->id_residen] ?? null;
            $newSupervisorId = $this->dosenMap[$oldSub->id_staf] ?? null;
            $newCategoryId = $this->categoryMap[$oldSub->id_kategori] ?? null;
            $newDivisionId = $this->divisionMap[$oldSub->id_divisi] ?? null;
            $newStageId = $this->stageMap[$oldSub->id_tahap] ?? null;

            if (!$newResidentId) { $this->warn(" Skip submission {$oldSub->id}: Resident not found."); $progressBar->advance(); continue; }
            if (!$newSupervisorId) { $this->warn(" Skip submission {$oldSub->id}: Supervisor not found."); $progressBar->advance(); continue; }
            if (!$newCategoryId) { $this->warn(" Skip submission {$oldSub->id}: Category not found."); $progressBar->advance(); continue; }

            // === PERBAIKAN DI SINI ===
            // Sediakan nilai fallback untuk presentation_date jika tgl_maju tidak valid.
            $presentationDate = $this->sanitizeDate($oldSub->tgl_maju) ?? $this->sanitizeDate($oldSub->date) ?? now();

            Submission::create([
                'resident_id' => $newResidentId,
                'supervisor_id' => $newSupervisorId,
                'task_category_id' => $newCategoryId,
                'division_id' => $newDivisionId,
                'stage_id' => $newStageId,
                'title' => $oldSub->judul_ilmiah,
                'description' => $oldSub->deskripsi,
                'seminar_title' => $oldSub->judul_seminar,
                'file_path' => $oldSub->file,
                'presentation_file_path' => $oldSub->file_presentasi,
                'grade_file_path' => $oldSub->file_nilai,
                'attendance_file_path' => $oldSub->file_presensi,
                'grade' => $oldSub->nilai,
                'status' => $oldSub->status == 1 ? 'verified' : 'pending',
                'presentation_date' => $presentationDate,
                'created_at' => $this->sanitizeDate($oldSub->date),
            ]);
            $progressBar->advance();
        }
        $progressBar->finish();
        $this->newLine(2);
    }

    private function migrateStageHistory()
    {
        $this->line('Migrating resident stage history...');
        $oldStageHistory = DB::connection('mysql_old')->table('residen_tahap')->get();
        $progressBar = $this->output->createProgressBar(count($oldStageHistory));

        foreach($oldStageHistory as $history) {
            $newResidentId = $this->residentMap[$history->id_residen] ?? null;
            $newStageId = $this->stageMap[$history->tahap] ?? null;

            if (!$newResidentId || !$newStageId) continue;

            DB::table('resident_stage')->insert([
                'resident_id' => $newResidentId,
                'stage_id' => $newStageId,
                'status' => $history->status == 1 ? 'completed' : 'active',
                'start_date' => $this->sanitizeDate($history->start_date),
                'end_date' => $this->sanitizeDate($history->end_date),
                'created_at' => $this->sanitizeDate($history->date),
                'updated_at' => $this->sanitizeDate($history->date),
            ]);
            
            if ($history->aktif == 1) {
                Resident::where('id', $newResidentId)->update(['current_stage_id' => $newStageId]);
            }
            $progressBar->advance();
        }
        $progressBar->finish();
        $this->newLine(2);
    }
}
