@extends('adminlte::page')

@section('title', 'Semua Tugas Ilmiah')

@section('content_header')
    <h1 class="m-0 text-dark">Semua Tugas Ilmiah</h1>
@stop

@section('content')
    {{-- Card untuk Filter --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Filter Data</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="stage_filter">Filter Berdasarkan Tahap</label>
                        <select id="stage_filter" class="form-control">
                            <option value="">-- Semua Tahap --</option>
                            @foreach ($stages as $stage)
                                <option value="{{ $stage->id }}">{{ $stage->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="status_filter">Filter Berdasarkan Status</label>
                        <select id="status_filter" class="form-control">
                            <option value="">-- Semua Status --</option>
                            <option value="pending">Pending</option>
                            <option value="verified">Verified</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <div class="form-group">
                        <button id="filter-btn" class="btn btn-primary">Terapkan Filter</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Card untuk Tabel Data --}}
    <div class="card">
        <div class="card-body">
            {{ $dataTable->table() }}
        </div>
    </div>
@stop

@push('js')
    {{-- Skrip untuk merender DataTable --}}
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}

    <script>
        // Skrip untuk membuat filter bekerja
        $('#filter-btn').on('click', function(e) {
            e.preventDefault();
            // Ambil instance tabel dan gambar ulang dengan parameter baru
            $('#allsubmission-table').DataTable().ajax.url(
                "{!! route('admin.submissions.all') !!}?stage_id=" + $('#stage_filter').val() + "&status=" + $('#status_filter').val()
            ).load();
        });
    </script>
@endpush