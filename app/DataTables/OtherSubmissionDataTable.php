<?php

namespace App\DataTables;

use App\Models\Submission;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
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
            ->addColumn('stage_name', fn($row) => $row->stage->name ?? '-') // Kolom baru
            ->addColumn('division_name', fn($row) => $row->division->name ?? '-') // Kolom baru
            ->addColumn('status', function ($row) {
                if ($row->status == 'verified') {
                    return '<span class="badge badge-success">Terverifikasi</span>';
                }
                return '<span class="badge badge-secondary">Lainnya</span>';
            })
            // ->addColumn('file', function($row){
            //     return '<button class="btn btn-secondary btn-sm" disabled>Lihat File</button>';
            // })
            // === BAGIAN YANG DIPERBARUI ===
            ->addColumn('action', function($row){
                // Mengganti tombol file dengan tombol detail
                return '<a href="'. route('submissions.show', $row->id) .'" class="btn btn-info btn-sm">Lihat Detail</a>';
            })
            ->rawColumns(['status', 'action']) // Pastikan kolom action di-render sebagai HTML
            ->setRowId('id');
            
    }

    public function query(Submission $model): QueryBuilder
    {
        $currentResidentId = Auth::user()->resident->id;

        $query = $model->newQuery()
            ->where('status', 'verified')
            // ->where('resident_id', '!=', $currentResidentId)
            ->with(['resident.user', 'taskCategory', 'stage', 'division']); // Eager load relasi baru

        // Logika filter baru
        if ($categoryId = $this->request()->get('category_id')) {
            $query->where('task_category_id', $categoryId);
        }
        if ($stageId = $this->request()->get('stage_id')) {
            $query->where('stage_id', $stageId);
        }
        if ($divisionId = $this->request()->get('division_id')) {
            $query->where('division_id', $divisionId);
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
            Column::make('stage_name')->title('Tahap'), // Kolom baru
            Column::make('division_name')->title('Divisi'), // Kolom baru
            Column::make('presentation_date')->title('Tgl Sidang'),
            // Column::computed('file')->title('File')->addClass('text-center'),
            Column::computed('action')->title('Aksi')->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'OtherSubmission_' . date('YmdHis');
    }
}