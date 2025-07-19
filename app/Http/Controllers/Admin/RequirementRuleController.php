<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DataTables\RequirementRuleDataTable;
use App\Models\RequirementRule;
use App\Models\Stage;
use App\Models\Division;
use App\Models\TaskCategory;

class RequirementRuleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(RequirementRuleDataTable $dataTable)
    {
        return $dataTable->render('admin.rules.index');
    }

    public function create()
    {
        $stages = Stage::orderBy('order')->get();
        $divisions = Division::orderBy('name')->get();
        $taskCategories = TaskCategory::orderBy('name')->get();

        // Kita butuh ID Tahap II untuk logika JavaScript di view
        $stage2Id = Stage::where('name', 'Tahap II')->first()->id;

        return view('admin.rules.create', compact('stages', 'divisions', 'taskCategories', 'stage2Id'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'stage_id' => 'nullable|exists:stages,id',
            'division_id' => 'nullable|exists:divisions,id',
            'required_count' => 'required|integer|min:1',
            'task_category_ids' => 'required|array',
            'task_category_ids.*' => 'exists:task_categories,id',
        ]);

        $rule = RequirementRule::create($request->only('name', 'stage_id', 'division_id', 'required_count'));
        $rule->taskCategories()->sync($request->task_category_ids);

        return redirect()->route('admin.requirement-rules.index')->with('success', 'Aturan baru berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    public function edit(RequirementRule $requirementRule)
{
    $stages = Stage::orderBy('order')->get();
    $divisions = Division::orderBy('name')->get();
    $taskCategories = TaskCategory::orderBy('name')->get();
    $stage2Id = Stage::where('name', 'Tahap II')->first()->id;

    // Ambil ID kategori tugas yang sudah terhubung dengan aturan ini
    $selectedCategories = $requirementRule->taskCategories->pluck('id')->toArray();

    return view('admin.rules.edit', compact('requirementRule', 'stages', 'divisions', 'taskCategories', 'stage2Id', 'selectedCategories'));
}

public function update(Request $request, RequirementRule $requirementRule)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'stage_id' => 'nullable|exists:stages,id',
        'division_id' => 'nullable|exists:divisions,id',
        'required_count' => 'required|integer|min:1',
        'task_category_ids' => 'required|array',
        'task_category_ids.*' => 'exists:task_categories,id',
    ]);

    $requirementRule->update($request->only('name', 'stage_id', 'division_id', 'required_count'));
    $requirementRule->taskCategories()->sync($request->task_category_ids);

    return redirect()->route('admin.requirement-rules.index')->with('success', 'Aturan berhasil diperbarui.');
}

public function destroy(RequirementRule $requirementRule)
{
    $requirementRule->delete();
    return redirect()->route('admin.requirement-rules.index')->with('success', 'Aturan berhasil dihapus.');
}
}
