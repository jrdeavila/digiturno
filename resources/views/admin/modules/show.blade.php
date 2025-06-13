@extends('adminlte::page')

@section('title', 'Detalle del modulo')

@section('content_header')
    <div class="row justify-content-between align-items-center">
        <h1>Modulo {{ $module->name }} ({{ $module->room?->name }})</h1>
        <div class="row gap-2">
            <x-adminlte-button id="edit-module" label="Editar" theme="primary" icon="fas fa-edit" class="mr-2" />
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-4">
            <x-adminlte-card title="Detalle del modulo" theme="primary" icon="fas fa-edit">
                <x-adminlte-input name="name" label="Nombre" value="{{ $module->name }}" disabled />
                <x-adminlte-input name="room" label="Sala" value="{{ $module->room?->name }}" disabled />
                <x-adminlte-input name="branch" label="Seccional" value="{{ $module->room?->branch->name }}" disabled />
                <x-adminlte-input name="client_type" label="Tipo de cliente" value="{{ $module->clientType?->name }}"
                    disabled />
                <x-adminlte-input name="module_type" label="Tipo de modulo" value="{{ $module->moduleType?->name }}"
                    disabled />
            </x-adminlte-card>
        </div>

        @if ($module->responsable)
            <div class="col-md-4">
                <x-adminlte-card title="Responsable" theme="primary" icon="fas fa-edit">
                    <x-adminlte-input name="responsable" label="Responsable"
                        value="{{ $module->responsable->employee->full_name }}" disabled />
                    <x-adminlte-input name="cargo" label="Cargo" value="{{ $module->responsable->employee->job->name }}"
                        disabled />
                    <x-adminlte-input name="email" label="Correo" value="{{ $module->responsable->employee->email }}"
                        disabled />
                    <x-adminlte-input name="dni" label="Documento de identidad"
                        value="{{ $module->responsable->employee->documentNumber }}" disabled />
                </x-adminlte-card>
            </div>
        @endif

        <div class="col-md-4">
            <x-adminlte-card title="Perfiles de atención" theme="primary" icon="fas fa-list">
                @php
                    $heads = ['ID', 'Nombre'];
                    $config = [
                        'data' => [],
                        'order' => [[0, 'asc'], [1, 'asc']],
                    ];
                @endphp
                <x-adminlte-datatable id="attention-profiles" :heads="$heads" :config="$config">
                    @if ($module->attentionProfiles->count() > 0)

                        @foreach ($module->attentionProfiles as $attentionProfile)
                            <tr>
                                <td>{{ $attentionProfile->id }}</td>
                                <td>{{ $attentionProfile->name }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="2">
                                <span>No se encontraron perfiles de atención</span>
                            </td>
                        </tr>
                    @endif
                </x-adminlte-datatable>
            </x-adminlte-card>
        </div>
        <div class="col-md-4">
            <div class="timeline" style="height: calc(100vh - 200px); overflow-y: scroll">
                @php
                    $groupedShifts = $module
                        ->shifts()
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
                            <div class="timeline-item">
                                <span class="time">
                                    <i class="fas fa-clock"></i>
                                    {{ $item->created_at->format('H:i') }}
                                </span>
                                <h3 class="timeline-header">
                                    <a href="{{ route('clients.show', $item->client) }}">
                                        {{ $item->client->name }}
                                    </a>
                                    {{ $item->qualification?->qualification }}
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
            $('#edit-module').click(function() {
                window.location.href = "{{ route('modules.edit', $module) }}";
            });
        })
    </script>
@endpush
