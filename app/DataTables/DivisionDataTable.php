<?php

namespace App\DataTables;

use App\Models\Division;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class DivisionDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('staff_count', function($row) {
                return $row->staff()->count() . ' Dosen';
            })
            ->addColumn('action', function($row) {
                // Tombol untuk mengelola staf
                $manageStaffBtn = '<a href="'.route('admin.divisions.staff', $row->id).'" class="btn btn-info btn-sm">Kelola Staf</a>';
                
                // Tombol untuk edit
                $editBtn = '<a href="'.route('admin.divisions.edit', $row->id).'" class="btn btn-warning btn-sm">Edit</a>';
                
                // === BAGIAN YANG DIPERBAIKI ===
                // Form hapus yang lengkap
                $deleteForm = '
                    <form action="'.route('admin.divisions.destroy', $row->id).'" method="POST" class="d-inline">
                        '.csrf_field().'
                        '.method_field("DELETE").'
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Yakin hapus divisi ini?\')">Hapus</button>
                    </form>
                ';
                
                return $manageStaffBtn . ' ' . $editBtn;
            })
            ->rawColumns(['action']) // Pastikan action di-render sebagai HTML
            ->setRowId('id');
    }

    public function query(Division $model): QueryBuilder
    {
        return $model->newQuery()->withCount('staff'); // Gunakan withCount untuk efisiensi
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('division-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->orderBy(1)
                    ->selectStyleSingle();
    }

    public function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex')->title('No')->width(30),
            Column::make('name')->title('Nama Divisi'),
            Column::make('staff_count')->title('Jumlah Staf')->searchable(false),
            Column::computed('action')
                  ->exportable(false)
                  ->printable(false)
                  ->width(250) // Perlebar kolom aksi
                  ->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'Division_' . date('YmdHis');
    }
}