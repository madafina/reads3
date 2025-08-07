@extends('adminlte::page')

@section('title', 'Impor Dosen')

@section('content_header')
    <h1 class="m-0 text-dark">Impor Data Dosen</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            {{-- Tampilkan error validasi dari Excel jika ada --}}
            @if (session()->has('import_errors'))
                <div class="alert alert-danger">
                    <h5><i class="icon fas fa-ban"></i> Impor Gagal!</h5>
                    Ada beberapa kesalahan pada file Excel Anda:
                    <ul>
                        @foreach (session()->get('import_errors') as $failure)
                            <li>
                                <strong>Baris ke-{{ $failure->row() }}:</strong> {{ $failure->errors()[0] }}
                                (Nilai yang diberikan: '{{ $failure->values()[$failure->attribute()] ?? '' }}')
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.lecturers.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="file">Pilih File Excel (.xlsx atau .xls)</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="file" name="file" required>
                                    <label class="custom-file-label" for="file">Pilih file...</label>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Impor Data</button>
                        <a href="{{ route('admin.lecturers.index') }}" class="btn btn-default">Batal</a>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Instruksi dan Template</h3>
                </div>
                <div class="card-body">
                    <p>Pastikan file Excel Anda memiliki kolom header sebagai berikut (huruf kecil semua):</p>
                    <ul>
                        <li><code>nama</code> - Nama lengkap dosen (wajib).</li>
                        <li><code>email</code> - Alamat email unik (wajib).</li>
                        <li><code>nidn</code> - Nomor Induk Dosen Nasional (opsional, unik jika diisi).</li>
                    </ul>
                    <p>Password default untuk semua pengguna baru adalah: <strong>123456</strong></p>
                     <a href="{{ asset('dist/import_dosen.xlsx') }}" class="btn btn-warning">Download sampel Excel</a>
                </div>
            </div>
        </div>
    </div>
@stop

@push('js')
<script>
    // Tampilkan nama file di label saat file dipilih
    $('.custom-file-input').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').html(fileName);
    });
</script>
@endpush
