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
    public $newFile; // Properti untuk file baru

    // Data untuk dropdown
    public $taskCategories = [];
    public $supervisors = [];
    public $divisions = [];
    public $showDivisionField = false;

    // === BAGIAN YANG DIPERBAIKI ===
    // Method mount sekarang menerima ID (integer), bukan objek
    public function mount($submissionId)
    {
        // Cari objek Submission berdasarkan ID yang diterima
        $submission = Submission::findOrFail($submissionId);
        $this->submission = $submission;

        // Isi properti form dengan data yang ada
        $this->title = $submission->title;
        $this->task_category_id = $submission->task_category_id;
        $this->division_id = $submission->division_id;
        $this->supervisor_id = $submission->supervisor_id;
        $this->presentation_date = $submission->presentation_date->format('Y-m-d');
        $this->grade = $submission->grade;

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
        $this->validate([
            'title' => 'required|string|max:255',
            'task_category_id' => 'required|exists:task_categories,id',
            'supervisor_id' => 'required|exists:users,id',
            'presentation_date' => 'required|date',
            'newFile' => 'nullable|file|mimes:pdf|max:10240', // File baru opsional
            'division_id' => $this->showDivisionField ? 'required|exists:divisions,id' : 'nullable',
            'grade' => 'nullable|numeric|min:0|max:100',
        ]);

        $dataToUpdate = [
            'title' => $this->title,
            'task_category_id' => $this->task_category_id,
            'supervisor_id' => $this->supervisor_id,
            'presentation_date' => $this->presentation_date,
            'division_id' => $this->showDivisionField ? $this->division_id : null,
            'grade' => $this->grade,
        ];

        // Jika ada file baru yang diupload
        if ($this->newFile) {
            // Hapus file lama
            Storage::disk('public')->delete($this->submission->file_path);
            // Simpan file baru dan update path
            $dataToUpdate['file_path'] = $this->newFile->store('submissions', 'public');
        }

        $this->submission->update($dataToUpdate);

        session()->flash('success', 'Ilmiah berhasil diperbarui.');
        return redirect()->route('submissions.history');
    }

    public function render()
    {
        return view('livewire.submission.edit-form');
    }
}