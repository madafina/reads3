<div>
    @push('css')
        <style>
            /* Perbaikan untuk menyamakan tinggi dan border Select2 dengan form input lain */
            .select2-container .select2-selection--single  {
                height: calc(2.25rem + 2px) !important;
                border:1px solid #ced4da;
                border-radius:4px;
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

    <div class="card card-warning">
        <div class="card-header">
            <h3 class="card-title">Edit Detail Tugas</h3>
        </div>
        <form wire:submit.prevent="update">
            <div class="card-body">
                
                <div class="form-group">
                    <label for="title">Judul Ilmiah</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" wire:model.defer="title">
                    @error('title') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="description">Deskripsi (Opsional)</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" wire:model.defer="description" rows="3"></textarea>
                    @error('description') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="seminar_title">Judul Seminar (Jika berbeda, opsional)</label>
                    <input type="text" class="form-control @error('seminar_title') is-invalid @enderror" id="seminar_title" wire:model.defer="seminar_title">
                    @error('seminar_title') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="task_category_id">Kategori Tugas</label>
                    <select class="form-control @error('task_category_id') is-invalid @enderror" id="task_category_id" wire:model.defer="task_category_id">
                        @foreach ($taskCategories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('task_category_id') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                @if ($showDivisionField)
                    <div class="form-group">
                        <label for="division_id">Divisi</label>
                        <select class="form-control @error('division_id') is-invalid @enderror" id="division_id" wire:model.defer="division_id">
                            <option value="">-- Pilih Divisi --</option>
                            @foreach ($divisions as $division)
                                <option value="{{ $division->id }}">{{ $division->name }}</option>
                            @endforeach
                        </select>
                        @error('division_id') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                @endif

                <div class="form-group" wire:ignore>
                    <label for="supervisor_id_select_edit">Dosen Pembimbing</label>
                    <select class="form-control" id="supervisor_id_select_edit" style="width: 100%;">
                        <option value="">-- Pilih Dosen --</option>
                        @foreach ($supervisors as $supervisor)
                            <option value="{{ $supervisor->id }}">{{ $supervisor->name }}</option>
                        @endforeach
                    </select>
                </div>
                @error('supervisor_id') <span class="text-danger">{{ $message }}</span> @enderror

                <div class="form-group">
                    <label for="presentation_date">Tanggal Maju / Sidang</label>
                    <input type="date" class="form-control @error('presentation_date') is-invalid @enderror" id="presentation_date" wire:model.defer="presentation_date">
                    @error('presentation_date') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="grade">Nilai (Opsional)</label>
                    <input type="number" step="0.01" class="form-control @error('grade') is-invalid @enderror" id="grade" wire:model.defer="grade">
                    @error('grade') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <hr>

                <div class="form-group">
                    <label for="newFile">Ganti File Ilmiah Utama (Opsional)</label>
                    @if($submission->file_path)
                        <p class="text-muted mb-1">File saat ini: <a href="{{ asset('storage/' . $submission->file_path) }}" target="_blank">Lihat File</a></p>
                    @endif
                    <input type="file" class="form-control-file @error('newFile') is-invalid @enderror" id="newFile" wire:model="newFile">
                    @error('newFile') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="new_presentation_file">Ganti File Presentasi (Opsional)</label>
                    @if($submission->presentation_file_path)
                        <p class="text-muted mb-1">File saat ini: <a href="{{ asset('storage/' . $submission->presentation_file_path) }}" target="_blank">Lihat File</a></p>
                    @endif
                    <input type="file" class="form-control-file @error('new_presentation_file') is-invalid @enderror" id="new_presentation_file" wire:model="new_presentation_file">
                    @error('new_presentation_file') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="new_grade_file">Ganti File Bukti Nilai (Opsional)</label>
                     @if($submission->grade_file_path)
                        <p class="text-muted mb-1">File saat ini: <a href="{{ asset('storage/' . $submission->grade_file_path) }}" target="_blank">Lihat File</a></p>
                    @endif
                    <input type="file" class="form-control-file @error('new_grade_file') is-invalid @enderror" id="new_grade_file" wire:model="new_grade_file">
                    @error('new_grade_file') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="new_attendance_file">Ganti File Bukti Presensi (Opsional)</label>
                     @if($submission->attendance_file_path)
                        <p class="text-muted mb-1">File saat ini: <a href="{{ asset('storage/' . $submission->attendance_file_path) }}" target="_blank">Lihat File</a></p>
                    @endif
                    <input type="file" class="form-control-file @error('new_attendance_file') is-invalid @enderror" id="new_attendance_file" wire:model="new_attendance_file">
                    @error('new_attendance_file') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="{{ route('submissions.history') }}" class="btn btn-default">Batal</a>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {
            // Inisialisasi Select2
            $('#supervisor_id_select_edit').select2({
                theme: 'bootstrap4',
                placeholder: "Cari dan pilih nama dosen",
                allowClear: true
            });

            // Set nilai awal Select2 dari data Livewire
            $('#supervisor_id_select_edit').val(@this.get('supervisor_id')).trigger('change');

            // Saat nilai di Select2 berubah, kirim nilainya ke properti Livewire
            $('#supervisor_id_select_edit').on('change', function (e) {
                var data = $(this).val();
                @this.set('supervisor_id', data);
            });
        });
    </script>
</div>
