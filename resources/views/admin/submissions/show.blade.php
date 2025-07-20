@extends('adminlte::page')

@section('title', 'Detail Tugas Ilmiah')

@section('content_header')
    <div class="d-flex justify-content-between">
        <h1 class="m-0 text-dark">Detail Tugas Ilmiah Admin</h1>
        <a href="{{ url()->previous() }}" class="btn btn-secondary">Kembali</a>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8">
            {{-- Card untuk Detail Utama --}}
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Informasi Utama</h3>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Judul Ilmiah</dt>
                        <dd class="col-sm-8">{{ $submission->title }}</dd>

                        <dt class="col-sm-4">Judul Seminar</dt>
                        <dd class="col-sm-8">{{ $submission->seminar_title ?? '-' }}</dd>

                        <dt class="col-sm-4">Deskripsi</dt>
                        <dd class="col-sm-8">{{ $submission->description ?? '-' }}</dd>

                        <dt class="col-sm-4">Residen</dt>
                        <dd class="col-sm-8">{{ $submission->resident->user->name ?? 'N/A' }}</dd>

                        <dt class="col-sm-4">Kategori</dt>
                        <dd class="col-sm-8">{{ $submission->taskCategory->name ?? 'N/A' }}</dd>

                        <dt class="col-sm-4">Tahap</dt>
                        <dd class="col-sm-8">{{ $submission->stage->name ?? 'N/A' }}</dd>

                        <dt class="col-sm-4">Divisi</dt>
                        <dd class="col-sm-8">{{ $submission->division->name ?? '-' }}</dd>

                        <dt class="col-sm-4">Dosen Pembimbing</dt>
                        <dd class="col-sm-8">{{ $submission->supervisor->name ?? 'N/A' }}</dd>

                        <dt class="col-sm-4">Tanggal Sidang</dt>
                        <dd class="col-sm-8">{{ $submission->presentation_date->translatedFormat('d F Y') }}</dd>

                        {{-- HANYA TAMPILKAN JIKA BUKAN "VIEWER ONLY" --}}
                        @if(!$isViewerOnly)
                            <dt class="col-sm-4">Nilai</dt>
                            <dd class="col-sm-8">{{ $submission->grade ?? '-' }}</dd>
                        @endif

                        <dt class="col-sm-4">Status</dt>
                        <dd class="col-sm-8">
                             @if ($submission->status == 'verified')
                                <span class="badge badge-success">Terverifikasi</span>
                            @elseif ($submission->status == 'rejected')
                                <span class="badge badge-danger">Ditolak</span>
                            @else
                                <span class="badge badge-warning">Pending</span>
                            @endif
                        </dd>

                        {{-- HANYA TAMPILKAN JIKA BUKAN "VIEWER ONLY" --}}
                        @if($submission->verified_by && !$isViewerOnly)
                        <dt class="col-sm-4">Diverifikasi oleh</dt>
                        <dd class="col-sm-8">{{ $submission->verifier->name ?? 'N/A' }} pada {{ $submission->verified_at->translatedFormat('d F Y H:i') }}</dd>
                        @endif
                    </dl>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            {{-- Card untuk Aksi Admin, hanya muncul jika login sebagai Admin dan status pending --}}
            @if(Auth::user()->hasRole('Admin') && $submission->status == 'pending')
            <div class="card card-secondary">
                <div class="card-header">
                    <h3 class="card-title">Aksi Admin</h3>
                </div>
                <div class="card-body d-flex justify-content-start">
                    <form action="{{ route('admin.submissions.verify', $submission->id) }}" method="POST" class="d-inline mr-2">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-success" onclick="return confirm('Anda yakin ingin memverifikasi tugas ini?')">
                            <i class="fas fa-check"></i> Verify
                        </button>
                    </form>
                    <form action="{{ route('admin.submissions.reject', $submission->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Anda yakin ingin menolak tugas ini?')">
                            <i class="fas fa-times"></i> Reject
                        </button>
                    </form>
                </div>
            </div>
            @endif

            {{-- Card untuk File Lampiran, tidak muncul untuk viewer only --}}
            @if(!$isViewerOnly)
            <div class="card card-secondary">
                <div class="card-header">
                    <h3 class="card-title">File Lampiran</h3>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @if($submission->file_path)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            File Ilmiah Utama
                            <a href="{{ asset('storage/' . $submission->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">Lihat</a>
                        </li>
                        @endif
                        @if($submission->presentation_file_path)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            File Presentasi
                            <a href="{{ asset('storage/' . $submission->presentation_file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">Lihat</a>
                        </li>
                        @endif
                        @if($submission->grade_file_path)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            File Bukti Nilai
                            <a href="{{ asset('storage/' . $submission->grade_file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">Lihat</a>
                        </li>
                        @endif
                        @if($submission->attendance_file_path)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            File Bukti Presensi
                            <a href="{{ asset('storage/' . $submission->attendance_file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">Lihat</a>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
            @endif
        </div>
    </div>
@stop
