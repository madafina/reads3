<?php

namespace Database\Seeders;

use App\Models\Stage;
use App\Models\Division;
use App\Models\TaskCategory;
use App\Models\RequirementRule;
use Illuminate\Database\Seeder;

class RequirementRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ambil semua data master yang dibutuhkan
        $tahap1 = Stage::where('name', 'Tahap I')->firstOrFail();
        $tahap2 = Stage::where('name', 'Tahap II')->firstOrFail();

        $divisiParu = Division::where('name', 'Pulmonologi')->firstOrFail();
        $divisiKardio = Division::where('name', 'Kardiologi Vaskular')->firstOrFail();

        $taskPemeriksaanFisik = TaskCategory::where('name', 'Pemeriksaan Fisik')->firstOrFail();
        $taskSindromaKlinis = TaskCategory::where('name', 'Sindroma Klinis')->firstOrFail();
        $taskMiniCx = TaskCategory::where('name', 'Mini CX')->firstOrFail();
        $taskJr = TaskCategory::where('name', 'Journal Reading')->firstOrFail();
        $taskLapsus = TaskCategory::where('name', 'Laporan Kasus')->firstOrFail();
        $taskRefrat = TaskCategory::where('name', 'Tinjauan Pustaka')->firstOrFail();
        $taskCbd = TaskCategory::where('name', 'Case Based Discussion')->firstOrFail();

        // 2. Hapus aturan lama agar tidak tumpang tindih (opsional tapi direkomendasikan)
        RequirementRule::query()->delete();

        // =================================================================
        // ATURAN UNTUK TAHAP I
        // =================================================================
        $ruleTahap1Fisik = RequirementRule::create(['name' => 'Tugas Pemeriksaan Fisik Tahap I', 'stage_id' => $tahap1->id, 'required_count' => 1]);
        $ruleTahap1Fisik->taskCategories()->attach($taskPemeriksaanFisik->id);

        $ruleTahap1Sindrom = RequirementRule::create(['name' => 'Tugas Sindroma Klinis Tahap I', 'stage_id' => $tahap1->id, 'required_count' => 1]);
        $ruleTahap1Sindrom->taskCategories()->attach($taskSindromaKlinis->id);
        
        $ruleTahap1MiniCx = RequirementRule::create(['name' => 'Tugas Mini CX Tahap I', 'stage_id' => $tahap1->id, 'required_count' => 5]);
        $ruleTahap1MiniCx->taskCategories()->attach($taskMiniCx->id);

        // =================================================================
        // ATURAN STANDAR UNTUK TAHAP II (division_id = null)
        // =================================================================
        $ruleStdJr = RequirementRule::create(['name' => 'Tugas JR Divisi Standar', 'stage_id' => $tahap2->id, 'division_id' => null, 'required_count' => 3]);
        $ruleStdJr->taskCategories()->attach($taskJr->id);
        
        $ruleStdLapsusRefrat = RequirementRule::create(['name' => 'Tugas Lapsus/Refrat Divisi Standar', 'stage_id' => $tahap2->id, 'division_id' => null, 'required_count' => 1]);
        $ruleStdLapsusRefrat->taskCategories()->attach([$taskLapsus->id, $taskRefrat->id]); // Kondisi ATAU
        
        $ruleStdCbd = RequirementRule::create(['name' => 'Tugas CBD Divisi Standar', 'stage_id' => $tahap2->id, 'division_id' => null, 'required_count' => 1]);
        $ruleStdCbd->taskCategories()->attach($taskCbd->id);

        // =================================================================
        // ATURAN PENGECUALIAN UNTUK DIVISI TERTENTU DI TAHAP II
        // =================================================================
        
        // Khusus Divisi Paru: Hanya JR 1x
        $ruleParu = RequirementRule::create(['name' => 'Tugas Khusus Divisi Paru (JR)', 'stage_id' => $tahap2->id, 'division_id' => $divisiParu->id, 'required_count' => 1]);
        $ruleParu->taskCategories()->attach($taskJr->id);

        // Khusus Divisi Kardio: Hanya Refrat/Lapsus 1x
        $ruleKardio = RequirementRule::create(['name' => 'Tugas Khusus Divisi Kardio (Refrat/Lapsus)', 'stage_id' => $tahap2->id, 'division_id' => $divisiKardio->id, 'required_count' => 1]);
        $ruleKardio->taskCategories()->attach([$taskLapsus->id, $taskRefrat->id]); // Kondisi ATAU
    }
}