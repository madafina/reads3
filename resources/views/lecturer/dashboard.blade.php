@extends('adminlte::page')
@section('title', 'Dashboard Dosen')
@section('content_header')
    <h1 class="m-0 text-dark">Dashboard Dosen</h1>
@stop
@section('content')
    <div class="row">
        <div class="col-lg-4">
            <x-adminlte-small-box title="{{ $adviseeCount }}" text="Mahasiswa Bimbingan" icon="fas fa-user-graduate text-dark"
                url="{{ route('lecturer.advisees') }}" url-text="Lihat Daftar" theme="warning"/>
        </div>
        <div class="col-lg-4">
            <x-adminlte-small-box title="{{ $divisionCount }}" text="Divisi Saya" icon="fas fa-hospital-alt text-white"
                url="{{ route('lecturer.divisions') }}" url-text="Lihat Divisi" theme="danger"/>
        </div>
    </div>
@stop

