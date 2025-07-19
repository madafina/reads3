<?php

namespace App\DataTables;

use App\Models\TaskCategory;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class TaskCategoryDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('action', function($row) {
                $editBtn = '<a href="'.route('admin.task-categories.edit', $row->id).'" class="btn btn-warning btn-sm">Edit</a>';
                $deleteForm = '
                    <form action="'.route('admin.task-categories.destroy', $row->id).'" method="POST" class="d-inline">
                        '.csrf_field().'
                        '.method_field("DELETE").'
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Yakin hapus kategori ini? Menghapus ini dapat mempengaruhi aturan yang ada.\')">Hapus</button>
                    </form>
                ';
                // return $editBtn . ' ' . $deleteForm;
                return $editBtn;
            })
            ->setRowId('id');
    }

    public function query(TaskCategory $model): QueryBuilder
    {
        return $model->newQuery();
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('taskcategory-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->orderBy(1)
                    ->selectStyleSingle();
    }

    public function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex')->title('No')->width(30),
            Column::make('name')->title('Nama Kategori'),
            Column::make('description')->title('Deskripsi'),
            Column::computed('action')->width(150)->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'TaskCategory_' . date('YmdHis');
    }
}