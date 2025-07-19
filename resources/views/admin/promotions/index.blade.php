@extends('adminlte::page')

@section('title', 'Promosi Tahap Residen')

@section('content_header')
    <h1 class="m-0 text-dark">Promosi Tahap Residen</h1>
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