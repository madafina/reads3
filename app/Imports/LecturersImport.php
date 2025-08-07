<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Lecturer;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class LecturersImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // 1. Buat User baru
        $user = User::create([
            'name'     => $row['nama'],
            'email'    => $row['email'],
            'password' => Hash::make('123456'), // Set password default
        ]);

        // 2. Tetapkan role sebagai 'Dosen'
        $user->assignRole('Dosen');

        // 3. Buat profil Lecturer yang terhubung
        return new Lecturer([
            'user_id' => $user->id,
            'nidn'    => $row['nidn'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'nama' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'nidn' => 'nullable|unique:lecturers,nidn',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nama.required' => 'Kolom nama wajib diisi.',
            'email.required' => 'Kolom email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email :input sudah terdaftar.',
            'nidn.unique' => 'NIDN :input sudah terdaftar.',
        ];
    }
}
