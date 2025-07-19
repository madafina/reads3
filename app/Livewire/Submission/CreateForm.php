<?php

namespace App\Livewire\Submission;

use App\Models\Division;
use App\Models\TaskCategory;
use App\Models\User;
use App\Models\Submission;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class CreateForm extends Component
{
    use WithFileUploads;

    // Properti untuk binding form
    public $title;
    public $task_category_id;
    public $division_id;
    public $supervisor_id;
    public $presentation_date;
    public $grade;
    public $file;

    // Data untuk dropdown
    public $taskCategories = [];
    public $supervisors = [];
    public $divisions = [];

    // State
    public $showDivisionField = false;
    public $resident;

    public function mount()
    {
        $this->resident = Auth::user()->resident()->with('currentStage')->first();

        // Ambil data untuk dropdown
        $this->taskCategories = TaskCategory::orderBy('name')->get();
        $this->supervisors = User::role('Dosen')->orderBy('name')->get();
        $this->divisions = Division::orderBy('name')->get();

        // Cek apakah residen berada di Tahap II untuk menampilkan pilihan divisi
        if ($this->resident && $this->resident->currentStage && $this->resident->currentStage->name === 'Tahap II') {
            $this->showDivisionField = true;
        }

        // === LOGIKA BARU UNTUK PRE-SELECT FORM ===
        // Cek parameter 'task_category_id' dari URL
        if (request()->has('task_category_id')) {
            $this->task_category_id = request('task_category_id');
        }

        // Cek parameter 'division_id' dari URL (jika ada)
        if (request()->has('division_id')) {
            $this->division_id = request('division_id');
        }
    }

    public function save()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'task_category_id' => 'required|exists:task_categories,id',
            'supervisor_id' => 'required|exists:users,id',
            'presentation_date' => 'required|date',
            'file' => 'required|file|mimes:pdf|max:10240', // PDF max 10MB
            'division_id' => $this->showDivisionField ? 'required|exists:divisions,id' : 'nullable',
            'grade' => 'nullable|numeric|min:0|max:100',
        ]);

        // Simpan file ke storage
        $filePath = $this->file->store('submissions', 'public');

        // Buat record di database
        Submission::create([
            'resident_id' => $this->resident->id,
            'stage_id' => $this->resident->current_stage_id,
            'title' => $this->title,
            'task_category_id' => $this->task_category_id,
            'supervisor_id' => $this->supervisor_id,
            'presentation_date' => $this->presentation_date,
            'file_path' => $filePath,
            'division_id' => $this->showDivisionField ? $this->division_id : null,
            'grade' => $this->grade,
            'status' => 'pending', // Status awal
        ]);

        // Beri notifikasi sukses dan reset form
        session()->flash('success', 'Ilmiah berhasil diunggah dan sedang menunggu verifikasi.');
        
        // Alihkan ke histori setelah berhasil
        return redirect()->route('submissions.history');
    }

    public function render()
    {
        return view('livewire.submission.create-form');
    }
}