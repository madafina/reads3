@extends('adminlte::page')

@section('title', 'Pengaturan Divisi')

@section('content_header')
    <div class="d-flex justify-content-between">
        <h1 class="m-0 text-dark">Pengaturan Divisi</h1>
        <a href="{{ route('admin.divisions.create') }}" class="btn btn-primary">Tambah Divisi</a>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            {{-- Notifikasi --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="card">
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
@endpush