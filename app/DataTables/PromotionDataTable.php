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
                if (!$row->currentStage) return '<span class="badge badge-secondary">N/A</span>';
                $isComplete = $this->progressService->isStageComplete($row);
                return $isComplete ? '<span class="badge badge-success">Lengkap</span>' : '<span class="badge badge-warning">Belum Lengkap</span>';
            })
             ->addColumn('show', function($row){
                // Mengganti tombol file dengan tombol detail
                return '<a href="'. route('admin.residents.show', $row->user->resident->id) .'" class="btn btn-info btn-sm">Lihat</a>';
            })
            ->addColumn('action', function($row) {
                if (!$row->currentStage) return 'Tidak ada aksi';
                $isComplete = $this->progressService->isStageComplete($row);
                if ($isComplete && $row->currentStage->order < 4) {
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
            ->rawColumns(['completion_status', 'show', 'action']);
    }

    public function query(Resident $model)
    {
        $query = $model->newQuery()->with(['user', 'currentStage']);

        // Filter berdasarkan tahap
        if ($stageId = $this->request()->get('stage_id')) {
            $query->where('current_stage_id', $stageId);
        }

        // Filter berdasarkan status kelengkapan
        if ($status = $this->request()->get('status')) {
            $residents = Resident::with('currentStage')->get();
            $filteredIds = [];

            foreach ($residents as $resident) {
                if (!$resident->currentStage) continue;

                $isComplete = $this->progressService->isStageComplete($resident);
                if ($status === 'complete' && $isComplete) {
                    $filteredIds[] = $resident->id;
                } elseif ($status === 'incomplete' && !$isComplete) {
                    $filteredIds[] = $resident->id;
                }
            }
            $query->whereIn('id', $filteredIds);
        }

        return $query;
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
            Column::computed('show')->title('Detail')->addClass('text-center'),
            Column::computed('action')->title('Aksi')->addClass('text-center'),
        ];
    }
}
