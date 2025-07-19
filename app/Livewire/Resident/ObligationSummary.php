<?php

namespace App\Livewire\Resident;

use App\Models\Division;
use App\Models\Stage;
use App\Models\Submission;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ObligationSummary extends Component
{
    public $stages;
    public $allDivisions;
    public $residentId;
    public $currentStageId; // Properti baru untuk menyimpan ID tahap saat ini

    public function mount()
    {
        $resident = Auth::user()->resident;
        if (!$resident) {
            $this->stages = collect();
            $this->allDivisions = collect();
            return;
        }
        $this->residentId = $resident->id;
        $this->currentStageId = $resident->current_stage_id; // Mengambil ID tahap saat ini

        // Eager load semua relasi yang dibutuhkan
        $this->stages = Stage::with([
            'requirementRules.division',
            'requirementRules.taskCategories'
        ])->orderBy('order')->get();
        
        $this->allDivisions = Division::orderBy('name')->get();

        // Hitung progres untuk setiap aturan
        $allRules = $this->stages->pluck('requirementRules')->flatten();
        foreach ($allRules as $rule) {
            $categoryIds = $rule->taskCategories->pluck('id');

            $completedCount = Submission::where('resident_id', $this->residentId)
                ->whereIn('task_category_id', $categoryIds)
                ->where('status', 'verified')
                ->when($rule->division_id, function ($query) use ($rule) {
                    return $query->where('division_id', $rule->division_id);
                })
                ->count();
            
            $rule->completed_count = $completedCount;
        }
    }

    public function render()
    {
        return view('livewire.resident.obligation-summary');
    }
}