<?php

namespace Database\Seeders;

use App\Models\Stage;
use App\Models\Division;
use App\Models\TaskCategory;
use App\Models\RequirementRule;
use Illuminate\Database\Seeder;

class RequirementRuleSeeder extends Seeder
{
    public function run(): void
    {
        $tahap1 = Stage::where('name', 'Tahap I')->first();
        $tahap2 = Stage::where('name', 'Tahap II')->first();

        $divisiParu = Division::where('name', 'Pulmonologi')->first();
        $divisiKardio = Division::where('name', 'Kardiologi Vaskular')->first();

        $taskJr = TaskCategory::where('name', 'Journal Reading')->first();
        $taskLapsus = TaskCategory::where('name', 'Laporan Kasus')->first();
        $taskRefrat = TaskCategory::where('name', 'Tinjauan Pustaka')->first();
        $taskCbd = TaskCategory::where('name', 'Case Based Discussion')->first();
        $taskMinicx = TaskCategory::where('name', 'Mini CX')->first();
        
        // Aturan Tahap I: Minicx 5x
        $ruleMinicx = RequirementRule::firstOrCreate(
            ['name' => 'Kewajiban Mini CX Tahap I', 'stage_id' => $tahap1->id],
            ['required_count' => 5]
        );
        $ruleMinicx->taskCategories()->sync([$taskMinicx->id]);

        // Aturan Standar Tahap II (division_id = null)
        $ruleStdJr = RequirementRule::firstOrCreate(
            ['name' => 'Kewajiban JR Divisi Standar', 'stage_id' => $tahap2->id, 'division_id' => null],
            ['required_count' => 3]
        );
        $ruleStdJr->taskCategories()->sync([$taskJr->id]);
        
        $ruleStdLapsusRefrat = RequirementRule::firstOrCreate(
            ['name' => 'Kewajiban Lapsus/Refrat Divisi Standar', 'stage_id' => $tahap2->id, 'division_id' => null],
            ['required_count' => 1]
        );
        $ruleStdLapsusRefrat->taskCategories()->sync([$taskLapsus->id, $taskRefrat->id]);
        
        $ruleStdCbd = RequirementRule::firstOrCreate(
            ['name' => 'Kewajiban CBD Divisi Standar', 'stage_id' => $tahap2->id, 'division_id' => null],
            ['required_count' => 1]
        );
        $ruleStdCbd->taskCategories()->sync([$taskCbd->id]);

        // Aturan Khusus Divisi Paru Tahap II
        $ruleParu = RequirementRule::firstOrCreate(
            ['name' => 'Kewajiban Khusus Divisi Paru', 'stage_id' => $tahap2->id, 'division_id' => $divisiParu->id],
            ['required_count' => 1]
        );
        $ruleParu->taskCategories()->sync([$taskJr->id]);

        // Aturan Khusus Divisi Kardio Tahap II
        $ruleKardio = RequirementRule::firstOrCreate(
            ['name' => 'Kewajiban Khusus Divisi Kardio', 'stage_id' => $tahap2->id, 'division_id' => $divisiKardio->id],
            ['required_count' => 1]
        );
        $ruleKardio->taskCategories()->sync([$taskLapsus->id, $taskRefrat->id]);
    }
}