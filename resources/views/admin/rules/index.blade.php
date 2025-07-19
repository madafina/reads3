@extends('adminlte::page')

@section('title', 'Pengaturan Kewajiban')

@section('content_header')
    <div class="d-flex justify-content-between">
        <h1 class="m-0 text-dark">Pengaturan Kewajiban</h1>
        <a href="{{ route('admin.requirement-rules.create') }}" class="btn btn-primary">Tambah Aturan</a>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
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