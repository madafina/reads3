<?php

namespace Database\Seeders;

use App\Models\Stage;
use Illuminate\Database\Seeder;

class StageSeeder extends Seeder
{
    public function run(): void
    {
        Stage::firstOrCreate(['name' => 'Tahap I', 'order' => 1]);
        Stage::firstOrCreate(['name' => 'Tahap II', 'order' => 2]);
        Stage::firstOrCreate(['name' => 'Tahap III', 'order' => 3]);
        Stage::firstOrCreate(['name' => 'Tesis', 'order' => 4]);
    }
}