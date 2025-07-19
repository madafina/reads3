<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\LecturerDataTable;
use App\Http\Controllers\Controller;
use App\Models\User; 
use App\Models\Resident; 
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables; 

class LecturerController extends Controller
{
    public function index(LecturerDataTable $dataTable)
    {
        return $dataTable->render('admin.lecturers.index');
    }

    // METHOD 1: UNTUK MENAMPILKAN HALAMAN UTAMA
    public function show(User $lecturer)
    {
        // Pastikan user yang diakses adalah dosen
        if (!$lecturer->hasRole('Dosen')) {
            abort(404);
        }
        return view('admin.lecturers.show', compact('lecturer'));
    }

    // METHOD 2: UNTUK MENYEDIAKAN DATA TABEL MAHASISWA BIMBINGAN
    public function advisees(Request $request, User $lecturer)
    {
        if ($request->ajax()) {
            // Cari residen yang pernah menjadikan dosen ini sebagai pembimbing
            $query = Resident::whereHas('submissions', function ($q) use ($lecturer) {
                $q->where('supervisor_id', $lecturer->id);
            })->with(['user', 'currentStage'])->distinct();

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('name', fn($row) => $row->user->name ?? 'N/A')
                ->addColumn('current_stage', fn($row) => $row->currentStage->name ?? 'N/A')
                ->addColumn('action', function($row){
                    return '<a href="'.route('admin.residents.show', $row->id).'" class="btn btn-secondary btn-sm">Lihat Profil Residen</a>';
                })
                ->make(true);
        }
    }
}   