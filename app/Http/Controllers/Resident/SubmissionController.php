<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Submission; 
use Illuminate\Support\Facades\Auth; 
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class SubmissionController extends Controller
{
     public function create()
    {
        // Method ini hanya bertugas menampilkan view Blade
        return view('submissions.create');
    }

    public function history(Request $request)
    {
        // Cek jika ini adalah request data untuk DataTables
        if ($request->ajax()) {
            // Ambil ID residen dari user yang login
            $residentId = Auth::user()->resident->id;

            // Query hanya submission milik residen tersebut
            $query = Submission::where('resident_id', $residentId)
                ->with(['taskCategory', 'supervisor']) // Eager loading untuk performa
                ->select('submissions.*');

            return DataTables::of($query)
                ->addIndexColumn() // Menambahkan kolom nomor urut
                ->addColumn('task_category', function ($row) {
                    return $row->taskCategory->name ?? '-';
                })
                ->addColumn('supervisor', function ($row) {
                    return $row->supervisor->name ?? '-';
                })
                ->addColumn('status', function ($row) {
                    if ($row->status == 'verified') {
                        return '<span class="badge badge-success">Terverifikasi</span>';
                    } elseif ($row->status == 'rejected') {
                        return '<span class="badge badge-danger">Ditolak</span>';
                    } else {
                        return '<span class="badge badge-warning">Pending</span>';
                    }
                })
                ->addColumn('action', function($row){
                    $viewBtn = '<a href="'. asset('storage/' . $row->file_path) .'" target="_blank" class="btn btn-primary btn-sm">Lihat File</a>';
                    
                    // Tombol edit hanya muncul jika status 'pending'
                    $editBtn = '';
                    if ($row->status == 'pending') {
                        $editBtn = '<a href="'. route('submissions.edit', $row->id) .'" class="btn btn-warning btn-sm ml-1">Edit</a>';
                    }

                    return $viewBtn . $editBtn;
                })
                 // 2. TAMBAHKAN BLOK INI UNTUK MEMFORMAT TANGGAL
                ->editColumn('presentation_date', function ($row) {
                    return Carbon::parse($row->presentation_date)->translatedFormat('d F Y');
                })
                ->rawColumns(['action', 'status']) // Render HTML di kolom action dan status
                ->make(true);
        }

        // Jika bukan request AJAX, tampilkan view-nya saja
        return view('submissions.history');
    }

    public function edit(Submission $submission)
    {
        // Pastikan residen hanya bisa mengedit tugasnya sendiri
        if ($submission->resident_id !== Auth::user()->resident->id) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit tugas ini.');
        }

        // Pastikan hanya tugas pending yang bisa diedit
        if ($submission->status !== 'pending') {
            return redirect()->route('submissions.history')->with('error', 'Tugas yang sudah diverifikasi atau ditolak tidak dapat diedit.');
        }

        return view('submissions.edit', compact('submission'));
        // return view('submissions.edit', ['submissionId' => $submission->id]);
    }
}
