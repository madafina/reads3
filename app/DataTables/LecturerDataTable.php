<?php

namespace App\DataTables;

use App\Models\User; // Kita akan query dari model User
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class LecturerDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                // Mengarahkan ke route 'admin.lecturers.show' dengan parameter ID user
                return '<a href="' . route('admin.lecturers.show', $row->id) . '" class="btn btn-info btn-sm">Lihat Detail</a>';
            })
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(User $model): QueryBuilder
    {
        // Query hanya user yang memiliki role 'Dosen'
        return $model->newQuery()->role('Dosen');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('lecturer-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1) // Urutkan berdasarkan nama
            ->selectStyleSingle();
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex')->title('No')->width(30),
            Column::make('name')->title('Nama Dosen'),
            Column::make('email')->title('Email'),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(120)
                ->addClass('text-center'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Lecturer_' . date('YmdHis');
    }
}
