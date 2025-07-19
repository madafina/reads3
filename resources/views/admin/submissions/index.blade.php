@extends('adminlte::page')

@section('title', 'Verifikasi Tugas Ilmiah')

@section('content_header')
    <h1 class="m-0 text-dark">Verifikasi Tugas Ilmiah</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            {{-- TAMBAHKAN BLOK INI UNTUK MENAMPILKAN NOTIFIKASI --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
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
    {{-- Hapus skrip listener Livewire dari sini --}}
@endpush