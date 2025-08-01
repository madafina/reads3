<div>
    @push('css')
        <style>
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

    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Masukkan Detail Tugas</h3>
        </div>
        <form wire:submit.prevent="save">
            <div class="card-body">

                @if (session()->has('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="form-group">
                    <label for="title">Judul Ilmiah</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" wire:model.defer="title" placeholder="Masukkan judul">
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
                        <option value="">-- Pilih Kategori --</option>
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
                    <label for="supervisor_id_select">Dosen Pembimbing</label>
                    <select class="form-control" id="supervisor_id_select" style="width: 100%;">
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
                    <input type="number" step="0.01" class="form-control @error('grade') is-invalid @enderror" id="grade" wire:model.defer="grade" placeholder="Masukkan nilai jika ada">
                    @error('grade') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <hr>

                <div class="form-group">
                    <label for="file">File Ilmiah Utama (PDF, Wajib)</label>
                    <input type="file" class="form-control-file @error('file') is-invalid @enderror" id="file" wire:model="file">
                    @error('file') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="presentation_file">File Presentasi (Opsional)</label>
                    <input type="file" class="form-control-file @error('presentation_file') is-invalid @enderror" id="presentation_file" wire:model="presentation_file">
                    @error('presentation_file') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="grade_file">File Bukti Nilai (Opsional)</label>
                    <input type="file" class="form-control-file @error('grade_file') is-invalid @enderror" id="grade_file" wire:model="grade_file">
                    @error('grade_file') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="attendance_file">File Bukti Presensi (Opsional)</label>
                    <input type="file" class="form-control-file @error('attendance_file') is-invalid @enderror" id="attendance_file" wire:model="attendance_file">
                    @error('attendance_file') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove>Submit</span>
                    <span wire:loading>Menyimpan...</span>
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {
            $('#supervisor_id_select').select2({
                theme: 'bootstrap4',
                placeholder: "Cari dan pilih nama dosen",
                allowClear: true
            });

            $('#supervisor_id_select').on('change', function (e) {
                var data = $(this).val();
                @this.set('supervisor_id', data);
            });
        });
    </script>
</div>
