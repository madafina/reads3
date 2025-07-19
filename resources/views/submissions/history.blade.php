@extends('adminlte::page')

@section('title', 'Ilmiah Saya')

@section('content_header')
    <h1 class="m-0 text-dark">Ilmiah Saya</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered table-striped" id="submissions-table" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Judul</th>
                                <th>Kategori</th>
                                <th>Pembimbing</th>
                                <th>Tgl. Sidang</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop

@push('js')
    <script>
        $(function() {
            $('#submissions-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{!! route('submissions.history') !!}', // Mengambil data dari route yang kita buat
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'title', name: 'title' },
                    { data: 'task_category', name: 'taskCategory.name' },
                    { data: 'supervisor', name: 'supervisor.name' },
                    { data: 'presentation_date', name: 'presentation_date' },
                    { data: 'status', name: 'status' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ]
            });
        });
    </script>
@endpush