@extends('adminlte::page')

@section('title', 'Detail Dosen')

@section('content_header')
    <h1 class="m-0 text-dark">Detail Dosen</h1>
@stop

@section('content')
    {{-- KARTU PROFIL DOSEN --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4><strong>{{ $lecturer->name }}</strong></h4>
                    <ul class="list-unstyled">
                        <li><strong>Email:</strong> {{ $lecturer->email }}</li>
                        {{-- Anda bisa menambahkan data lain dari tabel lecturers jika ada --}}
                        {{-- contoh: <li><strong>NIDN:</strong> {{ $lecturer->lecturer->nidn ?? '-' }}</li> --}}
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- KARTU UNTUK TABEL MAHASISWA BIMBINGAN --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Mahasiswa Bimbingan</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered" id="advisees-table" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Residen</th>
                                <th>NIM</th>
                                <th>Tahap Saat Ini</th>
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
            $('#advisees-table').DataTable({
                processing: true,
                serverSide: true,
                // Mengambil data dari route khusus untuk mahasiswa bimbingan dosen ini
                ajax: '{!! route('admin.lecturers.advisees', $lecturer->id) !!}', 
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'name', name: 'user.name' },
                    { data: 'nim', name: 'nim' },
                    { data: 'current_stage', name: 'currentStage.name' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ]
            });
        });
    </script>
@endpush