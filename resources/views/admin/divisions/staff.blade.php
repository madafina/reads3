@extends('adminlte::page')

@section('title', 'Kelola Staf & PJ Divisi')

@section('content_header')
    <h1 class="m-0 text-dark">Kelola Staf & PJ untuk Divisi: {{ $division->name }}</h1>
@stop

@push('css')
    <style>
        .dual-listbox-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .dual-listbox-container .list-box {
            width: 48%;
        }

        .dual-listbox-container .list-box select {
            width: 100%;
            height: 300px;
        }

        .dual-listbox-container .buttons {
            display: flex;
            flex-direction: column;
            justify-content: center;
            width: 4%;
        }

        .dual-listbox-container .buttons button {
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
                        {{-- DUAL LISTBOX UNTUK MEMILIH STAF --}}
                        <div class="dual-listbox-container">
                            <div class="list-box">
                                <h5>Dosen Tersedia</h5>
                                <select id="available-staff" multiple class="form-control">
                                    @foreach ($allLecturers as $lecturer)
                                        @if (!in_array($lecturer->id, $currentStaffIds))
                                            <option value="{{ $lecturer->id }}">{{ $lecturer->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <div class="buttons">
                                <button type="button" id="btn-add" class="btn btn-default">&gt;</button>
                                <button type="button" id="btn-remove" class="btn btn-default">&lt;</button>
                            </div>

                            <div class="list-box">
                                <h5>Staf Ditugaskan</h5>
                                <select id="assigned-staff" name="assigned_staff[]" multiple class="form-control"></select>
                            </div>
                        </div>

                        <hr>

                        {{-- DROPDOWN UNTUK MEMILIH PJ --}}
                        <div class="form-group">
                            <label for="pj_id">Pilih Penanggung Jawab (PJ)</label>
                            <select name="pj_id" id="pj_id_selector"
                                class="form-control @error('pj_id') is-invalid @enderror">
                                <option value="">-- Pilih PJ dari Staf yang Ditugaskan --</option>
                            </select>
                            @error('pj_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
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
            const availableBox = $('#available-staff');
            const assignedBox = $('#assigned-staff');
            const pjSelector = $('#pj_id_selector');
            const initialPjId = {{ $currentPjId ?? 'null' }};

            function updatePjSelector() {
                pjSelector.empty().append('<option value="">-- Pilih PJ --</option>');
                assignedBox.find('option').each(function() {
                    const value = $(this).val();
                    const text = $(this).text();
                    const option = new Option(text, value);

                    if (value == initialPjId) {
                        option.selected = true;
                    }

                    pjSelector.append(option);
                });
            }

            function moveOptions(from, to) {
                const selected = from.find('option:selected');

                // Cegah pemindahan PJ dari assigned ke available
                selected.each(function() {
                    if ($(this).val() == pjSelector.val() && from.is(assignedBox)) {
                        alert(
                            "Tidak bisa menghapus Penanggung Jawab (PJ) dari daftar staf. Pilih PJ lain terlebih dahulu.");
                    } else {
                        $(this).appendTo(to);
                    }
                });

                updatePjSelector();
            }

            // === Inisialisasi assigned staff ===
            @foreach ($allLecturers as $lecturer)
                @if (in_array($lecturer->id, $currentStaffIds))
                    assignedBox.append(new Option("{{ $lecturer->name }}", "{{ $lecturer->id }}"));
                @endif
            @endforeach

            updatePjSelector();

            if (initialPjId) {
                pjSelector.val(initialPjId);
            }

            $('#btn-add').click(() => moveOptions(availableBox, assignedBox));
            $('#btn-remove').click(() => moveOptions(assignedBox, availableBox));

            $('#staff-form').submit(function() {
                $('#assigned-staff option').prop('selected', true);
            });
        });
    </script>
@endpush
