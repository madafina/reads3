<?php

namespace App\Livewire\Resident;

use App\Models\RequirementRule;
use App\Models\Submission;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public $resident;
    public $rules;
    public $completionData = [];

    public function mount()
    {
        $this->resident = Auth::user()->resident()->with('currentStage')->first();

        if (!$this->resident || !$this->resident->currentStage) {
            // Handle jika residen tidak punya tahap aktif
            return;
        }

        // Ambil semua aturan untuk tahap residen saat ini
        $this->rules = RequirementRule::where('stage_id', $this->resident->current_stage_id)
            ->with('taskCategories')
            ->get();
            
        $this->calculateCompletion();
    }

    public function calculateCompletion()
    {
        foreach ($this->rules as $rule) {
            $categoryIds = $rule->taskCategories->pluck('id');

            $completedCount = Submission::where('resident_id', $this->resident->id)
                ->whereIn('task_category_id', $categoryIds)
                ->where('status', 'verified')
                ->count();

            $this->completionData[$rule->id] = [
                'completed' => $completedCount,
                'required' => $rule->required_count,
                'is_completed' => $completedCount >= $rule->required_count,
            ];
        }
    }

    public function render()
    {
        return view('livewire.resident.dashboard');
    }
}