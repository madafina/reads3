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
        $query = $model->newQuery()->with(['resident.user', 'taskCategory']);

        // Terapkan filter jika ada input dari request
        if ($stageId = $this->request()->get('stage_id')) {
            // Ambil semua submission dari resident yang ada di tahap tersebut
            $query->whereHas('resident', function ($q) use ($stageId) {
                $q->where('current_stage_id', $stageId);
            });
        }

        if ($status = $this->request()->get('status')) {
            $query->where('status', $status);
        }

        return $query;
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('allsubmission-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->orderBy(0, 'desc') // Urutkan berdasarkan ID terbaru
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
        return 'AllSubmission_' . date('YmdHis');
    }
}