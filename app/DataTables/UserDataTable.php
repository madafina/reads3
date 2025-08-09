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
            ->addColumn('action', function($row) {
                if ($row->id === auth()->id()) {
                    return 'Tidak ada aksi';
                }
                $resetBtn = '<form action="'.route('admin.users.reset-password', $row->id).'" method="POST" class="d-inline mr-1">'.csrf_field().method_field("POST").'<button type="submit" class="btn btn-warning btn-sm" onclick="return confirm(\'Reset password pengguna ini menjadi `123456`?\')">Reset Pass</button></form>';
                $deleteForm = '<form action="'.route('admin.users.destroy', $row->id).'" method="POST" class="d-inline">'.csrf_field().method_field("DELETE").'<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Anda yakin ingin menghapus pengguna ini?\')">Hapus</button></form>';
                return $resetBtn . $deleteForm;
            })
            ->rawColumns(['action']);
    }

    public function query(User $model): QueryBuilder
    {
        $query = $model->newQuery()->with('roles');

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
