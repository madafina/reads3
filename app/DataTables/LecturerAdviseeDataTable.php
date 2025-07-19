<?php

namespace App\DataTables;

use App\Models\Resident;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class LecturerAdviseeDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('name', fn($row) => $row->user->name ?? 'N/A')
            ->addColumn('current_stage', fn($row) => $row->currentStage->name ?? 'N/A')
            ->addColumn('action', function($row){
                return '<a href="'.route('admin.residents.show', $row->id).'" class="btn btn-info btn-sm">Lihat Profil</a>';
            })
            ->setRowId('id');
    }

    public function query(Resident $model): QueryBuilder
    {
        $lecturerId = Auth::id();
        // Cari residen yang pernah menjadikan dosen ini sebagai pembimbing
        return $model->newQuery()
            ->whereHas('submissions', fn($q) => $q->where('supervisor_id', $lecturerId))
            ->with(['user', 'currentStage'])
            ->distinct();
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('advisee-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->orderBy(1);
    }

    public function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex')->title('No'),
            Column::make('name')->title('Nama Residen'),
            Column::make('nim')->title('NIM'),
            Column::make('current_stage')->title('Tahap Saat Ini'),
            Column::computed('action')->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'LecturerAdvisee_' . date('YmdHis');
    }
}
