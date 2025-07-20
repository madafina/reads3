@extends('adminlte::page')

@section('title', 'Edit Profil')

@section('content_header')
    <h1 class="m-0 text-dark">Edit Profil</h1>
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        {{-- KARTU EDIT PROFIL --}}
        <div class="col-md-6">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Informasi Profil</h3>
                </div>
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="update_profile" value="1">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}">
                            @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label for="email">Alamat Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}">
                            @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        @if($user->resident)
                        <div class="form-group">
                            <label for="nim">NIM</label>
                            <input type="text" name="nim" class="form-control @error('nim') is-invalid @enderror" value="{{ old('nim', $user->resident->nim) }}">
                            @error('nim') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label for="phone_number">Nomor Telepon</label>
                            <input type="text" name="phone_number" class="form-control @error('phone_number') is-invalid @enderror" value="{{ old('phone_number', $user->resident->phone_number) }}">
                            @error('phone_number') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        @endif
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Simpan Profil</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-6">
            {{-- KARTU UPLOAD FOTO --}}
            <div class="card card-secondary">
                <div class="card-header">
                    <h3 class="card-title">Foto Profil</h3>
                </div>
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="update_photo" value="1">
                    <div class="card-body text-center">
                        {{-- PERBAIKAN: Tambahkan ID pada gambar untuk pratinjau --}}
                        <img src="{{ $user->adminlte_image() }}" id="photo-preview" class="img-circle elevation-2 mb-3" alt="User Image" style="width: 150px; height: 150px; object-fit: cover;">
                        <div class="form-group">
                            <div class="custom-file">
                                {{-- PERBAIKAN: Tambahkan ID pada input file --}}
                                <input type="file" name="photo" class="custom-file-input @error('photo') is-invalid @enderror" id="photo-input">
                                <label class="custom-file-label" for="photo-input">Pilih file baru...</label>
                            </div>
                            @error('photo') <span class="text-danger d-block mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Upload Foto</button>
                    </div>
                </form>
            </div>

            {{-- KARTU GANTI PASSWORD --}}
            <div class="card card-danger">
                <div class="card-header">
                    <h3 class="card-title">Ganti Password</h3>
                </div>
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="update_password" value="1">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="current_password">Password Saat Ini</label>
                            <input type="password" name="current_password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror">
                            @error('current_password', 'updatePassword') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label for="password">Password Baru</label>
                            <input type="password" name="password" class="form-control @error('password', 'updatePassword') is-invalid @enderror">
                             @error('password', 'updatePassword') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label for="password_confirmation">Konfirmasi Password Baru</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Ganti Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

{{-- PERBAIKAN: Tambahkan skrip JavaScript di sini --}}
@push('js')
<script>
    $(document).ready(function() {
        // Tampilkan nama file di label saat file dipilih
        $('#photo-input').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').html(fileName);

            // Tampilkan pratinjau gambar
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#photo-preview').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        });
    });
</script>
@endpush
