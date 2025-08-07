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
use App\Imports\ResidentsImport; // Import kelas baru
use Maatwebsite\Excel\Facades\Excel; // Import facade Excel
use Maatwebsite\Excel\Validators\ValidationException;

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
  
    public function edit(Resident $resident)
    {
        $stages = Stage::orderBy('order')->get();
        $lecturers = User::role('Dosen')->orderBy('name')->get();
        $resident->load('supervisorHistory');
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
        ]);

        $user->update($request->only('name', 'email'));
        $resident->update($request->only('nim', 'current_stage_id'));

        return redirect()->back()->with('success', 'Profil dasar residen berhasil diperbarui.');
    }


    /**
     * Memperbarui dosen pembimbing residen.
     */
    public function updateSupervisor(Request $request, Resident $resident)
    {
        $request->validate([
            'supervisor_id' => 'nullable|exists:users,id',
            'reason' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $resident) {
            $newSupervisorId = $request->input('supervisor_id');
            $currentSupervisor = $resident->currentSupervisor()->first();

            if ($newSupervisorId != ($currentSupervisor->id ?? null)) {
                if ($currentSupervisor) {
                    $resident->supervisorHistory()->updateExistingPivot($currentSupervisor->id, [
                        'end_date' => now(),
                        'status' => 'inactive',
                    ]);
                }
                if ($newSupervisorId) {
                    $resident->supervisorHistory()->attach($newSupervisorId, [
                        'start_date' => now(),
                        'status' => 'active',
                        'reason' => $request->reason,
                    ]);
                }
            }
        });

        return redirect()->back()->with('success', 'Dosen pembimbing berhasil diperbarui.');
    }

    public function showImportForm()
    {
        return view('admin.residents.import');
    }

    /**
     * Memproses file Excel yang diunggah.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        try {
            Excel::import(new ResidentsImport, $request->file('file'));
        } catch (ValidationException $e) {
            // Tangkap error validasi dari file Excel dan kirim kembali ke view
            $failures = $e->failures();
            return redirect()->route('admin.residents.import.form')->with('import_errors', $failures);
        }

        return redirect()->route('admin.residents.index')->with('success', 'Data residen berhasil diimpor.');
    }
}