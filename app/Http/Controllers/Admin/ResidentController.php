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
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ResidentController extends Controller
{
    public function index(ResidentDataTable $dataTable)
    {
        // Ambil data semua tahap
        $stages = Stage::orderBy('order')->get();
        
        // Kirim data 'stages' ke view
        return $dataTable->render('admin.residents.index', compact('stages'));
    }

    /**
     * Menampilkan detail seorang residen.
     */
    public function show(Resident $resident)
    {
        // Eager load relasi untuk ditampilkan di view
        $resident->load('user', 'currentStage', 'supervisorHistory');
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
                ->addColumn('file', fn($row) => '<a href="'. route('admin.submissions.show', $row->id) .'" class="btn btn-info btn-sm">Lihat</a>')
                ->rawColumns(['status', 'file'])
                ->make(true);
        }
    }
    /**
     * Menampilkan form untuk mengedit data residen.
     */
    public function edit(Resident $resident)
    {
        $stages = Stage::orderBy('order')->get();
        $lecturers = User::role('Dosen')->orderBy('name')->get();
        
        // Eager load relasi untuk efisiensi
        $resident->load('supervisorHistory');

        // Ambil ID pembimbing yang aktif saat ini
        $currentSupervisorId = $resident->currentSupervisor()->first()->id ?? null;

        return view('admin.residents.edit', compact('resident', 'stages', 'lecturers', 'currentSupervisorId'));
    }

    public function update(Request $request, Resident $resident)
    {
        $user = $resident->user;

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'nim' => 'required|string|max:255|unique:residents,nim,' . $resident->id,
            'current_stage_id' => 'required|exists:stages,id',
            'supervisor_id' => 'nullable|exists:users,id', // ID pembimbing yang baru
        ]);

        DB::transaction(function () use ($request, $resident, $user) {
            // Update data dasar user dan residen
            $user->update($request->only('name', 'email'));
            $resident->update($request->only('nim', 'current_stage_id'));

            // Logika untuk mengganti pembimbing
            $newSupervisorId = $request->input('supervisor_id');
            $currentSupervisor = $resident->currentSupervisor()->first();

            // Cek jika pembimbing diubah
            if ($newSupervisorId != ($currentSupervisor->id ?? null)) {
                // 1. Nonaktifkan pembimbing lama (jika ada)
                if ($currentSupervisor) {
                    $resident->supervisorHistory()->updateExistingPivot($currentSupervisor->id, [
                        'end_date' => now(),
                        'status' => 'inactive',
                    ]);
                }

                // 2. Tambahkan pembimbing baru (jika dipilih)
                if ($newSupervisorId) {
                    $resident->supervisorHistory()->attach($newSupervisorId, [
                        'start_date' => now(),
                        'status' => 'active',
                    ]);
                }
            }
        });

        return redirect()->route('admin.residents.index')->with('success', 'Data residen berhasil diperbarui.');
    }
}