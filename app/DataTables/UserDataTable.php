<?php

namespace App\DataTables;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class UserDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('role', fn($row) => $row->getRoleNames()->implode(', '))
            // === KOLOM BARU DITAMBAHKAN DI SINI ===
            // ->addColumn('has_stage_history', function($row) {
            //     // Hanya periksa jika pengguna adalah Residen
            //     if ($row->hasRole('Residen')) {
            //         // Cek apakah relasi resident ada dan apakah ada data di stageHistory
            //         if ($row->resident && $row->resident->stageHistory()->exists()) {
            //             return '<span class="badge badge-success">Ada</span>';
            //         }
            //         return '<span class="badge badge-danger">Tidak Ada</span>';
            //     }
            //     // Tampilkan strip untuk peran lain
            //     return '-';
            // })

             // === KOLOM YANG DIPERBARUI LOGIKANYA ===
            ->addColumn('has_submissions', function($row) {
                // Hanya periksa jika pengguna adalah Residen dan memiliki profil
                if ($row->hasRole('Residen') && $row->resident) {
                    // Cek apakah ada data di relasi submissions
                    if ($row->resident->submissions()->exists()) {
                        return '<span class="badge badge-success">Ada</span>';
                    }
                    return '<span class="badge badge-danger">Tidak Ada</span>';
                }
                return '-'; // Tampilkan strip untuk peran lain
            })

            ->addColumn('action', function($row) {
                if ($row->id === auth()->id()) {
                    return 'Tidak ada aksi';
                }
                $actions = '';

                // === TOMBOL DETAIL BARU ===
                // Tambahkan tombol detail yang mengarah ke profil yang sesuai
                if ($row->hasRole('Residen') && $row->resident) {
                    $actions .= '<a href="'.route('admin.residents.show', $row->resident->id).'" class="btn btn-info btn-sm">Detail</a>';
                } elseif ($row->hasRole('Dosen')) {
                    // Menggunakan ID user untuk route detail dosen
                    $actions .= '<a href="'.route('admin.lecturers.show', $row->id).'" class="btn btn-info btn-sm">Detail</a>';
                }

                $actions .= '<form action="'.route('admin.users.reset-password', $row->id).'" method="POST" class="d-inline mr-1">'.csrf_field().method_field("POST").'<button type="submit" class="btn btn-warning btn-sm" onclick="return confirm(\'Reset password pengguna ini menjadi `123456`?\')">Reset Pass</button></form>';
                $actions .= '<form action="'.route('admin.users.destroy', $row->id).'" method="POST" class="d-inline">'.csrf_field().method_field("DELETE").'<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Anda yakin ingin menghapus pengguna ini?\')">Hapus</button></form>';
                
                // === TOMBOL BARU DI SINI ===
                // Tampilkan tombol "Tambahkan Tahap" jika dia residen & belum punya riwayat
                // if ($row->hasRole('Residen')) {
                //     if ($row->resident && $row->resident->stageHistory()->exists()) {
                        
                //     } else {
                //         $actions .= '<form action="'.route('admin.users.add-stage', $row->id).'" method="POST" class="d-inline">'.csrf_field().'<button type="submit" class="btn btn-primary btn-sm">Tambahkan Tahap</button></form>';
                //     }
                // }
                
                return $actions;
            })
            ->rawColumns(['action', 'has_submissions']);
    }

    public function query(User $model): QueryBuilder
    {
        $query = $model->newQuery()->with(['roles', 'resident.submissions', 'resident.stageHistory']);

        // === BAGIAN YANG DIPERBARUI ===
        // Terapkan filter jika ada input dari request
        if ($role = $this->request()->get('role')) {
            $query->role($role);
        }

        return $query;
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('user-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1)
            ->processing(true)     
            ->serverSide(true) ;
    }

    public function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex')->title('No'),
            Column::make('name'),
            Column::make('email'),
            Column::make('role')->title('Peran')->orderable(false)->searchable(false),
            // === KOLOM BARU DITAMBAHKAN DI SINI ===
            // Column::computed('has_stage_history')->title('Riwayat Tahap?')->orderable(false)->searchable(false),
             Column::computed('has_submissions')->title('Punya Ilmiah?')->orderable(false)->searchable(false),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(160)
                ->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'User_' . date('YmdHis');
    }
}
