<?php

namespace App\Services;

use App\Models\Division;
use App\Models\Resident;
use App\Models\Stage;
use App\Models\Submission;

class ResidentProgressService
{
    /**
     * Memeriksa apakah seorang residen telah menyelesaikan semua kewajiban di tahapnya saat ini.
     *
     * @param Resident $resident
     * @return bool
     */
    public function isStageComplete(Resident $resident): bool
    {
        $currentStage = $resident->currentStage;

        if (!$currentStage) {
            return false;
        }

        $rules = $currentStage->requirementRules;

        // Logika khusus untuk Tahap II yang lebih kompleks
        if ($currentStage->name === 'Tahap II') {
            return $this->isStageTwoComplete($resident, $rules);
        }

        // Logika untuk tahap lainnya
        foreach ($rules as $rule) {
            if (!$this->isRuleComplete($resident, $rule)) {
                return false; // Jika ada satu saja aturan yang belum lengkap, return false
            }
        }

        return true;
    }

    /**
     * Logika spesifik untuk memeriksa kelengkapan Tahap II.
     * Residen harus menyelesaikan kewajiban di SEMUA divisi.
     */
    private function isStageTwoComplete(Resident $resident, $stageRules): bool
    {
        $allDivisions = Division::all();
        $specificRulesByDivisionId = $stageRules->whereNotNull('division_id')->groupBy('division_id');
        $standardRules = $stageRules->whereNull('division_id');

        foreach ($allDivisions as $division) {
            $rulesForThisDivision = $specificRulesByDivisionId[$division->id] ?? $standardRules;
            foreach ($rulesForThisDivision as $rule) {
                if (!$this->isRuleComplete($resident, $rule)) {
                    return false; // Jika ada satu kewajiban di satu divisi yang belum lengkap, maka seluruh tahap belum lengkap.
                }
            }
        }

        return true;
    }

    /**
     * Memeriksa apakah satu aturan spesifik sudah dipenuhi oleh residen.
     */
    private function isRuleComplete(Resident $resident, $rule): bool
    {
        $categoryIds = $rule->taskCategories->pluck('id');

        $completedCount = Submission::where('resident_id', $resident->id)
            ->whereIn('task_category_id', $categoryIds)
            ->where('status', 'verified')
            ->when($rule->division_id, function ($query) use ($rule) {
                return $query->where('division_id', $rule->division_id);
            })
            ->count();

        return $completedCount >= $rule->required_count;
    }
}