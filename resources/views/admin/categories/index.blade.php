@extends('adminlte::page')

@section('title', 'Pengaturan Kategori Tugas')

@section('content_header')
    <div class="d-flex justify-content-between">
        <h1 class="m-0 text-dark">Pengaturan Kategori Tugas</h1>
        <a href="{{ route('admin.task-categories.create') }}" class="btn btn-primary">Tambah Kategori</a>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            <div class="card">
                <div class="card-body">
                    {{ $dataTable->table() }}
                </div>
            </div>
        </div>
    </div>
@stop

@push('js')
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}
@endpush