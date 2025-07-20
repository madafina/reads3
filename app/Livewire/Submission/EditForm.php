<?php

namespace App\Livewire\Submission;

use App\Models\Division;
use App\Models\TaskCategory;
use App\Models\User;
use App\Models\Submission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class EditForm extends Component
{
    use WithFileUploads;

    public Submission $submission;

    // Properti untuk binding form
    public $title;
    public $task_category_id;
    public $division_id;
    public $supervisor_id;
    public $presentation_date;
    public $grade;
    public $newFile;

    // Properti baru
    public $description;
    public $seminar_title;
    public $new_presentation_file;
    public $new_grade_file;
    public $new_attendance_file;

    // Data untuk dropdown
    public $taskCategories = [];
    public $supervisors = [];
    public $divisions = [];
    public $showDivisionField = false;

    public function mount($submissionId)
    {
        $submission = Submission::findOrFail($submissionId);
        $this->submission = $submission;

        // Isi properti form dengan data yang ada
        $this->title = $submission->title;
        $this->task_category_id = $submission->task_category_id;
        $this->division_id = $submission->division_id;
        $this->supervisor_id = $submission->supervisor_id;
        $this->presentation_date = $submission->presentation_date->format('Y-m-d');
        $this->grade = $submission->grade;
        $this->description = $submission->description;
        $this->seminar_title = $submission->seminar_title;

        // Ambil data untuk dropdown
        $this->taskCategories = TaskCategory::orderBy('name')->get();
        $this->supervisors = User::role('Dosen')->orderBy('name')->get();
        $this->divisions = Division::orderBy('name')->get();

        // Cek apakah residen berada di Tahap II
        $resident = Auth::user()->resident()->with('currentStage')->first();
        if ($resident && $resident->currentStage && $resident->currentStage->name === 'Tahap II') {
            $this->showDivisionField = true;
        }
    }

    public function update()
    {
        // 1. Validasi semua input dan simpan hasilnya
        $validatedData = $this->validate([
            'title' => 'required|string|max:255',
            'task_category_id' => 'required|exists:task_categories,id',
            'supervisor_id' => 'required|exists:users,id',
            'presentation_date' => 'required|date',
            'division_id' => $this->showDivisionField ? 'required|exists:divisions,id' : 'nullable',
            'grade' => 'nullable|numeric|min:0|max:100',
            'description' => 'nullable|string',
            'seminar_title' => 'nullable|string|max:255',
            'newFile' => 'nullable|file|mimes:pdf|max:10240',
            'new_presentation_file' => 'nullable|file|max:10240',
            'new_grade_file' => 'nullable|file|max:5120',
            'new_attendance_file' => 'nullable|file|max:5120',
        ], [
            'title.required' => 'Judul ilmiah wajib diisi.',
            'task_category_id.required' => 'Kategori tugas wajib dipilih.',
            'supervisor_id.required' => 'Dosen pembimbing wajib dipilih.',
            'presentation_date.required' => 'Tanggal sidang wajib diisi.',
            'division_id.required' => 'Divisi wajib dipilih untuk tugas Tahap II.',
            'newFile.mimes' => 'File utama harus berformat PDF.',
            'newFile.max' => 'Ukuran file utama maksimal 10MB.',
            'new_presentation_file.max' => 'Ukuran file presentasi maksimal 10MB.',
            'new_grade_file.max' => 'Ukuran file nilai maksimal 5MB.',
            'new_attendance_file.max' => 'Ukuran file presensi maksimal 5MB.',
        ]);
        
        // 2. Hapus properti file dari array agar tidak error saat update model
        unset($validatedData['newFile'], $validatedData['new_presentation_file'], $validatedData['new_grade_file'], $validatedData['new_attendance_file']);

        // 3. Proses file-file baru jika ada
        if ($this->newFile) {
            Storage::disk('public')->delete($this->submission->file_path);
            $validatedData['file_path'] = $this->newFile->store('submissions', 'public');
        }
        if ($this->new_presentation_file) {
            Storage::disk('public')->delete($this->submission->presentation_file_path);
            $validatedData['presentation_file_path'] = $this->new_presentation_file->store('presentations', 'public');
        }
        if ($this->new_grade_file) {
            Storage::disk('public')->delete($this->submission->grade_file_path);
            $validatedData['grade_file_path'] = $this->new_grade_file->store('grades', 'public');
        }
        if ($this->new_attendance_file) {
            Storage::disk('public')->delete($this->submission->attendance_file_path);
            $validatedData['attendance_file_path'] = $this->new_attendance_file->store('attendances', 'public');
        }

        // 4. Update data di database
        $this->submission->update($validatedData);

        session()->flash('success', 'Tugas ilmiah berhasil diperbarui.');
        return redirect()->route('submissions.history');
    }

    public function render()
    {
        return view('livewire.submission.edit-form');
    }
}
