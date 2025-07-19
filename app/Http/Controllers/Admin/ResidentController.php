<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\ResidentDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Resident; 
use App\Models\Submission; 
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use App\Models\Stage; 

class ResidentController extends Controller
{
    public function index(ResidentDataTable $dataTable)
    {
        // Ambil data semua tahap
        $stages = Stage::orderBy('order')->get();
        
        // Kirim data 'stages' ke view
        return $dataTable->render('admin.residents.index', compact('stages'));
    }

    // METHOD 1: UNTUK MENAMPILKAN HALAMAN UTAMA
    public function show(Resident $resident)
    {
        // Method ini hanya mengirim data residen ke view
        return view('admin.residents.show', compact('resident'));
    }

    // METHOD 2: UNTUK MENYEDIAKAN DATA TABEL SUBMISSION
    public function submissions(Request $request, Resident $resident)
    {
        if ($request->ajax()) {
            $query = Submission::where('resident_id', $resident->id)
                ->with(['taskCategory']); // Eager load

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('presentation_date', fn($row) => Carbon::parse($row->presentation_date)->translatedFormat('d F Y'))
                ->addColumn('task_category_name', fn($row) => $row->taskCategory->name ?? 'N/A')
                ->addColumn('status', function ($row) {
                    $badges = [
                        'verified' => 'success',
                        'rejected' => 'danger',
                        'pending' => 'warning',
                    ];
                    return '<span class="badge badge-'.($badges[$row->status] ?? 'secondary').'">'.ucfirst($row->status).'</span>';
                })
                ->addColumn('file', fn($row) => '<a href="'. asset('storage/' . $row->file_path) .'" target="_blank" class="btn btn-secondary btn-sm">Lihat</a>')
                ->rawColumns(['status', 'file'])
                ->make(true);
        }
    }
}