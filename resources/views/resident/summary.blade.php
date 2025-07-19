@extends('adminlte::page')

@section('title', 'Rekap Ilmiah Saya')

@section('content_header')
    <h1 class="m-0 text-dark">Rekap Ilmiah Saya</h1>
@stop

@section('content')
    {{-- Di sinilah kita memanggil komponen Livewire. --}}
    {{-- Semua logika dan tampilan tabel akan dirender di sini. --}}
    @livewire('resident.obligation-summary')
@stop
