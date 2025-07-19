@extends('adminlte::page')

@section('title', 'Edit Tugas Ilmiah')

@section('content_header')
    <h1 class="m-0 text-dark">Edit Tugas Ilmiah</h1>
@stop

@section('content')
    {{-- @livewire('submission.edit-form', ['submission' => $submissionId])
     --}}
     <livewire:submission.edit-form :submission-id="$submission->id" />

@stop