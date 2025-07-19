<?php

namespace App\DataTables;

use App\Models\Submission;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Carbon\Carbon;

class AllSubmissionDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('presentation_date', fn($row) => Carbon::parse($row->presentation_date)->translatedFormat('d F Y'))
            ->addColumn('resident_name', fn($row) => $row->resident->user->name ?? 'N/A')
            ->addColumn('task_category_name', fn($row) => $row->taskCategory->name ?? 'N/A')
            // TAMBAHKAN DUA KOLOM BARU INI
            ->addColumn('stage_name', fn($row) => $row->stage->name ?? '-')
            ->addColumn('division_name', fn($row) => $row->division->name ?? '-')
            ->addColumn('status', function ($row) {
                $badges = [
                    'verified' => 'success',
                    'rejected' => 'danger',
                    'pending' => 'warning',
                ];
                $status = $row->status;
                return '<span class="badge badge-'.($badges[$status] ?? 'secondary').'">'.ucfirst($status).'</span>';
            })
            ->addColumn('file', fn($row) => '<a href="'. asset('storage/' . $row->file_path) .'" target="_blank" class="btn btn-secondary btn-sm">Lihat File</a>')
            ->rawColumns(['status', 'file'])
            ->setRowId('id');
    }

    public function query(Submission $model): QueryBuilder
    {
        // Eager load relasi baru
        $query = $model->newQuery()->with(['resident.user', 'taskCategory', 'stage', 'division']);

        // Logika filter yang diperbarui
        if ($stageId = $this->request()->get('stage_id')) {
            $query->where('stage_id', $stageId);
        }
        if ($status = $this->request()->get('status')) {
            $query->where('status', $status);
        }
        if ($divisionId = $this->request()->get('division_id')) {
            $query->where('division_id', $divisionId);
        }

        return $query;
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('allsubmission-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->orderBy(0, 'desc');
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
            Column::make('status')->title('Status'),
            Column::computed('file')->title('File')->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'AllSubmission_' . date('YmdHis');
    }
}
