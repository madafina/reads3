@extends('adminlte::page')

@section('title', 'Detail Residen')

@section('content_header')
    <h1 class="m-0 text-dark">Detail Residen</h1>
@stop

@section('content')
    {{-- KARTU PROFIL RESIDEN --}}
    <div class="row">
        {{-- Kolom untuk Foto --}}
        <div class="col-md-4">
            <div class="card card-primary card-outline">
                <div class="card-body box-profile text-center">
                    <div class="text-center mb-3">
                        @if ($resident->photo)
                            <img class="profile-user-img img-fluid img-circle"
                                 src="{{ asset('storage/' . $resident->photo) }}"
                                 alt="Foto Profil Residen" style="width: 128px; height: 128px; object-fit: cover;">
                        @else
                             <img class="profile-user-img img-fluid img-circle"
                                 src="https://placehold.co/128x128/6c757d/ffffff?text=No+Photo"
                                 alt="Foto Profil Residen">
                        @endif
                    </div>

                    <h3 class="profile-username text-center">{{ $resident->user->name }}</h3>
                    <p class="text-muted text-center">{{ $resident->nim }}</p>
                </div>
            </div>
        </div>

        {{-- Kolom untuk Detail Teks --}}
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Informasi Detail</h3>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Email</dt>
                        <dd class="col-sm-8">{{ $resident->user->email }}</dd>

                        <dt class="col-sm-4">No. Telepon</dt>
                        <dd class="col-sm-8">{{ $resident->phone_number ?? '-' }}</dd>

                        <dt class="col-sm-4">Tahap Saat Ini</dt>
                        <dd class="col-sm-8">{{ $resident->currentStage->name ?? 'Belum Diatur' }}</dd>

                        {{-- === BAGIAN YANG DITAMBAHKAN === --}}
                        <dt class="col-sm-4">Dosen Pembimbing Aktif</dt>
                        <dd class="col-sm-8">{{ $resident->currentSupervisor()->first()->name ?? 'Belum Diatur' }}</dd>
                        
                        <dt class="col-sm-4">Angkatan</dt>
                        <dd class="col-sm-8">{{ $resident->batch ?? '-' }}</dd>
                        
                        <dt class="col-sm-4">Tanggal Masuk</dt>
                        <dd class="col-sm-8">{{ $resident->start_date ? \Carbon\Carbon::parse($resident->start_date)->translatedFormat('d F Y') : '-' }}</dd>
                    </dl>
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
