@extends('adminlte::page')
@section('title', 'Semua Tugas Ilmiah')
@section('content_header')
    <h1 class="m-0 text-dark">Semua Tugas Ilmiah</h1>
@stop
@section('content')
    <div class="card">
        <div class="card-body">
            {{-- Halaman ini menggunakan DataTable dan view yang sama dengan Admin --}}
            @include('admin.submissions.all', ['dataTable' => $dataTable])
        </div>
    </div>
@stop
@push('js')
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}
    <script>
        // Skrip filter yang sama dengan halaman admin
        $('#filter-btn').on('click', function(e) {
            e.preventDefault();
            let url = '{!! route('lecturer.submissions') !!}?' +
                'stage_id=' + $('#stage_filter').val() +
                '&division_id=' + $('#division_filter').val() +
                '&status=' + $('#status_filter').val();
            $('#allsubmission-table').DataTable().ajax.url(url).load();
        });
    </script>
@endpush
