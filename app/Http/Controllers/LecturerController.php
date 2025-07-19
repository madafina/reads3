<?php

namespace App\Http\Controllers;

use App\DataTables\AllSubmissionDataTable;
use App\DataTables\LecturerAdviseeDataTable;
use App\Models\Resident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LecturerController extends Controller
{
    /**
     * Menampilkan dashboard utama untuk dosen.
     */
    public function dashboard()
    {
        $user = Auth::user();
        $adviseeCount = Resident::whereHas('submissions', fn($q) => $q->where('supervisor_id', $user->id))->distinct('user_id')->count();
        $divisionCount = $user->divisions()->count();

        return view('lecturer.dashboard', compact('adviseeCount', 'divisionCount'));
    }

    /**
     * Menampilkan daftar mahasiswa bimbingan dosen.
     */
    public function advisees(LecturerAdviseeDataTable $dataTable)
    {
        return $dataTable->render('lecturer.advisees');
    }

    /**
     * Menampilkan semua tugas ilmiah (mirip admin).
     */
    public function submissions(AllSubmissionDataTable $dataTable)
    {
        // Kita bisa menggunakan kembali AllSubmissionDataTable dari admin
        // Atau buat yang baru jika perlu kustomisasi
        return $dataTable->render('lecturer.submissions');
    }

    /**
     * Menampilkan daftar residen per divisi di mana dosen bertugas.
     */
    public function divisions()
    {
        $lecturer = Auth::user();
        // Eager load relasi staff dan resident di tahap II
        $divisions = $lecturer->divisions()->with(['staff', 'residents' => function ($query) {
            $query->whereHas('currentStage', fn($q) => $q->where('name', 'Tahap II'))
                  ->with('user');
        }])->get();

        return view('lecturer.divisions', compact('divisions'));
    }
}
