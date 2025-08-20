@extends('adminlte::page')

@section('title', 'Pengguna Terhapus')

@push('css')
    {{-- CSS untuk DataTables Buttons --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
@endpush

@section('content_header')
    <div class="d-flex justify-content-between">
        <h1 class="m-0 text-dark">Tong Sampah Pengguna</h1>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Kembali ke Daftar Pengguna</a>
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
                    <p>Halaman ini berisi daftar pengguna yang telah dihapus. Anda dapat memulihkannya atau menghapusnya secara permanen.</p>
                    {{ $dataTable->table() }}
                </div>
            </div>
        </div>
    </div>
@stop

@push('js')

    {{-- JS untuk DataTables Buttons --}}
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}
@endpush
