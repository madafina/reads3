<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\UserDataTable;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Menampilkan daftar semua pengguna.
     */
    public function index(UserDataTable $dataTable)
    {
        return $dataTable->render('admin.users.index');
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
            'password' => Hash::make('password')
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Password untuk pengguna ' . $user->name . ' berhasil direset menjadi "password".');
    }
}
