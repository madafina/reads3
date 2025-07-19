<?php

namespace App\Http\Controllers\Resident;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\OtherSubmissionDataTable; 
use App\Models\TaskCategory; 

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
        // 2. Ambil data semua kategori tugas
        $taskCategories = TaskCategory::orderBy('name')->get();

        // 3. Kirim data 'taskCategories' ke view
        return $dataTable->render('resident.browse', compact('taskCategories'));
    }
}