@extends('adminlte::page')

@section('title', 'Lihat Ilmiah Lain')

@section('content_header')
    <h1 class="m-0 text-dark">Arsip Tugas Ilmiah</h1>
@stop

@section('content')
    {{-- Card untuk Filter --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Filter Data</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="category_filter">Filter Berdasarkan Kategori</label>
                        <select id="category_filter" class="form-control">
                            <option value="">-- Semua Kategori --</option>
                            {{-- Loop ini membutuhkan variabel $taskCategories dari controller --}}
                            @foreach ($taskCategories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
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

    {{-- Card untuk Tabel Data --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Tugas Ilmiah Terverifikasi</h3>
                    <div class="card-tools">
                        {{-- <p class="text-muted mb-0">Hanya menampilkan tugas dari residen lain. File tidak dapat diunduh.</p> --}}
                    </div>
                </div>
                <div class="card-body">
                    {{-- Tabel akan dirender oleh skrip Yajra --}}
                    {{ $dataTable->table() }}
                </div>
            </div>
        </div>
    </div>
@stop

@push('js')
    {{-- Skrip untuk merender DataTable --}}
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}

    {{-- Skrip untuk membuat filter bekerja --}}
    <script>
        $('#filter-btn').on('click', function(e) {
            e.preventDefault();
            // Ambil instance tabel dan gambar ulang dengan parameter baru
            $('#othersubmission-table').DataTable().ajax.url(
                "{!! route('resident.browse') !!}?category_id=" + $('#category_filter').val()
            ).load();
        });
    </script>
@endpush