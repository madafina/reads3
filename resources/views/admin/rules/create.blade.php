@extends('adminlte::page')

@section('title', 'Tambah Aturan Kewajiban')

@section('content_header')
    <h1 class="m-0 text-dark">Tambah Aturan Kewajiban Baru</h1>
@stop

@section('content')
    <form action="{{ route('admin.requirement-rules.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">Nama Aturan</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}">
                            @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label for="stage_id">Tahap</label>
                            <select name="stage_id" id="stage_select" class="form-control">
                                <option value="">-- Semua Tahap --</option>
                                @foreach ($stages as $stage)
                                    <option value="{{ $stage->id }}">{{ $stage->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Divisi akan muncul/hilang berdasarkan pilihan Tahap --}}
                        <div class="form-group" id="division-wrapper" style="display: none;">
                            <label for="division_id">Divisi (Khusus Tahap II)</label>
                            <select name="division_id" class="form-control">
                                <option value="">-- Aturan Umum Tahap II --</option>
                                @foreach ($divisions as $division)
                                    <option value="{{ $division->id }}">{{ $division->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="task_category_ids">Kategori Tugas yang Termasuk</label>
                            <select name="task_category_ids[]" class="form-control" multiple>
                                @foreach ($taskCategories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                             @error('task_category_ids') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label for="required_count">Jumlah yang Harus Dipenuhi</label>
                            <input type="number" class="form-control @error('required_count') is-invalid @enderror" name="required_count" value="{{ old('required_count') }}">
                            @error('required_count') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ route('admin.requirement-rules.index') }}" class="btn btn-default">Batal</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
@stop

@push('js')
<script>
    document.getElementById('stage_select').addEventListener('change', function () {
        var divisionWrapper = document.getElementById('division-wrapper');
        // Tampilkan dropdown Divisi hanya jika 'Tahap II' yang dipilih
        if (this.value == '{{ $stage2Id }}') {
            divisionWrapper.style.display = 'block';
        } else {
            divisionWrapper.style.display = 'none';
        }
    });
</script>
@endpush