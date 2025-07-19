@extends('adminlte::page')

@section('title', 'Edit Aturan Kewajiban')
{{-- ... (content_header sama seperti create) ... --}}
@section('content')
    <form action="{{ route('admin.requirement-rules.update', $requirementRule->id) }}" method="POST">
        @csrf
        @method('PUT')
        {{-- ... (Isi form sama persis dengan create.blade.php, tapi tambahkan value dari $requirementRule) --}}
        {{-- Contoh untuk input 'name': --}}
        <input type="text" class="form-control" name="name" value="{{ old('name', $requirementRule->name) }}">
        
        {{-- Contoh untuk select 'stage_id': --}}
        <select name="stage_id" id="stage_select" class="form-control">
            <option value="">-- Semua Tahap --</option>
            @foreach ($stages as $stage)
                <option value="{{ $stage->id }}" {{ $requirementRule->stage_id == $stage->id ? 'selected' : '' }}>{{ $stage->name }}</option>
            @endforeach
        </select>
        
        {{-- Contoh untuk multiselect 'task_category_ids': --}}
        <select name="task_category_ids[]" class="form-control" multiple>
            @foreach ($taskCategories as $category)
                <option value="{{ $category->id }}" {{ in_array($category->id, $selectedCategories) ? 'selected' : '' }}>{{ $category->name }}</option>
            @endforeach
        </select>

        {{-- Jangan lupa tambahkan JavaScript yang sama di @push('js') untuk menampilkan/menyembunyikan Divisi --}}
    </form>
@stop