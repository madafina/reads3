<?php

namespace App\DataTables;

use App\Models\Resident;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Carbon\Carbon;

class ResidentDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('name', fn($row) => $row->user->name ?? 'N/A')
            ->addColumn('email', fn($row) => $row->user->email ?? 'N/A')
            ->addColumn('current_stage', fn($row) => $row->currentStage->name ?? '<span class="badge badge-danger">Belum Diatur</span>')
            ->editColumn('start_date', fn($row) => $row->start_date ? Carbon::parse($row->start_date)->translatedFormat('d F Y') : '-')
            ->addColumn('action', function($row){
                $detailBtn = '<a href="'.route('admin.residents.show', $row->id).'" class="btn btn-info btn-sm">Detail</a>';
                $editBtn = '<a href="'.route('admin.residents.edit', $row->id).'" class="btn btn-warning btn-sm ml-1">Edit</a>';
                return $detailBtn . ' ' . $editBtn;
            })
            // FILTER KHUSUS UNTUK NAME
        ->filterColumn('name', function($query, $keyword) {
            $query->whereHas('user', function($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%");
            });
        })
            ->rawColumns(['current_stage', 'action']) // Izinkan HTML di kolom tahap
            ->setRowId('id');
    }

    public function query(Resident $model): QueryBuilder
    {
        $query = $model->newQuery()->with(['user', 'currentStage'])
            ->whereHas('user', function ($q) {
                $q->whereNull('deleted_at');
            });

        // === BAGIAN YANG DIPERBARUI ===
        if ($stageId = $this->request()->get('stage_id')) {
            // Jika nilai filter adalah 'none', cari yang tahapnya NULL
            if ($stageId === 'none') {
                $query->whereNull('current_stage_id');
            } else {
                // Jika tidak, filter berdasarkan ID tahap
                $query->where('current_stage_id', $stageId);
            }
        }

        return $query;
    }
    
    // ... sisa method (html, getColumns, filename) tidak berubah ...
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('resident-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->orderBy(1) // Urutkan berdasarkan nama
                    ->selectStyleSingle();
    }

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

    protected function filename(): string
    {
        return 'Resident_' . date('YmdHis');
    }
}
