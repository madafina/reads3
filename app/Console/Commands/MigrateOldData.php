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
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class MigrateOldData extends Command
{
    protected $signature = 'app:migrate-old-data';
    protected $description = 'Migrate data from the old CodeIgniter database to the new Laravel database';

    private $userMap = [], $residentMap = [], $dosenMap = [], $categoryMap = [], $divisionMap = [], $stageMap = [];

    public function handle()
    {
        if (!$this->confirm('PERINGATAN: Ini akan MENGHAPUS semua data pengguna, residen, dan tugas di database baru sebelum memulai. Lanjutkan?')) {
            $this->info('Migrasi dibatalkan.');
            return;
        }

        $this->info('Memulai migrasi data...');

        Schema::disableForeignKeyConstraints();
        User::truncate();
        Resident::truncate();
        Submission::truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('resident_stage')->truncate();
        Schema::enableForeignKeyConstraints();

        $this->buildMasterDataMaps();

        DB::transaction(function () {
            $this->migrateUsersAndRoles();
            $this->migrateProfiles();
            $this->migrateSubmissions();
            $this->migrateStageHistory();
        });

        $this->info('Migrasi data berhasil diselesaikan!');
        $this->warn('PENTING: Jalankan `php artisan storage:link` jika Anda belum melakukannya.');
    }

    private function buildMasterDataMaps()
    {
        $this->line('Membangun peta data master...');
        
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

    private function migrateUsersAndRoles()
    {
        $this->line('Memigrasikan data login pengguna (ci_users)...');
        $oldUsers = DB::connection('mysql_old')->table('ci_users')->get();
        $progressBar = $this->output->createProgressBar(count($oldUsers));

        foreach ($oldUsers as $oldUser) {
            if (User::where('email', $oldUser->email)->exists()) {
                $progressBar->advance();
                continue;
            }

            $newUser = User::create([
                'name' => $oldUser->username, // Nama akan diperbarui nanti dari profil
                'email' => $oldUser->email,
                'password' => Hash::make('123456'),
                'created_at' => $this->sanitizeDate($oldUser->created_at),
                'updated_at' => $this->sanitizeDate($oldUser->updated_at),
            ]);
            $this->userMap[$oldUser->id] = $newUser->id;

            if ($oldUser->role == 1) $newUser->assignRole('Admin');
            
            $progressBar->advance();
        }
        $progressBar->finish();
        $this->newLine(2);
    }

    private function migrateProfiles()
    {
        $this->line('Membuat profil dan menetapkan peran Dosen & Residen...');
        
        // Proses Dosen
        $oldLecturers = DB::connection('mysql_old')->table('dosen')->get();
        foreach($oldLecturers as $oldLecturer) {
            $newUserId = $this->userMap[$oldLecturer->user_id] ?? null;
            if (!$newUserId) continue;

            $user = User::find($newUserId);
            if ($user) {
                $user->name = $oldLecturer->nama_lengkap;
                $user->save();
                $user->assignRole('Dosen');
                $this->dosenMap[$oldLecturer->id] = $user->id;
            }
        }

        // Proses Residen
        $oldResidents = DB::connection('mysql_old')->table('residen')->get();
        foreach($oldResidents as $oldResident) {
            $newUserId = $this->userMap[$oldResident->user_id] ?? null;
            if (!$newUserId) continue;

            $user = User::find($newUserId);
            if ($user) {
                $user->name = $oldResident->nama_lengkap;
                $user->save();
                $user->assignRole('Residen');

                if (empty($oldResident->nim) || Resident::where('nim', $oldResident->nim)->exists()) {
                    continue;
                }

                $newResident = Resident::create([
                    'user_id' => $user->id,
                    'nim' => $oldResident->nim,
                    'batch' => $oldResident->angkatan,
                    'start_date' => $this->sanitizeDate($user->created_at),
                ]);
                $this->residentMap[$oldResident->id] = $newResident->id;
            }
        }
    }

    private function migrateSubmissions()
    {
        $this->line('Memigrasikan tugas ilmiah...');
        $oldSubmissions = DB::connection('mysql_old')->table('ilmiah')->get();
        $progressBar = $this->output->createProgressBar(count($oldSubmissions));

        foreach ($oldSubmissions as $oldSub) {
            $newResidentId = $this->residentMap[$oldSub->id_residen] ?? null;
            $newSupervisorId = $this->dosenMap[$oldSub->id_staf] ?? null;
            $newCategoryId = $this->categoryMap[$oldSub->id_kategori] ?? null;
            $newDivisionId = $this->divisionMap[$oldSub->id_divisi] ?? null;
            $newStageId = $this->stageMap[$oldSub->id_tahap] ?? null;

            if (!$newResidentId || !$newSupervisorId || !$newCategoryId) {
                $progressBar->advance();
                continue;
            }

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
                'grade' => $oldSub->nilai,
                'status' => $oldSub->status == 1 ? 'verified' : 'pending',
                'presentation_date' => $presentationDate,
                'created_at' => $this->sanitizeDate($oldSub->date),
                'file_path' => $this->transformOldPath($oldSub->file),
                'presentation_file_path' => $this->transformOldPath($oldSub->file_presentasi),
                'grade_file_path' => $this->transformOldPath($oldSub->file_nilai),
                'attendance_file_path' => $this->transformOldPath($oldSub->file_presensi),
            ]);
            $progressBar->advance();
        }
        $progressBar->finish();
        $this->newLine(2);
    }

    private function migrateStageHistory()
    {
        $this->line('Memigrasikan riwayat tahap residen...');
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
        
        $this->assignDefaultStageForOrphans();
    }

    private function assignDefaultStageForOrphans()
    {
        $this->line('Menetapkan tahap default untuk residen yang belum memiliki tahap...');
        $stage1Id = $this->stageMap[1];

        $orphanResidents = Resident::whereNull('current_stage_id')->get();

        if ($orphanResidents->isEmpty()) {
            $this->info('Tidak ada residen tanpa tahap yang ditemukan. Langkah ini dilewati.');
            return;
        }

        foreach ($orphanResidents as $resident) {
            $resident->update(['current_stage_id' => $stage1Id]);
            $hasHistory = DB::table('resident_stage')->where('resident_id', $resident->id)->exists();
            if (!$hasHistory) {
                DB::table('resident_stage')->insert([
                    'resident_id' => $resident->id,
                    'stage_id' => $stage1Id,
                    'status' => 'active',
                    'start_date' => $resident->start_date ?? now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $this->info("Residen '{$resident->user->name}' telah ditetapkan ke Tahap I.");
            }
        }
    }

    private function sanitizeDate($dateString)
    {
        if (empty($dateString) || $dateString === '0000-00-00 00:00:00') return null;
        try { return Carbon::parse($dateString); } catch (\Exception $e) { return null; }
    }

    private function transformOldPath($oldPath)
    {
        if (empty($oldPath)) return null;
        return ltrim($oldPath, './ ');
    }
}
