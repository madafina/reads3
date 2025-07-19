<div>
    @section('title', 'Upload Ilmiah')
    @section('content_header')
        <h1 class="m-0 text-dark">Form Upload Ilmiah</h1>
    @stop

    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Masukkan Detail Ilmiah</h3>
        </div>
        <form wire:submit="save">
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
                    <label for="task_category_id">Kategori Ilmiah</label>
                    <select class="form-control @error('task_category_id') is-invalid @enderror" id="task_category_id" wire:model.defer="task_category_id">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach ($taskCategories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('task_category_id') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                {{-- Tampilkan field ini hanya jika di Tahap II --}}
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

                <div class="form-group">
                    <label for="supervisor_id">Dosen Pembimbing</label>
                    <select class="form-control @error('supervisor_id') is-invalid @enderror" id="supervisor_id" wire:model.defer="supervisor_id">
                        <option value="">-- Pilih Dosen --</option>
                        @foreach ($supervisors as $supervisor)
                            <option value="{{ $supervisor->id }}">{{ $supervisor->name }}</option>
                        @endforeach
                    </select>
                    @error('supervisor_id') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

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

                <div class="form-group">
                    <label for="file">Upload File (PDF, max 10MB)</label>
                    <input type="file" class="form-control-file @error('file') is-invalid @enderror" id="file" wire:model="file">
                    @error('file') <span class="text-danger">{{ $message }}</span> @enderror
                    
                    {{-- Progress bar upload --}}
                    <div wire:loading wire:target="file" class="mt-2">
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Uploading...</div>
                        </div>
                    </div>
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

</div>