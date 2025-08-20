@extends('adminlte::page')

@section('title', 'Manajemen Pengguna')

@push('css')
    {{-- CSS untuk DataTables Buttons --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
@endpush

@section('content_header')
    <div class="d-flex justify-content-between">
        <h1 class="m-0 text-dark">Manajemen Pengguna</h1>
        <div>
             <a href="{{ route('admin.users.trashed') }}" class="btn btn-secondary">Tong Sampah</a>
        </div>
    </div>
@stop

@section('content')

{{-- Card untuk Filter --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Filter Data</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="role_filter">Filter Berdasarkan Peran</label>
                        <select id="role_filter" class="form-control">
                            <option value="">-- Semua Peran --</option>
                            {{-- Loop ini membutuhkan variabel $roles dari controller --}}
                            @foreach ($roles as $role)
                                <option value="{{ $role->name }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <div class="form-group">
                        <button id="filter-btn" class="btn btn-primary">Terapkan Filter</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Dari halaman ini, Anda dapat merßeset password pengguna kembali ke default ("123456").</span>
                       
                        <a href="{{ route('admin.users.export')  }}" class="btn btn-success btn-sm">Download<span class="ml-2"><i class="fas fa-file-excel"></i></span></a>
                    </div>                  
                </div>
                <div class="card-body">
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

     <script>
        // Gunakan document.ready dari jQuery untuk memastikan semua skrip, termasuk dari Yajra, sudah dimuat
        $(function() {
            $('#filter-btn').on('click', function(e) {
                e.preventDefault();
                // Dapatkan instance DataTable di dalam event handler untuk memastikan tabel sudah ada
                var table = $('#user-table').DataTable();
                table.ajax.url(
                    "{{ route('admin.users.index') }}?role=" + $('#role_filter').val()
                ).load();
            });
        });
    </script>
@endpush
