@extends('adminlte::page')

@section('title', 'Promosi Tahap Residen')

@section('content_header')
    <h1 class="m-0 text-dark">Promosi Tahap Residen</h1>
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
                        <label for="stage_filter">Filter Tahap</label>
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
                        <label for="status_filter">Filter Status Kelengkapan</label>
                        <select id="status_filter" class="form-control">
                            <option value="">-- Semua Status --</option>
                            <option value="complete">Lengkap</option>
                            <option value="incomplete">Belum Lengkap</option>
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
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}

    <script>
        $('#filter-btn').on('click', function(e) {
            e.preventDefault();
            // Ambil instance tabel dan gambar ulang dengan parameter baru
            $('#promotion-table').DataTable().ajax.url(
                "{{ route('admin.promotions.index') }}?stage_id=" + $('#stage_filter').val() + "&status=" + $('#status_filter').val()
            ).load();
        });
    </script>
@endpush
