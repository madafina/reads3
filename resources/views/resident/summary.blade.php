@extends('adminlte::page')

@section('title', 'Rekapitulasi Kewajiban')

@section('content_header')
    <h1 class="m-0 text-dark">Rekapitulasi Kewajiban Ilmiah</h1>
@stop

@section('content')
    {{-- Di sinilah kita memanggil komponen Livewire. --}}
    {{-- Semua logika dan tampilan tabel akan dirender di sini. --}}
    @livewire('resident.obligation-summary')
@stop
