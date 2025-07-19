@extends('adminlte::page')

@section('title', 'Lihat Ilmiah Lain')

@section('content_header')
    <h1 class="m-0 text-dark">Arsip Ilmiah</h1>
@stop

@section('content')
    {{-- Card untuk Filter --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Filter Data</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="category_filter">Kategori</label>
                        <select id="category_filter" class="form-control">
                            <option value="">-- Semua Kategori --</option>
                            @foreach ($taskCategories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="stage_filter">Tahap</label>
                        <select id="stage_filter" class="form-control">
                            <option value="">-- Semua Tahap --</option>
                            @foreach ($stages as $stage)
                                <option value="{{ $stage->id }}">{{ $stage->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="division_filter">Divisi</label>
                        <select id="division_filter" class="form-control">
                            <option value="">-- Semua Divisi --</option>
                            @foreach ($divisions as $division)
                                <option value="{{ $division->id }}">{{ $division->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <div class="form-group">
                        <button id="filter-btn" class="btn btn-primary">Terapkan Filter</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Card untuk Tabel Data --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Ilmiah Terverifikasi</h3>
        </div>
        <div class="card-body">
            {{ $dataTable->table() }}
        </div>
    </div>
@stop

@push('js')
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}

    <script>
        $('#filter-btn').on('click', function(e) {
            e.preventDefault();
            // Bangun URL dengan semua parameter filter
            let url = '{!! route('resident.browse') !!}?' +
                'category_id=' + $('#category_filter').val() +
                '&stage_id=' + $('#stage_filter').val() +
                '&division_id=' + $('#division_filter').val();
            
            // Muat ulang data tabel dengan URL baru
            $('#othersubmission-table').DataTable().ajax.url(url).load();
        });
    </script>
@endpush
