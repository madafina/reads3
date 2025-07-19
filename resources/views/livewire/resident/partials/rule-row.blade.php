<tr>
    <td>
        {{-- Menampilkan Nama Aturan Kewajiban --}}
        {{ $rule->name }}
        {{-- Menampilkan kategori tugas apa saja yang bisa memenuhi aturan ini --}}
        <small class="d-block text-muted">
            Kategori: {{ $rule->taskCategories->pluck('name')->implode(' / ') }}
        </small>
    </td>
    <td>
        @php
            // Menghitung progres
            $completed = $rule->completed_count;
            $required = $rule->required_count;
            $isCompleted = $completed >= $required;
            // Menghitung persentase untuk progress bar
            $percentage = $required > 0 ? ($completed / $required) * 100 : 0;
            if ($percentage > 100) $percentage = 100; // Batasi maksimal 100%
        @endphp
        {{-- Menampilkan progres dalam format "Selesai / Wajib" --}}
        <strong>{{ $completed }} / {{ $required }}</strong>
        <div class="progress progress-xs">
            {{-- Progress bar yang lebarnya sesuai persentase --}}
            <div class="progress-bar {{ $isCompleted ? 'bg-success' : 'bg-warning' }}" style="width: {{ $percentage }}%"></div>
        </div>
    </td>
    <td>
        {{-- Menampilkan status "Lengkap" atau "Belum Lengkap" --}}
        @if ($isCompleted)
            <span class="badge badge-success">Lengkap</span>
        @else
            <span class="badge badge-warning">Belum Lengkap</span>
        @endif
    </td>
    <td class="text-center">
        {{-- Tombol hanya muncul jika residen berada di tahap yang sesuai & kewajiban belum lengkap --}}
        @if ($rule->stage_id == $currentStageId && !$isCompleted)
            <a href="{{ route('submissions.create', ['task_category_id' => $rule->taskCategories->first()->id, 'division_id' => $rule->division_id]) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-upload mr-1"></i> Upload
            </a>
        @endif
    </td>
</tr>
