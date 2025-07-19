<?php

namespace App\DataTables;

use App\Models\Submission;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Carbon\Carbon;

class OtherSubmissionDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('presentation_date', fn($row) => Carbon::parse($row->presentation_date)->translatedFormat('d F Y'))
            ->addColumn('resident_name', fn($row) => $row->resident->user->name ?? 'N/A')
            ->addColumn('task_category_name', fn($row) => $row->taskCategory->name ?? 'N/A')
            ->addColumn('status', function ($row) {
                // Hanya tampilkan yang sudah terverifikasi
                if ($row->status == 'verified') {
                    return '<span class="badge badge-success">Terverifikasi</span>';
                }
                return '<span class="badge badge-secondary">Lainnya</span>';
            })
            ->addColumn('file', function($row){
                // Tombol file dinonaktifkan
                return '<button class="btn btn-secondary btn-sm" disabled>Lihat File</button>';
            })
            ->rawColumns(['status', 'file'])
            ->setRowId('id');
    }

    public function query(Submission $model): QueryBuilder
    {
        $currentResidentId = Auth::user()->resident->id;

        $query = $model->newQuery()
            ->where('status', 'verified')
            ->where('resident_id', '!=', $currentResidentId)
            ->with(['resident.user', 'taskCategory']);

        // TAMBAHKAN BLOK IF INI UNTUK MENERAPKAN FILTER
        if ($categoryId = $this->request()->get('category_id')) {
            $query->where('task_category_id', $categoryId);
        }

        return $query;
    }
    
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('othersubmission-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->orderBy(0, 'desc')
                    ->selectStyleSingle();
    }

    public function getColumns(): array
    {
        return [
            Column::make('id')->title('ID')->width(50),
            Column::make('resident_name')->title('Nama Residen'),
            Column::make('title')->title('Judul'),
            Column::make('task_category_name')->title('Kategori'),
            Column::make('presentation_date')->title('Tgl Sidang'),
            Column::make('status')->title('Status'),
            Column::computed('file')->title('File')->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'OtherSubmission_' . date('YmdHis');
    }
}