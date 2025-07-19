@extends('adminlte::page')

@section('title', 'Detail Residen')

@section('content_header')
    <h1 class="m-0 text-dark">Detail Residen</h1>
@stop

@section('content')
    {{-- KARTU PROFIL RESIDEN --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h4><strong>{{ $resident->user->name }}</strong></h4>
                            <ul class="list-unstyled">
                                <li><strong>NIM:</strong> {{ $resident->nim }}</li>
                                <li><strong>Email:</strong> {{ $resident->user->email }}</li>
                                <li><strong>Tahap Saat Ini:</strong> {{ $resident->currentStage->name ?? 'Belum Diatur' }}</li>
                                <li><strong>Angkatan:</strong> {{ $resident->batch ?? '-' }}</li>
                                <li><strong>Tanggal Masuk:</strong> {{ \Carbon\Carbon::parse($resident->start_date)->translatedFormat('d F Y') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- KARTU UNTUK TABEL TUGAS --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Riwayat Ilmiah</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered" id="submissions-table" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Judul</th>
                                <th>Kategori</th>
                                <th>Tgl Sidang</th>
                                <th>Status</th>
                                <th>File</th>
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
                // Mengambil data dari route khusus untuk submission residen ini
                ajax: '{!! route('admin.residents.submissions', $resident->id) !!}', 
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'title', name: 'title' },
                    { data: 'task_category_name', name: 'taskCategory.name' },
                    { data: 'presentation_date', name: 'presentation_date' },
                    { data: 'status', name: 'status' },
                    { data: 'file', name: 'file', orderable: false, searchable: false },
                ]
            });
        });
    </script>
@endpush