<?php

namespace App\Livewire\Submission;

use App\Models\Division;
use App\Models\Submission;
use App\Models\TaskCategory;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class CreateForm extends Component
{
    use WithFileUploads;

    // Properti form lama
    public $title;
    public $task_category_id;
    public $division_id;
    public $supervisor_id;
    public $presentation_date;
    public $grade;
    public $file;

    // Properti baru
    public $description;
    public $seminar_title;
    public $presentation_file;
    public $grade_file;
    public $attendance_file;

    // Data & State
    public $taskCategories = [], $supervisors = [], $divisions = [];
    public $showDivisionField = false;
    public $resident;

    public function mount()
    {
        $this->resident = Auth::user()->resident()->with('currentStage')->first();
        $this->taskCategories = TaskCategory::orderBy('name')->get();
        $this->supervisors = User::role('Dosen')->orderBy('name')->get();
        $this->divisions = Division::orderBy('name')->get();

        if ($this->resident && $this->resident->currentStage && $this->resident->currentStage->name === 'Tahap II') {
            $this->showDivisionField = true;
        }

        if (request()->has('task_category_id')) {
            $this->task_category_id = request('task_category_id');
        }
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
            'file' => 'required|file|mimes:pdf|max:10240',
            'division_id' => $this->showDivisionField ? 'required|exists:divisions,id' : 'nullable',
            'grade' => 'nullable|numeric|min:0|max:100',
            'description' => 'nullable|string',
            'seminar_title' => 'nullable|string|max:255',
            'presentation_file' => 'nullable|file|max:10240',
            'grade_file' => 'nullable|file|max:5120',
            'attendance_file' => 'nullable|file|max:5120',
        ], [
            'title.required' => 'Judul ilmiah wajib diisi.',
            'task_category_id.required' => 'Kategori tugas wajib dipilih.',
            'supervisor_id.required' => 'Dosen pembimbing wajib dipilih.',
            'presentation_date.required' => 'Tanggal sidang wajib diisi.',
            'file.required' => 'File ilmiah utama wajib diunggah.',
            'file.mimes' => 'File utama harus berformat PDF.',
            'file.max' => 'Ukuran file utama maksimal 10MB.',
            'division_id.required' => 'Divisi wajib dipilih untuk tugas Tahap II.',
            'presentation_file.max' => 'Ukuran file presentasi maksimal 10MB.',
            'grade_file.max' => 'Ukuran file nilai maksimal 5MB.',
            'attendance_file.max' => 'Ukuran file presensi maksimal 5MB.',
        ]);

        $dataToSave = [
            'resident_id' => $this->resident->id,
            'stage_id' => $this->resident->current_stage_id,
            'title' => $this->title,
            'description' => $this->description,
            'seminar_title' => $this->seminar_title,
            'task_category_id' => $this->task_category_id,
            'division_id' => $this->showDivisionField ? $this->division_id : null,
            'supervisor_id' => $this->supervisor_id,
            'presentation_date' => $this->presentation_date,
            'grade' => $this->grade,
            'status' => 'pending',
        ];

        $dataToSave['file_path'] = $this->file->store('submissions', 'public');

        if ($this->presentation_file) {
            $dataToSave['presentation_file_path'] = $this->presentation_file->store('presentations', 'public');
        }
        if ($this->grade_file) {
            $dataToSave['grade_file_path'] = $this->grade_file->store('grades', 'public');
        }
        if ($this->attendance_file) {
            $dataToSave['attendance_file_path'] = $this->attendance_file->store('attendances', 'public');
        }

        Submission::create($dataToSave);

        session()->flash('success', 'Tugas ilmiah berhasil diunggah.');
        return redirect()->route('submissions.history');
    }

    public function render()
    {
        return view('livewire.submission.create-form');
    }
}
