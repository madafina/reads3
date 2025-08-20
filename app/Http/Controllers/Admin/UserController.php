<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\TrashedUserDataTable;
use App\DataTables\UserDataTable;
use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role; 
use Illuminate\Support\Facades\Hash;
use App\Exports\UsersExport; 
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use App\Models\Stage;
use App\Models\Resident;

class UserController extends Controller
{
    /**
     * Menampilkan daftar semua pengguna.
     */
    public function index(UserDataTable $dataTable)
    {
        // 2. Ambil semua data peran
        $roles = Role::all();
        
        // 3. Kirim data 'roles' ke view
        return $dataTable->render('admin.users.index', compact('roles'));
    }


    /**
     * Mereset password seorang pengguna.
     */
    public function resetPassword(User $user)
    {
        // Proteksi agar admin tidak mereset passwordnya sendiri
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak dapat mereset password Anda sendiri dari halaman ini.');
        }

        // Update password menjadi 'password' (atau nilai default lain yang Anda inginkan)
        $user->update([
            'password' => Hash::make('123456'), // Ganti dengan nilai default yang diinginkan
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Password untuk pengguna ' . $user->name . ' berhasil direset menjadi "password".');
    }

    public function destroy(User $user)
    {
        // Proteksi agar admin tidak bisa menghapus akunnya sendiri
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete(); // Ini akan melakukan soft delete

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil dihapus.');
    }

    public function exportExcel()
    {
        return Excel::download(new UsersExport, 'daftar-pengguna.xlsx');
    }

   /**
     * METHOD YANG DIPERBARUI: Menambahkan riwayat tahap default untuk residen.
     */
    public function addStage(User $user)
    {
        if (!$user->hasRole('Residen')) {
            return redirect()->back()->with('error', 'Pengguna ini bukan seorang residen.');
        }

        // PERBAIKAN: Cek jika profil residen belum ada, maka buatkan.
        if (!$user->resident) {
            Resident::create([
                'user_id' => $user->id,
                'nim' => 'BELUM-DIATUR-' . $user->id, // NIM sementara yang unik
                'start_date' => $user->created_at ?? now(),
            ]);
            // Refresh relasi user agar bisa menemukan profil yang baru dibuat
            $user->refresh();
        }

        if ($user->resident->stageHistory()->exists()) {
            return redirect()->back()->with('error', 'Residen ini sudah memiliki riwayat tahap.');
        }

        $stage1 = Stage::where('name', 'Tahap I')->first();
        if (!$stage1) {
            return redirect()->back()->with('error', 'Data master "Tahap I" tidak ditemukan.');
        }

        DB::transaction(function () use ($user, $stage1) {
            // Update tahap saat ini di profil residen
            $user->resident->update(['current_stage_id' => $stage1->id]);

            // Buat catatan riwayat baru di tabel pivot
            DB::table('resident_stage')->insert([
                'resident_id' => $user->resident->id,
                'stage_id' => $stage1->id,
                'status' => 'active',
                'start_date' => $user->resident->start_date ?? now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        return redirect()->route('admin.users.index')->with('success', 'Profil residen dibuat dan Tahap I berhasil ditambahkan untuk ' . $user->name);
    }

    /**
     * Menampilkan daftar pengguna yang sudah di-soft delete.
     */
    public function trashed(TrashedUserDataTable $dataTable)
    {
        return $dataTable->render('admin.users.trashed');
    }

    /**
     * Memulihkan pengguna yang sudah di-soft delete.
     */
    public function restore($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();

        return redirect()->route('admin.users.trashed')->with('success', 'Pengguna berhasil dipulihkan.');
    }

    /**
     * Menghapus pengguna secara permanen dari database.
     */
    public function forceDelete($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        // Tambahkan logika untuk menghapus file foto jika ada
        // if ($user->resident && $user->resident->photo) { ... }
        $user->forceDelete();

        return redirect()->route('admin.users.trashed')->with('success', 'Pengguna berhasil dihapus secara permanen.');
    }
    
}
