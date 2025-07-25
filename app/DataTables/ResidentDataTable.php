<?php

namespace App\DataTables;

use App\Models\Resident;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Carbon\Carbon;

class ResidentDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('name', fn($row) => $row->user->name ?? 'N/A')
            ->addColumn('email', fn($row) => $row->user->email ?? 'N/A')
            ->addColumn('current_stage', fn($row) => $row->currentStage->name ?? 'Belum Diatur')
            ->editColumn('start_date', fn($row) => Carbon::parse($row->start_date)->translatedFormat('d F Y'))
           ->addColumn('action', function($row){
                $detailBtn = '<a href="'.route('admin.residents.show', $row->id).'" class="btn btn-info btn-sm">Detail</a>';
                $editBtn = '<a href="'.route('admin.residents.edit', $row->id).'" class="btn btn-warning btn-sm ml-1">Edit</a>';
                return $detailBtn . ' ' . $editBtn;
            })
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Resident $model): QueryBuilder
    {
        $query = $model->newQuery()->with(['user', 'currentStage']);

        // TAMBAHKAN BLOK IF INI UNTUK MENERAPKAN FILTER
        if ($stageId = $this->request()->get('stage_id')) {
            $query->where('current_stage_id', $stageId);
        }

        return $query;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('resident-table')
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
            Column::make('name')->title('Nama Residen'),
            Column::make('nim')->title('NIM'),
            Column::make('current_stage')->title('Tahap Saat Ini'),
            Column::make('batch')->title('Angkatan'),
            Column::make('start_date')->title('Tanggal Masuk'),
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
        return 'Resident_' . date('YmdHis');
    }
}
