<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UsersExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // Mengambil semua user beserta relasi rolenya
        return User::with('roles')->get();
    }

    /**
     * Mendefinisikan header untuk kolom di file Excel.
     */
    public function headings(): array
    {
        return [
            'ID',
            'Nama',
            'Email',
            'Peran',
        ];
    }

    /**
     * Memetakan data dari setiap user ke kolom yang sesuai.
     *
     * @param User $user
     */
    public function map($user): array
    {
        return [
            $user->id,
            $user->name,
            $user->email,
            $user->getRoleNames()->implode(', '), // Mengambil nama peran
        ];
    }
}
