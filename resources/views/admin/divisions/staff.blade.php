@extends('adminlte::page')

@section('title', 'Kelola Staf Divisi')

@section('content_header')
    <h1 class="m-0 text-dark">Kelola Staf untuk Divisi: {{ $division->name }}</h1>
@stop

@push('css')
    {{-- CSS untuk membuat tampilan dual listbox lebih baik --}}
    <style>
        .dual-listbox {
            display: flex;
            justify-content: space-between;
        }
        .dual-listbox .list-box {
            width: 45%;
        }
        .dual-listbox .list-box select {
            width: 100%;
            height: 300px;
        }
        .dual-listbox .buttons {
            display: flex;
            flex-direction: column;
            justify-content: center;
            width: 10%;
            padding: 0 10px;
        }
        .dual-listbox .buttons button {
            margin-bottom: 10px;
        }
    </style>
@endpush

@section('content')
    <form action="{{ route('admin.divisions.staff.update', $division->id) }}" method="POST" id="staff-form">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="dual-listbox">
                            {{-- KOLOM KIRI: Dosen yang Tersedia --}}
                            <div class="list-box">
                                <label>Dosen Tersedia</label>
                                <select id="available-staff" multiple class="form-control">
                                    @foreach ($allLecturers as $lecturer)
                                        {{-- Hanya tampilkan dosen yang BELUM menjadi staf --}}
                                        @if (!in_array($lecturer->id, $currentStaffIds))
                                            <option value="{{ $lecturer->id }}">{{ $lecturer->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            {{-- TOMBOL PEMINDAH --}}
                            <div class="buttons">
                                <button type="button" id="btn-add" class="btn btn-primary">&gt;</button>
                                <button type="button" id="btn-remove" class="btn btn-primary">&lt;</button>
                            </div>

                            {{-- KOLOM KANAN: Staf yang Ditugaskan --}}
                            <div class="list-box">
                                <label>Staf Ditugaskan untuk Divisi Ini</label>
                                {{-- Nama field ini yang akan dikirim ke controller --}}
                                <select id="assigned-staff" name="assigned_staff[]" multiple class="form-control">
                                    @foreach ($allLecturers as $lecturer)
                                        {{-- Hanya tampilkan dosen yang SUDAH menjadi staf --}}
                                        @if (in_array($lecturer->id, $currentStaffIds))
                                            <option value="{{ $lecturer->id }}">{{ $lecturer->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        <a href="{{ route('admin.divisions.index') }}" class="btn btn-default">Batal</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
@stop

@push('js')
    <script>
        $(document).ready(function() {
            // Fungsi untuk memindahkan option
            function moveOptions(from, to) {
                $(from).find('option:selected').appendTo(to);
            }

            // Event handler untuk tombol
            $('#btn-add').click(function() {
                moveOptions('#available-staff', '#assigned-staff');
            });

            $('#btn-remove').click(function() {
                moveOptions('#assigned-staff', '#available-staff');
            });
            
            // PENTING: Saat form akan disubmit, kita harus memilih semua option di kolom kanan
            // agar nilainya ikut terkirim ke controller.
            $('#staff-form').submit(function() {
                $('#assigned-staff option').prop('selected', true);
            });
        });
    </script>
@endpush