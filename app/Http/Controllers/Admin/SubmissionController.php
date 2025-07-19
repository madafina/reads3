<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\SubmissionDataTable; 
use App\Http\Controllers\Controller;
use App\Models\Submission; 
use Illuminate\Support\Facades\Auth; 
use App\DataTables\AllSubmissionDataTable;  
use App\Models\Stage;

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
        $stages = Stage::orderBy('order')->get();
        // Kirim data 'stages' ke view agar bisa digunakan untuk filter
        return $dataTable->render('admin.submissions.all', compact('stages'));
    }
}