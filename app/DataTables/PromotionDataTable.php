<?php

namespace App\DataTables;

use App\Models\Resident;
use App\Services\ResidentProgressService;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class PromotionDataTable extends DataTable
{
    protected $progressService;

    public function __construct(ResidentProgressService $progressService)
    {
        $this->progressService = $progressService;
    }

    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->addColumn('name', fn($row) => $row->user->name ?? 'N/A')
            ->addColumn('current_stage', fn($row) => $row->currentStage->name ?? 'N/A')
            ->addColumn('completion_status', function($row) {
                $isComplete = $this->progressService->isStageComplete($row);
                if ($isComplete) {
                    return '<span class="badge badge-success">Lengkap</span>';
                }
                return '<span class="badge badge-warning">Belum Lengkap</span>';
            })
            ->addColumn('action', function($row) {
                $isComplete = $this->progressService->isStageComplete($row);
                if ($isComplete && $row->currentStage->order < 4) { // Asumsi 4 adalah tahap terakhir
                    $nextStageName = \App\Models\Stage::where('order', '>', $row->currentStage->order)->orderBy('order')->first()->name ?? '';
                    $form = '
                        <form action="'.route('admin.promotions.promote', $row->id).'" method="POST">
                            '.csrf_field().'
                            <button type="submit" class="btn btn-primary btn-sm" onclick="return confirm(\'Naikkan '. $row->user->name .' ke '.$nextStageName.'?\')">
                                Naikkan ke '.$nextStageName.'
                            </button>
                        </form>
                    ';
                    return $form;
                }
                return 'Tidak ada aksi';
            })
            ->rawColumns(['completion_status', 'action']);
    }

    public function query(Resident $model)
    {
        return $model->newQuery()->with(['user', 'currentStage']);
    }

    public function html()
    {
        return $this->builder()
                    ->setTableId('promotion-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->orderBy(1);
    }

    protected function getColumns()
    {
        return [
            Column::computed('DT_RowIndex')->title('No'),
            Column::make('name')->title('Nama Residen'),
            Column::make('current_stage')->title('Tahap Saat Ini'),
            Column::computed('completion_status')->title('Status Kelengkapan'),
            Column::computed('action')->title('Aksi')->addClass('text-center'),
        ];
    }
}