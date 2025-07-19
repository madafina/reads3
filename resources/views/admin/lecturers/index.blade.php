@extends('adminlte::page')

@section('title', 'Manajemen Dosen')

@section('content_header')
    <div class="d-flex justify-content-between">
        <h1 class="m-0 text-dark">Manajemen Dosen</h1>
        <a href="#" class="btn btn-primary">Tambah Dosen</a>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
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