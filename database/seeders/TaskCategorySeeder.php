<?php

namespace Database\Seeders;

use App\Models\TaskCategory;
use Illuminate\Database\Seeder;

class TaskCategorySeeder extends Seeder
{
    public function run(): void
    {
        $taskNames = [
            'Pemeriksaan Fisik', 'Sindroma Klinis', 'Laporan Kasus', 'Mini CX', 
            'Pretest', 'Postest', 'Tinjauan Pustaka', 'Journal Reading', 'DOPS',
            'Case Based Discussion', 'Proposal Tesis', 'Tesis', 'Ilmiah Nasional', 
            'Etika/Kerjasama', 'Tugas Tambahan', 'SIP Residen', 'Berita Acara Proposal Tesis',
        ];

        foreach ($taskNames as $name) {
            TaskCategory::firstOrCreate(['name' => $name]);
        }
    }
}