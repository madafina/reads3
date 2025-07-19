<?php

namespace App\Livewire\Admin;

use App\Models\Submission;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SubmissionAction extends Component
{
    public Submission $submission;

    public function verify()
    {
        $this->submission->update([
            'status' => 'verified',
            'verified_by' => Auth::id(),
            'verified_at' => now(),
        ]);

        // Kirim event untuk me-refresh datatable di halaman utama
        $this->dispatch('submissionUpdated');
    }

    public function reject()
    {
        $this->submission->update([
            'status' => 'rejected',
            'verified_by' => Auth::id(),
            'verified_at' => now(),
        ]);
        
        // Kirim event untuk me-refresh datatable di halaman utama
        $this->dispatch('submissionUpdated');
    }

    public function render()
    {
        // View ini hanya akan berisi tombol
        return view('livewire.admin.submission-action');
    }
}