<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\DivisionDataTable;
use App\Http\Controllers\Controller;
use App\Models\Division;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DivisionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(DivisionDataTable $dataTable)
    {
        return $dataTable->render('admin.divisions.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.divisions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:divisions,name',
        ]);

        Division::create($request->all());

        return redirect()->route('admin.divisions.index')->with('success', 'Divisi baru berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     * (Kita tidak gunakan halaman show terpisah untuk divisi)
     */
    public function show(Division $division)
    {
        // Redirect ke halaman kelola staf sebagai halaman detail utama
        return redirect()->route('admin.divisions.staff', $division->id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Division $division)
    {
        return view('admin.divisions.edit', compact('division'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Division $division)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:divisions,name,' . $division->id,
        ]);

        $division->update($request->all());

        return redirect()->route('admin.divisions.index')->with('success', 'Divisi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Division $division)
    {
        // Validasi untuk mencegah penghapusan jika divisi sedang digunakan
        if ($division->requirementRules()->exists()) {
            return redirect()->route('admin.divisions.index')->with('error', 'Divisi tidak bisa dihapus karena sedang digunakan dalam sebuah aturan.');
        }

        if ($division->staff()->exists()) {
            return redirect()->route('admin.divisions.index')->with('error', 'Divisi tidak bisa dihapus karena masih memiliki staf.');
        }

        $division->delete();

        return redirect()->route('admin.divisions.index')->with('success', 'Divisi berhasil dihapus.');
    }

    /**
     * Menampilkan halaman untuk mengelola staf sebuah divisi.
     */
    // public function staff(Division $division)
    // {
    //     // Ambil semua user dengan role 'Dosen'
    //     $allLecturers = User::role('Dosen')->orderBy('name')->get();

    //     // Ambil ID dosen yang sudah menjadi staf di divisi ini
    //     $currentStaffIds = $division->staff->pluck('id')->toArray();

    //     return view('admin.divisions.staff', compact('division', 'allLecturers', 'currentStaffIds'));
    // }

    public function staff(Division $division)
{
    // Ambil semua user dengan role 'Dosen'
    $allLecturers = User::role('Dosen')->orderBy('name')->get();

    // Ambil ID dosen yang sudah menjadi staf di divisi ini
    $currentStaffIds = $division->staff->pluck('id')->toArray();

    // Ambil ID dari user yang menjadi PJ
    $currentPjId = $division->staff()->wherePivot('is_pj', true)->value('id');

    return view('admin.divisions.staff', compact(
        'division',
        'allLecturers',
        'currentStaffIds',
        'currentPjId'
    ));
}


    /**
     * Memperbarui daftar staf di sebuah divisi. (VERSI BARU)
     */
    // app/Http/Controllers/Admin/DivisionController.php

    public function updateStaff(Request $request, Division $division)
    {
        $request->validate([
            'assigned_staff' => 'nullable|array',
            'assigned_staff.*' => 'exists:users,id',
            // Jadikan pj_id wajib jika ada staf yang ditugaskan
            'pj_id' => 'required_with:assigned_staff|nullable|exists:users,id',
        ], [
            // Pesan error kustom
            'pj_id.required_with' => 'Anda harus memilih satu Penanggung Jawab (PJ) jika ada staf yang ditugaskan.'
        ]);

        // ... sisa logika method sama persis ...
        $division->staff()->sync($request->assigned_staff ?? []);

        DB::table('division_staff')
            ->where('division_id', $division->id)
            ->update(['is_pj' => false]);

        if ($request->pj_id) {
            $division->staff()->updateExistingPivot($request->pj_id, [
                'is_pj' => true,
            ]);
        }

        return redirect()->route('admin.divisions.index')->with('success', 'Daftar staf dan PJ untuk divisi ' . $division->name . ' berhasil diperbarui.');
    }
}
