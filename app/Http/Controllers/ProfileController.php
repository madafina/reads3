<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Menampilkan halaman edit profil.
     */
    public function edit()
    {
        return view('profile.edit', ['user' => Auth::user()]);
    }

    /**
     * Memperbarui informasi profil, foto, atau password.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // Logika untuk update informasi profil
        if ($request->has('update_profile')) {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            ], [
                'name.required' => 'Nama lengkap wajib diisi.',
                'email.required' => 'Alamat email wajib diisi.',
                'email.email' => 'Format alamat email tidak valid.',
                'email.unique' => 'Alamat email ini sudah digunakan oleh pengguna lain.',
            ]);

            $user->update($request->only('name', 'email'));

            if ($user->resident) {
                $request->validate([
                    'nim' => ['required', 'string', 'max:255', 'unique:residents,nim,' . $user->resident->id],
                    'phone_number' => ['nullable', 'string', 'max:15'],
                ], [
                    'nim.required' => 'NIM wajib diisi.',
                    'nim.unique' => 'NIM ini sudah terdaftar.',
                ]);
                $user->resident->update($request->only('nim', 'phone_number'));
            }

            return redirect()->route('profile.edit')->with('success', 'Profil berhasil diperbarui.');
        }

        // Logika untuk update foto profil
        if ($request->has('update_photo')) {
            $request->validate([
                'photo' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            ], [
                'photo.required' => 'Anda harus memilih file foto untuk diunggah.',
                'photo.image' => 'File yang diunggah harus berupa gambar.',
                'photo.mimes' => 'Format foto harus jpeg, png, atau jpg.',
                'photo.max' => 'Ukuran foto maksimal adalah 2MB.',
            ]);

            $path = $request->file('photo')->store('avatars', 'public');

            if ($user->resident && $user->resident->photo) {
                Storage::disk('public')->delete($user->resident->photo);
            }

            if ($user->resident) {
                $user->resident->update(['photo' => $path]);
            }

            return redirect()->route('profile.edit')->with('success', 'Foto profil berhasil diperbarui.');
        }

        // Logika untuk update password
        if ($request->has('update_password')) {
            $request->validate([
                'current_password' => ['required', 'current_password'],
                'password' => ['required', Password::defaults(), 'confirmed'],
            ], [
                'current_password.required' => 'Password saat ini wajib diisi.',
                'current_password.current_password' => 'Password saat ini yang Anda masukkan salah.',
                'password.required' => 'Password baru wajib diisi.',
                'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
            ]);

            $user->update(['password' => Hash::make($request->password)]);

            return redirect()->route('profile.edit')->with('success', 'Password berhasil diperbarui.');
        }

        return redirect()->route('profile.edit');
    }
}
