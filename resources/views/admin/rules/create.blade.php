@extends('adminlte::page')

{{-- Aktifkan plugin Select2 untuk halaman ini --}}
@section('plugins.Select2', true)

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
                            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" placeholder="Contoh: Kewajiban Journal Reading Tahap I" required>
                            @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label for="stage_id">Berlaku untuk Tahap</label>
                            <select name="stage_id" id="stage_select" class="form-control">
                                <option value="">-- Aturan Umum (Semua Tahap) --</option>
                                @foreach ($stages as $stage)
                                    <option value="{{ $stage->id }}">{{ $stage->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Divisi akan muncul/hilang berdasarkan pilihan Tahap --}}
                        <div class="form-group" id="division-wrapper" style="display: none;">
                            <label for="division_id">Berlaku untuk Divisi (Khusus Tahap II)</label>
                            <select name="division_id" class="form-control">
                                <option value="">-- Aturan Umum (Semua Divisi di Tahap II) --</option>
                                @foreach ($divisions as $division)
                                    <option value="{{ $division->id }}">{{ $division->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="task_category_ids">Kategori Ilmiah yang Termasuk (Bisa lebih dari satu)</label>
                            {{-- Tambahkan class 'select2' di sini --}}
                            <select name="task_category_ids[]" class="form-control select2 @error('task_category_ids') is-invalid @enderror" multiple="multiple" data-placeholder="Pilih kategori Ilmiah" style="width: 100%;">
                                @foreach ($taskCategories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                             @error('task_category_ids') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label for="required_count">Jumlah yang Harus Dipenuhi</label>
                            <input type="number" class="form-control @error('required_count') is-invalid @enderror" name="required_count" value="{{ old('required_count') }}" required>
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
    $(document).ready(function() {
        // Inisialisasi Select2
        $('.select2').select2();

        // Logika untuk menampilkan/menyembunyikan dropdown Divisi
        $('#stage_select').on('change', function () {
            var divisionWrapper = $('#division-wrapper');
            // Tampilkan dropdown Divisi hanya jika 'Tahap II' yang dipilih
            if (this.value == '{{ $stage2Id ?? 0 }}') {
                divisionWrapper.show();
            } else {
                divisionWrapper.hide();
                // Kosongkan pilihan divisi jika tahap lain dipilih
                divisionWrapper.find('select').val('');
            }
        });
    });
</script>
@endpush