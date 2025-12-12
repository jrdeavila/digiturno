@extends('adminlte::page')

@section('title', 'Detalle del turno')

@section('content_header')
    <div class="d-flex align-items-center justify-content-between">
        <h1 class="mb-0">Detalle del turno</h1>
        <div class="d-flex">
            <a href="{{ route('shifts.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left mr-1"></i> Volver a listados
            </a>
        </div>
    </div>
@stop

@section('content')
    @php
        $stateMap = [
            'pending' => ['color' => 'bg-info', 'icon' => 'fas fa-hourglass-half', 'text' => 'Pendiente'],
            'in_progress' => ['color' => 'bg-warning', 'icon' => 'fas fa-hourglass-start', 'text' => 'En progreso'],
            'finished' => ['color' => 'bg-success', 'icon' => 'fas fa-hourglass-end', 'text' => 'Finalizado'],
            'cancelled' => ['color' => 'bg-danger', 'icon' => 'fas fa-times', 'text' => 'Cancelado'],
            'transferred' => ['color' => 'bg-warning', 'icon' => 'fas fa-exchange-alt', 'text' => 'Transferido'],
            'pending-transferred' => [
                'color' => 'bg-info',
                'icon' => 'fas fa-hourglass-half',
                'text' => 'Transferido pendiente',
            ],
            'qualified' => ['color' => 'bg-success', 'icon' => 'fas fa-check', 'text' => 'Calificado'],
            'distracted' => ['color' => 'bg-danger', 'icon' => 'fas fa-times', 'text' => 'Distraído'],
        ];

        // Cálculo de tiempos usando ShiftHistory
        // Obtenemos el primer registro de in_progress y el primer de qualified
        $firstInProgress = $shift->histories()->where('state', 'in_progress')->orderBy('created_at', 'asc')->first();

        $firstQualified = $shift->histories()->where('state', 'qualified')->orderBy('created_at', 'asc')->first();

        // Tiempo de espera: desde created_at (Shift) hasta primer in_progress
        $waitingMinutes = null;
        if ($firstInProgress) {
            $waitingMinutes = \Carbon\Carbon::parse($shift->created_at)->diffInMinutes($firstInProgress->created_at);
        }

        // Tiempo de atención: desde primer in_progress hasta primer qualified
        $attentionMinutes = null;
        if (
            $firstInProgress &&
            $firstQualified &&
            $firstQualified->created_at->greaterThan($firstInProgress->created_at)
        ) {
            $attentionMinutes = \Carbon\Carbon::parse($firstInProgress->created_at)->diffInMinutes(
                $firstQualified->created_at,
            );
        }
    @endphp

    <div class="row">
        <div class="col-md-4">
            <div class="d-flex flex-column">
                @if ($shift->services->count() > 0)
                    <x-adminlte-card title="Servicios prestados ({{ $shift->services->count() }})" theme="light"
                        icon="fas fa-concierge-bell" class="elevation-1">
                        <div class="d-flex flex-wrap">
                            @foreach ($shift->services as $service)
                                <span class="badge badge-primary mr-2 mb-2">{{ $service->name }}</span>
                            @endforeach
                        </div>
                    </x-adminlte-card>
                @endif
                @if ($shift->module)
                    <x-adminlte-card title="Detalle del módulo" theme="light" icon="fas fa-desktop" class="elevation-1">
                        <x-adminlte-input name="name" label="Nombre" value="{{ $shift->module->name }}" disabled />
                        <x-adminlte-input name="room" label="Sala" value="{{ $shift->module->room?->name }}"
                            disabled />
                        <x-adminlte-input name="branch" label="Seccional"
                            value="{{ optional($shift->module->room)->branch->name }}" disabled />
                        <x-adminlte-input name="client_type" label="Tipo de cliente"
                            value="{{ optional($shift->module->clientType)->name }}" disabled />
                        <x-adminlte-input name="module_type" label="Tipo de módulo"
                            value="{{ optional($shift->module->moduleType)->name }}" disabled />
                        @if (optional($shift->module)->responsable)
                            <x-adminlte-input name="responsable" label="Responsable"
                                value="{{ optional($shift->module->responsable->employee)->full_name }}" disabled />
                        @endif
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('modules.show', $shift->module) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-eye mr-1"></i> Ver detalles
                            </a>
                        </div>
                    </x-adminlte-card>
                @endif
            </div>

        </div>

        <div class="col-md-4">
            <div class="d-flex flex-column">
                {{-- Resumen del estado y tiempos del turno --}}
                @php
                    $currentState = $stateMap[$shift->state] ?? null;
                @endphp
                <x-adminlte-card title="Estado y tiempos del turno" theme="light" icon="fas fa-info-circle"
                    class="elevation-1">
                    <div class="d-flex flex-column align-items-start">
                        @if ($currentState)
                            <span class="text-muted mr-3 mb-2">
                                Creado:
                                {{ \Carbon\Carbon::parse($shift->created_at)->translatedFormat('j \d\e F \d\e\l Y H:i') }}
                            </span>
                            <div
                                class="d-inline-flex align-items-center {{ $currentState['color'] }} px-3 py-2 rounded mr-3 mb-2">
                                <i class="{{ $currentState['icon'] }} mr-2"></i>
                                <strong>{{ $currentState['text'] }}</strong>
                            </div>
                        @else
                            <span class="text-muted mr-3 mb-2">Estado no disponible</span>
                        @endif

                        <span class="text-muted mr-3 mb-2">
                            Los tiempos de espera y atención son calculados desde el momento en que el paciente
                            llega a la sala.
                        </span>

                        {{-- Tiempo de espera --}}
                        <div class="d-inline-flex align-items-center px-3 py-2 rounded bg-light border mr-3 mb-2">
                            <i class="fas fa-hourglass mr-2 text-info"></i>
                            <strong>Espera:</strong>
                            <span class="ml-2">
                                @if (!is_null($waitingMinutes))
                                    {{ ceil($waitingMinutes / 5) }} min
                                @else
                                    — <span class="text-muted">(sin registro de inicio)</span>
                                @endif
                            </span>
                        </div>

                        {{-- Tiempo de atención --}}
                        <div class="d-inline-flex align-items-center px-3 py-2 rounded bg-light border mb-2">
                            <i class="fas fa-stopwatch mr-2 text-success"></i>
                            <strong>Atención:</strong>
                            <span class="ml-2">
                                @if (!is_null($attentionMinutes))
                                    {{ ceil($attentionMinutes / 5) }} min
                                @else
                                    — <span class="text-muted">(sin calificación o inicio)</span>
                                @endif
                            </span>
                        </div>
                    </div>
                </x-adminlte-card>

                <x-adminlte-card title="Detalle del cliente" theme="light" icon="fas fa-user" class="elevation-1">
                    <x-adminlte-input name="name" label="Nombre" value="{{ $shift->client->name }}" disabled />
                    <x-adminlte-input name="dni" label="Documento de identidad"
                        value="{{ number_format($shift->client->dni, 0, '', '.') }}" disabled />
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('clients.show', $shift->client) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-eye mr-1"></i> Ver detalles
                        </a>
                    </div>
                </x-adminlte-card>


                <x-adminlte-card title="Detalle de la sala" theme="light" icon="fas fa-door-open" class="elevation-1">
                    <x-adminlte-input name="name" label="Nombre" value="{{ $shift->room->name }}" disabled />
                    <x-adminlte-input name="branch" label="Seccional" value="{{ $shift->room->branch->name }}" disabled />
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('rooms.show', $shift->room) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-eye mr-1"></i> Ver detalles
                        </a>
                    </div>
                </x-adminlte-card>


            </div>

        </div>

        {{-- Columna derecha: timeline con trazabilidad --}}
        <div class="col-lg-4">
            <div class="d-flex flex-column">
                <x-adminlte-card title="Trazabilidad del turno" theme="light" icon="fas fa-stream" class="elevation-1">
                    @php
                        // Historial del día del turno
                        $groupedShiftHistories = $shift
                            ->histories()
                            ->whereDate('created_at', '>=', $shift->created_at->copy()->startOfDay())
                            ->whereDate('created_at', '<=', $shift->created_at->copy()->endOfDay())
                            ->orderBy('created_at', 'desc')
                            ->get()
                            ->groupBy(fn($s) => $s->created_at->format('Y-m-d'));
                    @endphp

                    <div class="timeline">
                        @foreach ($groupedShiftHistories as $date => $histories)
                            <div class="time-label my-2">
                                <span class="bg-primary px-2 py-1">
                                    {{ \Carbon\Carbon::parse($date)->translatedFormat('l j \d\e F \d\e\l Y') }}
                                </span>
                            </div>

                            @foreach ($histories->sortBy('created_at') as $item)
                                @php
                                    $def = [
                                        'color' => 'bg-' . $item->getStateColor(),
                                        'icon' => $item->getStateIcon(),
                                        'text' => $item->getStateLabel(),
                                    ];
                                    // El historial no es el shift, no se resalta por ID de shift, pero podemos resaltar el último estado del shift
                                    $isCurrent =
                                        $item->state === $shift->state &&
                                        $item->created_at->equalTo($shift->updated_at);
                                @endphp
                                <div>
                                    <i class="{{ $def['icon'] }} {{ $def['color'] }}"></i>
                                    <div class="timeline-item {{ $isCurrent ? 'border-primary' : '' }}"
                                        style="{{ $isCurrent ? 'border: 2px solid #007bff; background-color: #f8fbff;' : '' }}">
                                        <span class="time">
                                            <i class="fas fa-clock"></i>
                                            {{ $item->created_at->format('H:i') }}
                                        </span>
                                        <h3 class="timeline-header">
                                            <span class="text-primary">{{ $def['text'] }}</span>
                                            @if (optional($item->qualification)->qualification)
                                                <span
                                                    class="badge badge-success ml-2">{{ $item->qualification->qualification }}</span>
                                            @endif
                                        </h3>
                                        <div class="timeline-body">
                                            @if (optional($item->shift->module)->responsable)
                                                <div class="mb-1">
                                                    <i class="fas fa-user-check text-success mr-1"></i>
                                                    Atendido por
                                                    {{ optional($item->shift->module)->responsable->employee->full_name }}
                                                </div>
                                            @elseif ($item->shift->module)
                                                <div class="mb-1">
                                                    <i class="fas fa-desktop text-info mr-1"></i>
                                                    Atendido por el módulo {{ optional($item->shift->module)->name }}
                                                </div>
                                            @else
                                                <div class="mb-1 text-muted">
                                                    <i class="fas fa-question-circle mr-1"></i>
                                                    Módulo no asociado
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            {{-- Si el turno esta calificado, entonces terminar la linea de tiempo --}}
                            @if ($shift->state === \App\Enums\ShiftState::Qualified->value)
                                <div>
                                    <i class="fas fa-check-circle bg-success"></i>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </x-adminlte-card>

            </div>

        </div>


    </div>
@stop

@push('js')
    <script>
        $(function() {
            $('#show-client').on('click', function() {
                window.location.href = "{{ route('clients.show', $shift->client) }}";
            });
            $('#show-room').on('click', function() {
                window.location.href = "{{ route('rooms.show', $shift->room) }}";
            });
            $('#show-module').on('click', function() {
                window.location.href = "{{ route('modules.show', $shift->module) }}";
            });
        });
    </script>
@endpush
