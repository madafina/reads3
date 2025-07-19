<?php

namespace App\Http\Controllers\Resident;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\OtherSubmissionDataTable; 
use App\Models\TaskCategory; 
use App\Models\Division;
use App\Models\Stage;

class ResidentController extends Controller
{
    /**
     * Menampilkan halaman rekapitulasi kewajiban.
     */
    public function summary()
    {
        // Controller ini hanya bertugas menampilkan view Blade utama.
        // Semua logika data akan ditangani oleh komponen Livewire.
        return view('resident.summary');
    }

    /**
     * Menampilkan halaman untuk melihat tugas ilmiah residen lain.
     */
    public function browse(OtherSubmissionDataTable $dataTable)
    {
        // Ambil data untuk dropdown filter
        $taskCategories = TaskCategory::orderBy('name')->get();
        $stages = Stage::orderBy('order')->get();
        $divisions = Division::orderBy('name')->get();

        // Kirim semua data filter ke view
        return $dataTable->render('resident.browse', compact('taskCategories', 'stages', 'divisions'));
    }
}