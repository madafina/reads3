<?php

namespace App\DataTables;

use App\Models\RequirementRule;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class RequirementRuleDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('stage', fn($row) => $row->stage->name ?? 'Semua Tahap')
            ->addColumn('division', fn($row) => $row->division->name ?? '-')
            ->addColumn('task_categories', function ($row) {
                return $row->taskCategories->pluck('name')->implode(', ');
            })
            ->addColumn('action', function($row) {
                $editBtn = '<a href="'.route('admin.requirement-rules.edit', $row->id).'" class="btn btn-warning btn-sm">Edit</a>';
                $deleteForm = '
                    <form action="'.route('admin.requirement-rules.destroy', $row->id).'" method="POST" class="d-inline">
                        '.csrf_field().'
                        '.method_field("DELETE").'
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Yakin hapus aturan ini?\')">Hapus</button>
                    </form>
                ';
                return $editBtn . ' ' . $deleteForm;
            })
            ->setRowId('id');
    }

    public function query(RequirementRule $model): QueryBuilder
    {
        return $model->newQuery()->with(['stage', 'division', 'taskCategories']);
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('requirementrule-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->orderBy(0)
                    ->selectStyleSingle();
    }

    public function getColumns(): array
    {
        return [
            Column::make('id')->title('ID'),
            Column::make('name')->title('Nama Aturan'),
            Column::make('stage')->title('Tahap'),
            Column::make('division')->title('Divisi'),
            Column::make('task_categories')->title('Kategori Tugas'),
            Column::make('required_count')->title('Jumlah'),
            Column::computed('action')->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'RequirementRule_' . date('YmdHis');
    }
}