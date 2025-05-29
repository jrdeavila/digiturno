@extends('adminlte::page')

@section('title', 'Detalle del turno')

@section('content_header')
    <h1>Detalle del turno</h1>
@stop

@section('content')
    <div class="row">

        <div class="col-md-8 row">
            <div class="col-md-6">
                <x-adminlte-card title="Detalle de la sala" theme="light" icon="fas fa-edit">
                    <x-adminlte-input name="name" label="Nombre" value="{{ $shift->room->name }}" disabled />
                    <x-adminlte-input name="branch" label="Seccional" value="{{ $shift->room->branch->name }}" disabled />
                    <x-adminlte-button label="Ver detalles" theme="primary" icon="fas fa-eye" id="show-room" />
                </x-adminlte-card>
            </div>
            <div class="col-md-6">
                <x-adminlte-card title="Detalle del cliente" theme="light" icon="fas fa-edit">
                    <x-adminlte-input name="name" label="Nombre" value="{{ $shift->client->name }}" disabled />
                    <x-adminlte-input name="dni" label="Documento de identidad"
                        value="{{ number_format($shift->client->dni, 0, '', '.') }}" disabled />
                    <x-adminlte-button label="Ver detalles" theme="primary" icon="fas fa-eye" id="show-client" />
                </x-adminlte-card>
            </div>
            @if ($shift->module)
                <div class="col-md-6">
                    <x-adminlte-card title="Detalle del modulo" theme="primary" icon="fas fa-edit">
                        <x-adminlte-input name="name" label="Nombre" value="{{ $shift->module->name }}" disabled />
                        <x-adminlte-input name="room" label="Sala" value="{{ $shift->module->room?->name }}"
                            disabled />
                        <x-adminlte-input name="branch" label="Seccional" value="{{ $shift->module->room?->branch->name }}"
                            disabled />
                        <x-adminlte-input name="client_type" label="Tipo de cliente"
                            value="{{ $shift->module->clientType?->name }}" disabled />
                        <x-adminlte-input name="module_type" label="Tipo de modulo"
                            value="{{ $shift->module->moduleType?->name }}" disabled />
                        @if ($shift->module->responsable)
                            <x-adminlte-input name="responsable" label="Responsable"
                                value="{{ $shift->module->responsable->employee->full_name }}" disabled />
                        @endif
                        <x-adminlte-button label="Ver detalles" theme="primary" icon="fas fa-eye" id="show-module" />
                    </x-adminlte-card>
                </div>
            @endif
            @if ($shift->services->count() > 0)

                <div class="col-md-6">
                    <x-adminlte-card title="Servicios prestados" theme="primary" icon="fas fa-edit">
                        <ul class="list-group list-group-flush">
                            @foreach ($shift->services as $service)
                                <li class="list-group-item">
                                    {{ $service->name }}
                                </li>
                            @endforeach
                        </ul>
                    </x-adminlte-card>
                </div>
            @endif
        </div>
        <div class="col-md-4">
            <div class="timeline">
                @php
                    $groupedShifts = $shift->client
                        ->shifts()
                        ->whereDate('created_at', '>=', $shift->created_at)
                        ->whereDate('created_at', '<=', $shift->created_at->format('Y-m-d'))
                        ->orderBy('created_at', 'desc')
                        ->get()
                        ->groupBy(function ($shift) {
                            return $shift->created_at->format('Y-m-d');
                        });
                @endphp
                @foreach ($groupedShifts as $date => $shifts)
                    <div class="time-label">
                        <span class="bg-primary"> {{ $date }}</span>
                    </div>
                    @foreach ($shifts->sortBy('created_at') as $item)
                        <div>
                            @switch($item->state)
                                @case('pending')
                                    <i class="fas fa-hourglass-half bg-info"></i>
                                @break

                                @case('in_progress')
                                    <i class="fas fa-hourglass-start bg-warning"></i>
                                @break

                                @case('finished')
                                    <i class="fas fa-hourglass-end bg-success"></i>
                                @break

                                @case('cancelled')
                                    <i class="fas fa-times bg-danger"></i>
                                @break

                                @case('transferred')
                                    <i class="fas fa-exchange-alt bg-warning"></i>
                                @break

                                @case('pending-transferred')
                                    <i class="fas fa-hourglass-half bg-info"></i>
                                @break

                                @case('qualified')
                                    <i class="fas fa-check bg-success"></i>
                                @break

                                @case('distracted')
                                    <i class="fas fa-times bg-danger"></i>
                                @break
                            @endswitch
                            <div
                                class="timeline-item {{ $item->id == $shift->id ? 'border-primary border border-2' : '' }}">
                                <span class="time">
                                    <i class="fas fa-clock"></i>
                                    {{ $item->created_at->format('H:i') }}
                                </span>
                                <h3 class="timeline-header">
                                    <a href="{{ route('shifts.show', $item) }}">
                                        {{ $item->qualification?->qualification }}
                                    </a>
                                    @if ($item->module?->responsable)
                                        Atendido por {{ $item->module->responsable->employee->full_name }}
                                    @elseif ($item->module)
                                        Atendido por el modulo {{ $item->module->name }}
                                    @endif
                                </h3>
                                <div class="timeline-body">
                                    <strong>Servicios</strong>
                                    <br>
                                    <span class="text-muted">

                                        {{ $item->services->map(function ($service) {
                                                return $service->name;
                                            })->implode(', ') }}
                                    </span>
                                </div>
                            </div>

                        </div>
                    @endforeach
                @endforeach
            </div>
        </div>


    </div>

@stop


@push('js')
    <script>
        $(document).ready(function() {
            $('#show-client').click(function() {
                window.location.href = "{{ route('clients.show', $shift->client) }}";
            });
            $('#show-room').click(function() {
                window.location.href = "{{ route('rooms.show', $shift->room) }}";
            });
            $('#show-module').click(function() {
                window.location.href = "{{ route('modules.show', $shift->module) }}";
            });
        });
    </script>
@endpush
