<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\TaskCategoryDataTable;
use App\Http\Controllers\Controller;
use App\Models\TaskCategory;
use Illuminate\Http\Request;

class TaskCategoryController extends Controller
{
    public function index(TaskCategoryDataTable $dataTable)
    {
        return $dataTable->render('admin.categories.index');
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:task_categories,name',
            'description' => 'nullable|string',
        ]);

        TaskCategory::create($request->all());

        return redirect()->route('admin.task-categories.index')->with('success', 'Kategori baru berhasil dibuat.');
    }

    public function edit(TaskCategory $taskCategory)
    {
        return view('admin.categories.edit', compact('taskCategory'));
    }

    public function update(Request $request, TaskCategory $taskCategory)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:task_categories,name,' . $taskCategory->id,
            'description' => 'nullable|string',
        ]);

        $taskCategory->update($request->all());

        return redirect()->route('admin.task-categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(TaskCategory $taskCategory)
    {
        // Tambahkan validasi jika kategori ini sedang digunakan di aturan
        if ($taskCategory->requirementRules()->exists()) {
            return redirect()->route('admin.task-categories.index')->with('error', 'Kategori tidak bisa dihapus karena sedang digunakan dalam sebuah aturan.');
        }
        
        $taskCategory->delete();

        return redirect()->route('admin.task-categories.index')->with('success', 'Kategori berhasil dihapus.');
    }
}