@extends('adminlte::page')

@section('plugins.Select2', true)

@section('title', 'Edit Aturan Kewajiban')

@section('content_header')
    <h1 class="m-0 text-dark">Edit Aturan Kewajiban</h1>
@stop

@section('content')
    <form action="{{ route('admin.requirement-rules.update', $requirementRule->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">Nama Aturan</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $requirementRule->name) }}" required>
                            @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label for="stage_id">Berlaku untuk Tahap</label>
                            <select name="stage_id" id="stage_select" class="form-control">
                                <option value="">-- Aturan Umum (Semua Tahap) --</option>
                                @foreach ($stages as $stage)
                                    <option value="{{ $stage->id }}" {{ old('stage_id', $requirementRule->stage_id) == $stage->id ? 'selected' : '' }}>{{ $stage->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group" id="division-wrapper" style="{{ old('stage_id', $requirementRule->stage_id) == $stage2Id ? '' : 'display: none;' }}">
                            <label for="division_id">Berlaku untuk Divisi (Khusus Tahap II)</label>
                            <select name="division_id" class="form-control">
                                <option value="">-- Aturan Umum (Semua Divisi di Tahap II) --</option>
                                @foreach ($divisions as $division)
                                    <option value="{{ $division->id }}" {{ old('division_id', $requirementRule->division_id) == $division->id ? 'selected' : '' }}>{{ $division->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="task_category_ids">Kategori Ilmiah yang Termasuk (Bisa lebih dari satu)</label>
                            <select name="task_category_ids[]" class="form-control select2 @error('task_category_ids') is-invalid @enderror" multiple="multiple" data-placeholder="Pilih kategori ilmiah" style="width: 100%;">
                                @foreach ($taskCategories as $category)
                                    <option value="{{ $category->id }}" {{ in_array($category->id, old('task_category_ids', $selectedCategories)) ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('task_category_ids') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label for="required_count">Jumlah yang Harus Dipenuhi</label>
                            <input type="number" class="form-control @error('required_count') is-invalid @enderror" name="required_count" value="{{ old('required_count', $requirementRule->required_count) }}" required>
                            @error('required_count') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        <a href="{{ route('admin.requirement-rules.index') }}" class="btn btn-default">Batal</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
@stop

@push('js')
<script>
    $(document).ready(function() {
        // Inisialisasi Select2
        $('.select2').select2();

        // Logika untuk menampilkan/menyembunyikan dropdown Divisi
        $('#stage_select').on('change', function () {
            var divisionWrapper = $('#division-wrapper');
            if (this.value == '{{ $stage2Id ?? 0 }}') {
                divisionWrapper.show();
            } else {
                divisionWrapper.hide();
                divisionWrapper.find('select').val('');
            }
        });
    });
</script>
@endpush
