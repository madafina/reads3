<?php

namespace App\Livewire\Admin;

use App\Models\Resident;
use App\Models\Submission;
use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Layout;

class Dashboard extends Component
{
    public $pendingSubmissionsCount = 0;
    public $totalResidentsCount = 0;
    public $totalLecturersCount = 0;

    public function mount()
    {
        // Menghitung jumlah tugas yang statusnya 'pending'
        $this->pendingSubmissionsCount = Submission::where('status', 'pending')->count();
        
        // Menghitung jumlah residen
        $this->totalResidentsCount = Resident::count();

        // Menghitung jumlah dosen
        $this->totalLecturersCount = User::role('Dosen')->count();
    }

    #[Layout('adminlte::page')]
    public function render()
    {
        return view('livewire.admin.dashboard');
    }
}