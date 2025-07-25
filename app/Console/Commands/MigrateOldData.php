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

    // Properti untuk menyimpan peta ID dari lama ke baru
    private $userMap = [], $residentMap = [], $dosenMap = [], $categoryMap = [], $divisionMap = [], $stageMap = [];

    public function handle()
    {
        if (!$this->confirm('PERINGATAN: Ini akan MENGHAPUS semua data pengguna, residen, dan tugas di database baru sebelum memulai. Lanjutkan?')) {
            $this->info('Migrasi dibatalkan.');
            return;
        }

        $this->info('Memulai migrasi data...');

        // 1. Kosongkan tabel target di database baru untuk menghindari duplikasi
        Schema::disableForeignKeyConstraints();
        User::truncate();
        Resident::truncate();
        Submission::truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('resident_stage')->truncate();
        Schema::enableForeignKeyConstraints();

        // 2. Bangun peta untuk data master (kategori, divisi, tahap)
        $this->buildMasterDataMaps();

        // 3. Jalankan migrasi dalam sebuah transaksi agar aman
        DB::transaction(function () {
            $this->migrateUsersAndProfiles();
            $this->migrateSubmissions();
            $this->migrateStageHistory();
        });

        $this->info('Migrasi data berhasil diselesaikan!');
        $this->warn('PENTING: Jalankan `php artisan storage:link` jika Anda belum melakukannya.');
    }

    /**
     * Membuat peta (array asosiatif) dari ID lama ke ID baru untuk data master.
     */
    private function buildMasterDataMaps()
    {
        $this->line('Membangun peta data master...');
        
        // Peta Kategori
        $oldCategories = DB::connection('mysql_old')->table('kategori_ilmiah')->get();
        foreach ($oldCategories as $oldCat) {
            $newCat = TaskCategory::where('name', 'like', trim($oldCat->kategori).'%')->first();
            if ($newCat) {
                $this->categoryMap[$oldCat->id] = $newCat->id;
            }
        }

        // Peta Divisi
        $oldDivisions = DB::connection('mysql_old')->table('divisi')->get();
        foreach ($oldDivisions as $oldDiv) {
            $newDiv = Division::where('name', 'like', $oldDiv->divisi.'%')->first();
            if ($newDiv) {
                $this->divisionMap[$oldDiv->id] = $newDiv->id;
            }
        }

        // Peta Tahap (disesuaikan dengan struktur baru)
        $this->stageMap = [
            1 => Stage::where('name', 'Tahap I')->first()->id,
            2 => Stage::where('name', 'Tahap II')->first()->id, // 2a
            3 => Stage::where('name', 'Tahap II')->first()->id, // 2b
            4 => Stage::where('name', 'Tahap III')->first()->id,
            5 => Stage::where('name', 'Tesis')->first()->id,
        ];
    }

    /**
     * Memigrasikan data dari ci_users, residen, dan dosen.
     */
    private function migrateUsersAndProfiles()
    {
        $this->line('Memigrasikan pengguna, residen, dan dosen...');
        $oldUsers = DB::connection('mysql_old')->table('ci_users')->get();
        $progressBar = $this->output->createProgressBar(count($oldUsers));

        foreach ($oldUsers as $oldUser) {
            // Lewati jika email duplikat
            if (User::where('email', $oldUser->email)->exists()) {
                $this->warn(" Melewati pengguna dengan email duplikat: {$oldUser->email}");
                $progressBar->advance();
                continue;
            }

            $oldProfile = null;
            $name = $oldUser->username;
            if ($oldUser->role == 2) { // Dosen
                $oldProfile = DB::connection('mysql_old')->table('dosen')->where('user_id', $oldUser->id)->first();
                if ($oldProfile) $name = $oldProfile->nama_lengkap;
            }
            if ($oldUser->role == 3) { // Residen
                $oldProfile = DB::connection('mysql_old')->table('residen')->where('user_id', $oldUser->id)->first();
                if ($oldProfile) $name = $oldProfile->nama_lengkap;
            }
            if (empty($name)) $name = $oldUser->email; // Fallback jika nama kosong

            $newUser = User::create([
                'name' => $name,
                'email' => $oldUser->email,
                'password' => $oldUser->password, // Langsung salin hash bcrypt
                'created_at' => $this->sanitizeDate($oldUser->created_at),
                'updated_at' => $this->sanitizeDate($oldUser->updated_at),
            ]);
            $this->userMap[$oldUser->id] = $newUser->id;

            if ($oldUser->role == 1) $newUser->assignRole('Admin');
            if ($oldUser->role == 2) {
                $newUser->assignRole('Dosen');
                if ($oldProfile) $this->dosenMap[$oldProfile->id] = $newUser->id;
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

    /**
     * Memigrasikan data tugas ilmiah.
     */
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

    /**
     * Memigrasikan riwayat tahap residen.
     */
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
    }

    /**
     * Membersihkan nilai tanggal yang tidak valid.
     */
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

    /**
     * Membersihkan path file lama.
     */
    private function transformOldPath($oldPath)
    {
        if (empty($oldPath)) {
            return null;
        }
        return ltrim($oldPath, './ ');
    }
}
