@extends('adminlte::page')

@section('title', 'Upload Tugas Ilmiah')

@section('content_header')
    <h1 class="m-0 text-dark">Form Upload Tugas Ilmiah</h1>
@stop

@section('content')
    {{-- Di sinilah kita memanggil komponen Livewire --}}
    @livewire('submission.create-form')
@stop