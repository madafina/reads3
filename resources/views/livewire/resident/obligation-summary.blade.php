<div>
    @foreach ($stages as $stage)
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h3 class="card-title font-weight-bold mb-0">{{ $stage->name }}</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th style="width: 45%">Nama Kewajiban</th>
                            <th style="width: 20%">Progres</th>
                            <th style="width: 20%">Status</th>
                            <th style="width: 15%" class="text-center">Aksi</th> {{-- Header baru --}}
                        </tr>
                    </thead>
                    <tbody>
                        @if ($stage->name === 'Tahap II')
                            @php
                                $specificRulesByDivisionId = $stage->requirementRules->whereNotNull('division_id')->groupBy('division_id');
                                $standardRules = $stage->requirementRules->whereNull('division_id');
                            @endphp

                            @foreach ($allDivisions as $division)
                                <tr>
                                    <td colspan="4" class="bg-light">
                                        <strong class="ml-3">
                                            <i class="fas fa-hospital-alt mr-2"></i>
                                            Divisi {{ $division->name }}
                                        </strong>
                                    </td>
                                </tr>
                                
                                @php
                                    $rulesToShow = $specificRulesByDivisionId[$division->id] ?? $standardRules;
                                @endphp

                                @forelse ($rulesToShow as $rule)
                                    {{-- Kirim currentStageId ke partial --}}
                                    @include('livewire.resident.partials.rule-row', ['rule' => $rule, 'currentStageId' => $currentStageId])
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted pl-5"><i>Tidak ada kewajiban yang diatur untuk divisi ini.</i></td>
                                    </tr>
                                @endforelse
                            @endforeach

                        @else
                            @forelse ($stage->requirementRules as $rule)
                                {{-- Kirim currentStageId ke partial --}}
                                @include('livewire.resident.partials.rule-row', ['rule' => $rule, 'currentStageId' => $currentStageId])
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Belum ada aturan kewajiban untuk tahap ini.</td>
                                </tr>
                            @endforelse
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
</div>