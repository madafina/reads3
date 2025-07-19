<div>
    {{-- Atur Judul Halaman --}}
    @section('title', 'Dashboard Residen')
    @section('content_header')
        <h1 class="m-0 text-dark">Halo, {{ auth()->user()->name; }}</h1>
    @stop

    @section('content')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Tahap Saat Ini: <strong>{{ $resident->currentStage->name ?? 'Belum ada tahap' }}</strong></h5>
                    <p class="card-text">Berikut adalah progres penyelesaian kewajiban ilmiah Anda pada tahap ini.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        @forelse ($rules as $rule)
            <div class="col-md-4">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-{{ $completionData[$rule->id]['is_completed'] ? 'success' : 'warning' }} elevation-1">
                        <i class="fas fa-file-alt"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">{{ $rule->name }}</span>
                        <span class="info-box-number">
                            {{ $completionData[$rule->id]['completed'] }} / {{ $completionData[$rule->id]['required'] }}
                        </span>
                        <div class="progress">
                            @php
                                $percentage = ($rule->required_count > 0) ? ($completionData[$rule->id]['completed'] / $rule->required_count) * 100 : 0;
                            @endphp
                            <div class="progress-bar" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <p>Belum ada aturan kewajiban yang diatur untuk tahap ini.</p>
            </div>
        @endforelse
    </div>

    @stop
</div>