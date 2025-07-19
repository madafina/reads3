@extends('adminlte::page')

@section('title', 'Histori Ilmiah')

@section('content_header')
    <h1 class="m-0 text-dark">Histori Ilmiah Saya</h1>
@stop

@section('content')
    {{-- Card untuk Filter --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Filter Data</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="category_filter">Kategori</label>
                        <select id="category_filter" class="form-control">
                            <option value="">-- Semua Kategori --</option>
                            @foreach ($taskCategories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="stage_filter">Tahap</label>
                        <select id="stage_filter" class="form-control">
                            <option value="">-- Semua Tahap --</option>
                            @foreach ($stages as $stage)
                                <option value="{{ $stage->id }}">{{ $stage->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="division_filter">Divisi</label>
                        <select id="division_filter" class="form-control">
                            <option value="">-- Semua Divisi --</option>
                            @foreach ($divisions as $division)
                                <option value="{{ $division->id }}">{{ $division->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <div class="form-group">
                        <button id="filter-btn" class="btn btn-primary">Terapkan Filter</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Card untuk Tabel Data --}}
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-striped" id="submissions-table" style="width:100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Judul</th>
                        <th>Kategori</th>
                        <th>Tahap</th>
                        <th>Divisi</th>
                        <th>Pembimbing</th>
                        <th>Tgl. Sidang</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@stop

@push('js')
    <script>
        $(function() {
            // Inisialisasi DataTable
            var table = $('#submissions-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{!! route('submissions.history') !!}',
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'title', name: 'title' },
                    { data: 'task_category', name: 'taskCategory.name' },
                    { data: 'stage', name: 'stage.name' },
                    { data: 'division', name: 'division.name' },
                    { data: 'supervisor', name: 'supervisor.name' },
                    { data: 'presentation_date', name: 'presentation_date' },
                    { data: 'status', name: 'status' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ]
            });

            // Event handler untuk tombol filter
            $('#filter-btn').on('click', function(e) {
                e.preventDefault();
                // Bangun URL dengan semua parameter filter
                let url = '{!! route('submissions.history') !!}?' +
                    'category_id=' + $('#category_filter').val() +
                    '&stage_id=' + $('#stage_filter').val() +
                    '&division_id=' + $('#division_filter').val();
                
                // Muat ulang data tabel dengan URL baru
                table.ajax.url(url).load();
            });
        });
    </script>
@endpush
