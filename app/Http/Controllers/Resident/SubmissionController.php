<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Submission; 
use Illuminate\Support\Facades\Auth; 
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use App\Models\Division;
use App\Models\Stage;
use App\Models\TaskCategory;

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

            $query = Submission::where('resident_id', $residentId)
                // Eager load relasi baru
                ->with(['taskCategory', 'supervisor', 'stage', 'division']);

             // === BAGIAN YANG DIPERBARUI UNTUK FILTER ===
            if ($categoryId = $request->get('category_id')) {
                $query->where('task_category_id', $categoryId);
            }
            if ($divisionId = $request->get('division_id')) {
                $query->where('division_id', $divisionId);
            }
            if ($stageId = $request->get('stage_id')) {
                $query->where('stage_id', $stageId);
            }

            return DataTables::of($query)
                ->addIndexColumn() // Menambahkan kolom nomor urut
                ->addColumn('task_category', function ($row) {
                    return $row->taskCategory->name ?? '-';
                })
                ->addColumn('supervisor', function ($row) {
                    return $row->supervisor->name ?? '-';
                })
                ->addColumn('stage', fn ($row) => $row->stage->name ?? '-')
                // Menambahkan pengecekan apakah relasi division ada sebelum mengambil nama
                ->addColumn('division', function ($row) {
                    return $row->division ? $row->division->name : '-';
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

       // Ambil data untuk dropdown filter dan kirim ke view
        $taskCategories = TaskCategory::orderBy('name')->get();
        $divisions = Division::orderBy('name')->get();
        $stages = Stage::orderBy('order')->get();

        return view('submissions.history', compact('taskCategories', 'divisions', 'stages'));
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
