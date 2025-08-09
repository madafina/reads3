<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\UserDataTable;
use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role; 
use Illuminate\Support\Facades\Hash;
use App\Exports\UsersExport; 
use Maatwebsite\Excel\Facades\Excel;

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
}
