@extends('adminlte::page')

@section('title', 'Tambah Kategori Ilmiah')

@section('content_header')
    <h1 class="m-0 text-dark">Tambah Kategori Ilmiah Baru</h1>
@stop

@section('content')
    <form action="{{ route('admin.task-categories.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">Nama Kategori</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required>
                            @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label for="description">Deskripsi (Opsional)</label>
                            <textarea name="description" class="form-control">{{ old('description') }}</textarea>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ route('admin.task-categories.index') }}" class="btn btn-default">Batal</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
@stop