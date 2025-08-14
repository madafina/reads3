@extends('adminlte::page')

@section('title', 'Manajemen Residen')

@section('content_header')
    <div class="d-flex justify-content-between">
        <h1 class="m-0 text-dark">Manajemen Residen</h1>
        <div>
            <a href="{{ route('admin.residents.import.form') }}" class="btn btn-success">Impor Residen</a>
            <a href="#" class="btn btn-primary">Tambah Residen</a>
        </div>
    </div>
@stop

@section('content')
    {{-- KARTU UNTUK FILTER --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Filter Data</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="stage_filter">Filter Berdasarkan Tahap</label>
                        <select id="stage_filter" class="form-control">
                            <option value="">-- Semua Tahap --</option>
                            @foreach ($stages as $stage)
                                <option value="{{ $stage->id }}">{{ $stage->name }}</option>
                            @endforeach
                            {{-- OPSI BARU DI SINI --}}
                            <option value="none">-- Belum Memiliki Tahap --</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <div class="form-group">
                        <button id="filter-btn" class="btn btn-primary">Terapkan Filter</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- KARTU UNTUK TABEL DATA --}}
    <div class="card">
        <div class="card-body">
            {{ $dataTable->table() }}
        </div>
    </div>
@stop

@push('js')
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}

    <script>
        $(function() {
            $('#filter-btn').on('click', function(e) {
                e.preventDefault();
                var table = $('#resident-table').DataTable();
                table.ajax.url(
                    "{{ route('admin.residents.index') }}?stage_id=" + $('#stage_filter').val()
                ).load();
            });
        });
    </script>
@endpush
