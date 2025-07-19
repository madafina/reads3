<?php

namespace Database\Seeders;

use App\Models\Division;
use Illuminate\Database\Seeder;

class DivisionSeeder extends Seeder
{
    public function run(): void
    {
        $divisions = [
            'Nefro', 'Gastro', 'Tropik Infeksi', 'Endokrin', 'Hemato', 
            'Remato', 'Pulmonologi', 'Kardiologi Vaskular', 'Geriatri', 
            'Alergi Imunologi', 'Psikosomatis',
        ];

        foreach ($divisions as $divisionName) {
            Division::firstOrCreate(['name' => $divisionName]);
        }
    }
}