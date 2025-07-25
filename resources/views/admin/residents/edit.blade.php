@extends('adminlte::page')

@section('title', 'Edit Residen')

@section('content_header')
    <h1 class="m-0 text-dark">Edit Data Residen</h1>
@stop

@push('css')
    <style>
        /* Perbaikan untuk menyamakan tinggi dan border Select2 dengan form input lain */
        .select2-container .select2-selection--single {
            height: calc(2.25rem + 2px) !important;
            border: 1px solid #ced4da;
            border-radius: 4px;
        }

        .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
            line-height: 2.25rem;
            padding-left: .75rem !important;
        }

        .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow {
            height: 2.25rem !important;
        }
    </style>
@endpush
z
@section('content')
    <div class="row">
        {{-- Kolom Kiri: Form Edit Utama --}}
        <div class="col-md-8">
            <form action="{{ route('admin.residents.update', $resident->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="name">Nama Lengkap</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        name="name" value="{{ old('name', $resident->user->name) }}" required>
                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        name="email" value="{{ old('email', $resident->user->email) }}" required>
                                    @error('email')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="nim">NIM</label>
                                    <input type="text" class="form-control @error('nim') is-invalid @enderror"
                                        name="nim" value="{{ old('nim', $resident->nim) }}" required>
                                    @error('nim')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="batch">Angkatan</label>
                                    <input type="text" class="form-control @error('batch') is-invalid @enderror"
                                        name="batch" value="{{ old('batch', $resident->batch) }}">
                                    @error('batch')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="start_date">Tanggal Masuk</label>
                                    <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                                        name="start_date"
                                        value="{{ old('start_date', $resident->start_date ? $resident->start_date->format('Y-m-d') : '') }}">
                                    @error('start_date')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="phone_number">Nomor Telepon</label>
                                    <input type="text" class="form-control @error('phone_number') is-invalid @enderror"
                                        name="phone_number" value="{{ old('phone_number', $resident->phone_number) }}"
                                        required>
                                    @error('phone_number')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                {{-- Dropdown untuk Pembimbing Aktif --}}
                                <div class="form-group">
                                    <label for="supervisor_id">Dosen Pembimbing Aktif</label>
                                    <select name="supervisor_id" id="pembimbing_select"
                                        class="form-control @error('supervisor_id') is-invalid @enderror">
                                        <option value="">-- Tidak Ada Pembimbing --</option>
                                        @foreach ($lecturers as $lecturer)
                                            <option value="{{ $lecturer->id }}"
                                                {{ old('supervisor_id', $currentSupervisorId) == $lecturer->id ? 'selected' : '' }}>
                                                {{ $lecturer->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('supervisor_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                <a href="{{ route('admin.residents.index') }}" class="btn btn-default">Batal</a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

        </div>


        <div class="col-md-4">
            <div class="card card-secondary">
                <div class="card-header">
                    <h3 class="card-title">Riwayat Dosen Pembimbing</h3>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @forelse($resident->supervisorHistory as $history)
                            <li class="list-group-item">
                                <strong>{{ $history->name }}</strong>
                                <br>
                                <small class="text-muted">
                                    Mulai:
                                    {{ \Carbon\Carbon::parse($history->pivot->start_date)->translatedFormat('d M Y') }}
                                    @if ($history->pivot->end_date)
                                        - Selesai:
                                        {{ \Carbon\Carbon::parse($history->pivot->end_date)->translatedFormat('d M Y') }}
                                    @else
                                        <span class="badge badge-success float-right mt-1">Aktif</span>
                                    @endif
                                </small>
                            </li>
                        @empty
                            <li class="list-group-item">Belum ada riwayat pembimbing.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
@stop

@push('js')
    <script>
        $(document).ready(function() {
            // Inisialisasi Select2
            $('#pembimbing_select').select2({
                theme: 'bootstrap4',
                placeholder: "-- Pilih Dosen Pembimbing --",
                allowClear: true
            });
        });
    </script>
@endpush
