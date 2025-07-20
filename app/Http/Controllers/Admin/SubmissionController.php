<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\SubmissionDataTable; 
use App\Http\Controllers\Controller;
use App\Models\Submission; 
use Illuminate\Support\Facades\Auth; 
use App\DataTables\AllSubmissionDataTable;  
use App\Models\Stage;
use App\Models\Division; 

class SubmissionController extends Controller
{
    public function index(SubmissionDataTable $dataTable)
    {
        return $dataTable->render('admin.submissions.index');
    }

    public function verify(Submission $submission)
    {
        $submission->update([
            'status' => 'verified',
            'verified_by' => Auth::id(),
            'verified_at' => now(),
        ]);

        return redirect()->route('admin.submissions.verify.index')->with('success', 'Ilmiah berhasil diverifikasi.');
    }

    public function reject(Submission $submission)
    {
        $submission->update([
            'status' => 'rejected',
            'verified_by' => Auth::id(),
            'verified_at' => now(),
        ]);

        return redirect()->route('admin.submissions.verify.index')->with('success', 'Ilmiah telah ditolak.');
    }

    public function all(AllSubmissionDataTable $dataTable)
    {
        // Ambil data untuk dropdown filter
        $stages = Stage::orderBy('order')->get();
        $divisions = Division::orderBy('name')->get(); // Ambil data divisi

        // Kirim semua data filter ke view
        return $dataTable->render('admin.submissions.all', compact('stages', 'divisions'));
    }

    public function show(Submission $submission)
    {
        $user = Auth::user();
        $isViewerOnly = false; // Default: pengguna memiliki akses penuh

        // Cek jika pengguna adalah Residen
        if ($user->hasRole('Residen')) {
            // Jika BUKAN pemilik submission
            if ($submission->resident_id !== $user->resident->id) {
                // Maka, tandai sebagai "hanya lihat"
                $isViewerOnly = true;
            }
        }

        // Semua orang bisa mengakses view, tetapi dengan data yang berbeda
        return view('admin.submissions.show', compact('submission', 'isViewerOnly'));
    }
}