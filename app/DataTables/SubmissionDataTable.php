<?php

namespace App\DataTables;

use App\Models\Submission;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class SubmissionDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     */
    // app/DataTables/SubmissionDataTable.php

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('resident_name', fn($row) => $row->resident->user->name ?? 'N/A')
            ->addColumn('task_category_name', fn($row) => $row->taskCategory->name ?? 'N/A')
     
            ->filterColumn('resident_name', function($query, $keyword) {
                $query->whereHas('resident.user', function($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            ->addColumn('action', function($row){
                // Mengganti tombol file dengan tombol detail
                return '<a href="'. route('admin.submissions.show', $row->id) .'" class="btn btn-info btn-sm">Lihat Detail</a>';
            })
            ->rawColumns(['action'])
            ->setRowId('id');
    }
    /**
     * Get the query source of dataTable.
     */
    public function query(Submission $model): QueryBuilder
    {
        // Query hanya submission yang statusnya 'pending'
        return $model->newQuery()
            ->where('status', 'pending')
            ->with(['resident.user', 'taskCategory']);
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('submissions-table') // ID tabel
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1)
            ->selectStyleSingle()
            ->buttons([]); // Kita tidak pakai tombol ekspor bawaan
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex')->title('No')->width(30),
            Column::make('resident_name')->title('Nama Residen'),
            Column::make('title')->title('Judul'),
            Column::make('task_category_name')->title('Kategori'),
            // Column::computed('file')->title('File')->width(100),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(150)
                ->addClass('text-center'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Submission_' . date('YmdHis');
    }
}
