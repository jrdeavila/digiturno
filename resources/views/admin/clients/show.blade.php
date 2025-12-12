@extends('adminlte::page')

@section('title', 'Detalles del cliente')

@section('content_header')
    <div class="d-flex align-items-center justify-content-between">
        <h1 class="mb-0">Detalles del cliente {{ $client->name }}</h1>
        <div class="d-flex">
            <a href="{{ route('clients.index') }}" class="btn btn-outline-primary mr-2">
                <i class="fas fa-arrow-left mr-1"></i> Volver
            </a>
            <a href="{{ route('clients.edit', $client) }}" class="btn btn-primary">
                <i class="fas fa-edit mr-1"></i> Editar
            </a>
        </div>
    </div>
@stop

@section('content')
    @php
        $stateMap = [
            'pending' => ['bg' => 'bg-info', 'icon' => 'fas fa-hourglass-half', 'label' => 'Pendiente'],
            'in_progress' => ['bg' => 'bg-warning', 'icon' => 'fas fa-hourglass-start', 'label' => 'En progreso'],
            'finished' => ['bg' => 'bg-success', 'icon' => 'fas fa-hourglass-end', 'label' => 'Finalizado'],
            'cancelled' => ['bg' => 'bg-danger', 'icon' => 'fas fa-times', 'label' => 'Cancelado'],
            'transferred' => ['bg' => 'bg-warning', 'icon' => 'fas fa-exchange-alt', 'label' => 'Transferido'],
            'pending-transferred' => [
                'bg' => 'bg-info',
                'icon' => 'fas fa-hourglass-half',
                'label' => 'Transferido pendiente',
            ],
            'qualified' => ['bg' => 'bg-success', 'icon' => 'fas fa-check', 'label' => 'Calificado'],
            'distracted' => ['bg' => 'bg-danger', 'icon' => 'fas fa-times', 'label' => 'Distraído'],
        ];

        // Opcional: paginar por servidor para mejorar rendimiento en clientes con MUY muchos turnos
        // $paginatedShifts = $client->shifts()->orderBy('created_at', 'desc')->paginate(50);
        // $groupedShifts = $paginatedShifts->getCollection()->groupBy(fn($s) => $s->created_at->format('Y-m-d'));

        // Versión actual: todo el histórico agrupado
        $groupedShifts = $client
            ->shifts()
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(fn($s) => $s->created_at->format('Y-m-d'));
    @endphp

    <div class="row">
        <div class="col-md-4">
            <x-adminlte-card title="Detalle del cliente" theme="primary" icon="fas fa-user" class="elevation-1">
                <x-adminlte-input name="name" label="Nombre" value="{{ $client->name }}" disabled />
                <x-adminlte-input name="dni" label="Documento de identidad"
                    value="{{ number_format($client->dni, 0, '', '.') }}" disabled />
                <x-adminlte-input name="client_type" label="Tipo de cliente"
                    value="{{ optional($client->clientType)->name }}" disabled />
                <div class="d-flex justify-content-end">
                    <a href="{{ route('clients.edit', $client) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit mr-1"></i> Editar
                    </a>
                </div>
            </x-adminlte-card>
        </div>

        <div class="col-md-8">
            <x-adminlte-card title="Histórico de turnos" theme="light" icon="fas fa-stream" class="elevation-1">
                <div class="timeline" style="max-height: calc(100vh - 240px); overflow-y: auto; padding-right: 6px;">
                    @forelse ($groupedShifts as $date => $shifts)
                        <div class="time-label my-2">
                            <span class="bg-primary px-2 py-1">
                                {{ \Carbon\Carbon::parse($date)->translatedFormat('l j \d\e F \d\e\l Y') }}
                            </span>
                        </div>

                        @foreach ($shifts->sortBy('created_at') as $item)
                            @php
                                $def = $stateMap[$item->state] ?? [
                                    'bg' => 'bg-secondary',
                                    'icon' => 'fas fa-info',
                                    'label' => ucfirst($item->state),
                                ];
                                $qualification = optional($item->qualification)->qualification;
                                $module = $item->module;
                                $responsable = optional(optional($module)->responsable)->employee;
                                $services = $item->services->map(fn($s) => $s->name)->implode(', ');
                            @endphp
                            <div>
                                <i class="{{ $def['icon'] }} {{ $def['bg'] }}"></i>
                                <div class="timeline-item">
                                    <span class="time">
                                        <i class="fas fa-clock"></i>
                                        {{ $item->created_at->format('H:i') }}
                                    </span>
                                    <h3 class="timeline-header">
                                        <a href="{{ route('shifts.show', $item) }}">
                                            {{ $def['label'] }}
                                        </a>
                                        @if ($qualification)
                                            <span class="badge badge-success ml-2">{{ $qualification }}</span>
                                        @endif
                                    </h3>
                                    <div class="timeline-body">
                                        @if ($responsable)
                                            <div class="mb-1">
                                                <i class="fas fa-user-check text-success mr-1"></i>
                                                Atendido por {{ $responsable->full_name }}
                                            </div>
                                        @elseif ($module)
                                            <div class="mb-1">
                                                <i class="fas fa-desktop text-info mr-1"></i>
                                                Atendido por el módulo {{ $module->name }}
                                            </div>
                                        @endif

                                        @if ($services)
                                            <div class="mt-2">
                                                <strong>Servicios</strong><br>
                                                <span class="text-muted">{{ $services }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @empty
                        <div class="text-muted p-3">
                            Sin turnos registrados para este cliente.
                        </div>
                    @endforelse
                </div>

                {{-- Si activas la paginación por servidor, muestra los links aquí --}}
                {{-- <div class="mt-3 d-flex justify-content-end">
                    {{ $paginatedShifts->links('custom.pagination') }}
                </div> --}}
            </x-adminlte-card>
        </div>
    </div>
@stop

@push('js')
    <script>
        $(function() {
            $('#edit-client').on('click', function() {
                window.location.href = "{{ route('clients.edit', $client) }}";
            });
        });
    </script>
@endpush
