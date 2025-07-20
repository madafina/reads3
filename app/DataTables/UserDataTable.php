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
                // Admin tidak bisa mereset passwordnya sendiri dari halaman ini
                if ($row->id === auth()->id()) {
                    return 'Tidak ada aksi';
                }
                $form = '
                    <form action="'.route('admin.users.reset-password', $row->id).'" method="POST">
                        '.csrf_field().'
                        '.method_field("POST").'
                        <button type="submit" class="btn btn-warning btn-sm" onclick="return confirm(\'Anda yakin ingin mereset password pengguna ini menjadi `password`?\')">
                            Reset Password
                        </button>
                    </form>
                ';
                return $form;
            })
            ->rawColumns(['action']);
    }

    public function query(User $model): QueryBuilder
    {
        return $model->newQuery()->with('roles');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('user-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->orderBy(1);
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
