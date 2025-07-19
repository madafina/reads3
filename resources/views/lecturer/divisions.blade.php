@extends('adminlte::page')
@section('title', 'Divisi')
@section('content_header')
    <h1 class="m-0 text-dark">Divisi Saya</h1>
@stop
@section('content')
    @forelse ($divisions as $division)
        <div class="card">
            <div class="card-header">
                <h3 class="card-title font-weight-bold">Divisi {{ $division->name }}</h3>
            </div>
            <div class="card-body">
                <strong>Staf Divisi:</strong>
                <ul>
                    @foreach($division->staff as $staff)
                        <li>{{ $staff->name }} @if($staff->pivot->is_pj) <strong>(PJ)</strong> @endif</li>
                    @endforeach
                </ul>
                <hr>
              
                <table class="table table-bordered">
                    <thead>
                        <tr><th>Nama Residen</th><th>NIM</th></tr>
                    </thead>
                    <tbody>
                        @forelse($division->residents as $resident)
                            <tr>
                                <td>{{ $resident->user->name }}</td>
                                <td>{{ $resident->nim }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2">Tidak ada residen di divisi ini saat ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @empty
        <p>Anda belum ditugaskan ke divisi manapun.</p>
    @endforelse
@stop
