<?php

namespace App\DataTables;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class TrashedUserDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('role', fn($row) => $row->getRoleNames()->implode(', '))
            ->addColumn('deleted_at', fn($row) => $row->deleted_at->translatedFormat('d F Y H:i'))
            ->addColumn('action', function($row) {
                $restoreForm = '
                    <form action="'.route('admin.users.restore', $row->id).'" method="POST" class="d-inline mr-1">
                        '.csrf_field().'
                        '.method_field("PUT").'
                        <button type="submit" class="btn btn-success btn-sm" onclick="return confirm(\'Anda yakin ingin memulihkan pengguna ini?\')">
                            Pulihkan
                        </button>
                    </form>
                ';
                $forceDeleteForm = '
                    <form action="'.route('admin.users.force-delete', $row->id).'" method="POST" class="d-inline">
                        '.csrf_field().'
                        '.method_field("DELETE").'
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'PERINGATAN: Aksi ini tidak dapat diurungkan! Anda yakin ingin menghapus permanen pengguna ini?\')">
                            Hapus Permanen
                        </button>
                    </form>
                ';
                return $restoreForm . $forceDeleteForm;
            })
            ->rawColumns(['action']);
    }

    public function query(User $model): QueryBuilder
    {
        // Mengambil HANYA pengguna yang sudah di-soft delete
        return $model->onlyTrashed()->newQuery()->with('roles');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('trasheduser-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->orderBy(4, 'desc'); // Urutkan berdasarkan tanggal dihapus
    }

    public function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex')->title('No'),
            Column::make('name'),
            Column::make('email'),
            Column::make('role')->title('Peran'),
            Column::make('deleted_at')->title('Tanggal Dihapus'),
            Column::computed('action')->addClass('text-center')->width(250),
        ];
    }

    protected function filename(): string
    {
        return 'TrashedUser_' . date('YmdHis');
    }
}
