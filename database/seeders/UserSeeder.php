<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Resident;
use App\Models\Stage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat User Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@ppdsinternasolo.id'],
            [
                'name' => 'Admin PPDS',
                'password' => Hash::make('password'),
            ]
        );
        $admin->assignRole('Admin');

        // // 2. Buat User Residen Contoh
        // $tahap1 = Stage::where('name', 'Tahap I')->first();
        // if ($tahap1) {
        //     $residenUser = User::firstOrCreate(
        //         ['email' => 'residen@ppdsinternasolo.id'],
        //         [
        //             'name' => 'Mahasiswa Residen',
        //             'password' => Hash::make('password'),
        //         ]
        //     );
        //     $residenUser->assignRole('Residen');

        //     Resident::firstOrCreate(
        //         ['user_id' => $residenUser->id],
        //         [
        //             'nim' => '123456789',
        //             'current_stage_id' => $tahap1->id,
        //             'start_date' => now(),
        //             'batch' => '2024'
        //         ]
        //     );
        // }

        // 3. Buat semua User Dosen dari daftar
        // $this->createLecturerUsers();
    }

    // private function createLecturerUsers(): void
    // {
    //     $lecturerNames = [
    //        'Prof. Dr. dr. HM. Bambang Purwanto, SpPD-KGH, FINASIM',
    //         'dr. Wachid Putranto, Sp.PD, K-GH, FINASIM',
    //         'Dr. dr. Agung Susanto, Sp.PD, K-GH, FINASIM',
    //         'dr. Ratih Tri Kusuma Dewi, SpPD-KGH, FINASIM',
    //         'dr. Aryo Suseno, Sp.PD, K-GH, M.Kes, FINASIM',
    //         'Prof. Dr. dr. Zainal Arifin Adnan, Sp.PD, K-R, FINASIM',
    //         'Dr. dr. Arief Nurudhin, Sp.PD, K-R, FINASIM',
    //         'dr. Yulyani Werdiningsih, Sp.PD, Subsp.R(K), FINASIM',
    //         'dr. Nurhasan Agung Prabowo, SpPD. MKes, FINASIM',
    //         'dr. Suradi Maryono, SpPD-KHOM, FINASIM',
    //         'dr. Sri Marwanta, SpPD, MKes, FINASIM',
    //         'dr. Agus Jati Sunggoro, Sp.PD, K-HOM, FINASIM',
    //         'dr. Kun Salimah, Sp.PD, Subsp.H.Onk.M (K), M.Biomed, FINASIM',
    //         'dr. Sihwidhi Dimas, SpPD',
    //         'Dr. dr. Triyanta Yuli Pramana, Sp.PD, K-GEH, FINASIM',
    //         'dr. P. Kusnanto, SpPD-KGEH, FINASIM',
    //         'dr. Aritantri Darmayani, M.Sc, Sp.PD, K-GEH, FINASIM',
    //         'dr. Didik Prasetyo, Sp.PD, K-GEH, M.Kes, FINASIM',
    //         'Dr. dr. Tatar Sumandjar, Sp.PD, K-PTI, FINASIM',
    //         'Dr. dr. Dhani Redhono H, Sp.PD, K-PTI, FINASIM',
    //         'Dr. dr. Arifin, Sp.PD, K-IC, FINASIM',
    //         'Dr. dr. Evi Nurhayatun, Sp.PD, M.Kes, FINASIM',
    //         'dr. R. Satriyo Budi Susilo, Sp.PD­, K-PTI, M.Kes, FINASIM',
    //         'Dr. dr. Agus Joko Susanto, Sp.PD, K-AI, FINASIM',
    //         'dr. Warigit Dri Atmoko, SpPD, MKes',
    //         'dr. Fatichati Budiningsih, Sp.PD, K-Ger, FINASIM',
    //         'dr. Bayu Basuki Wijaya, Sp.PD, K-Ger, M.Kes, FINASIM',
    //         'dr. Yudhi Hajianto Nugroho, Sp.PD, K-Ger, AIFO-K, M.Kes, FINASIM',
    //         'dr. Supriyanto Kartodarsono, Sp.PD, K-EMD, FINASIM',
    //         'dr. Eva Niamuzisilawati, Sp.PD, Subsp.EMD(K), M.Kes, FINASIM',
    //         'dr. Santy Ayu Perdhana, SpPD',
    //         'dr. Diding Heri Prasetyo, MSi, SpPD, MKes, FINASIM',
    //         'dr. Ratih Arianita Agung, Sp.PD, K-Psi, M.Kes, FINASIM',
    //         'dr. Yulia Sekarsari, Sp.PD',
    //         'dr. Fatna Andika Wati, Sp.PD',
    //         'dr. Brilliant Van Vitof, Sp.PD',
    //         'dr. Ega Caesaria Pratama P, Sp.PD',
    //         'dr. Hastin Mutiara Surga, Sp.PD',
    //         'dr. Indrayana Sunarso, Sp.PD',
    //         'dr. Santy Ayu Puspita Perdhana, Sp.PD',
    //         'dr. Sihwidhi Dimas Sudarmadi, Sp.PD',
    //         'Dr. dr. Sri Marwanta, Sp.PD, Fel.Onk, M.Kes, FINASIM',
    //     ]; 

    //     foreach ($lecturerNames as $name) {
    //         $email = strtolower(Str::slug(explode(',', $name)[0], '.')) . '@ppdsinternasolo.id';
    //         $user = User::firstOrCreate(
    //             ['email' => $email],
    //             [
    //                 'name' => $name,
    //                 'password' => Hash::make('password')
    //             ]
    //         );
    //         $user->assignRole('Dosen');
    //     }
    // }
}