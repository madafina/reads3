@extends('adminlte::page')
@section('title', 'Mahasiswa Bimbingan')
@section('content_header')
    <h1 class="m-0 text-dark">Daftar Mahasiswa Bimbingan</h1>
@stop
@section('content')
    <div class="card">
        <div class="card-body">
            {{ $dataTable->table() }}
        </div>
    </div>
@stop
@push('js')
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}
@endpush
