<?php

namespace App\Imports;

use App\Models\Resident;
use App\Models\Stage;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ResidentsImport implements ToModel, WithHeadingRow, WithValidation
{
    private $stages;

    public function __construct()
    {
        // Ambil dan cache data tahap untuk efisiensi
        $this->stages = Stage::all()->pluck('id', 'name');
    }

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

        // 2. Tetapkan role sebagai 'Residen'
        $user->assignRole('Residen');

        // 3. Buat profil Resident yang terhubung
        return new Resident([
            'user_id' => $user->id,
            'nim' => $row['nim'],
            'batch' => $row['angkatan'],
            'start_date' => now(),
            // Cari ID tahap berdasarkan nama tahap dari file Excel
            'current_stage_id' => $this->stages->get($row['tahap']),
        ]);
    }

    public function rules(): array
    {
        return [
            'nama' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'nim' => 'required|unique:residents,nim',
            'angkatan' => 'required|numeric',
            'tahap' => 'required|in:' . $this->stages->keys()->implode(','),
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nama.required' => 'Kolom nama wajib diisi.',
            'email.required' => 'Kolom email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email :input sudah terdaftar.',
            'nim.required' => 'Kolom NIM wajib diisi.',
            'nim.unique' => 'NIM :input sudah terdaftar.',
            'angkatan.required' => 'Kolom angkatan wajib diisi.',
            'tahap.required' => 'Kolom tahap wajib diisi.',
            'tahap.in' => 'Nama tahap :input tidak valid. Pilihan yang valid: ' . $this->stages->keys()->implode(', '),
        ];
    }
}
