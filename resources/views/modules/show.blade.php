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
        <div class="col-md-12">
            <x-adminlte-card title="Turnos del modulo ({{ $module->shifts()->count() }})" theme="primary"
                icon="fas fa-list">
                @php
                    $heads = [
                        'ID',
                        'Cliente',
                        'Documento',
                        'Estado',
                        'Calificación',
                        'Fecha de inicio',
                        'Fecha de fin',
                    ];
                    $config = [
                        'data' => [],
                        'order' => [[0, 'asc'], [1, 'asc']],
                    ];
                @endphp
                <x-adminlte-datatable id="turns" :heads="$heads" :config="$config">
                    @php
                        $shifts = $module->shifts()->orderBy('created_at')->paginate(5);
                    @endphp
                    @if ($shifts->count() > 0)

                        @foreach ($shifts as $shift)
                            <tr>
                                <td>{{ $shift->id }}</td>
                                <td>{{ $shift->client->name }}</td>
                                <td>{{ $shift->client->dni }}</td>
                                <td>
                                    @switch($shift->state)
                                        @case('pending')
                                            <div class="text-info">
                                                <i class="fas fa-hourglass-half"></i>
                                                <span>Pendiente</span>
                                            </div>
                                        @break

                                        @case('in_progress')
                                            <div class="text-warning">
                                                <i class="fas fa-clock"></i>
                                                <span>En progreso</span>
                                            </div>
                                        @break

                                        @case('finished')
                                            <div class="text-success">
                                                <i class="fas fa-check"></i>
                                                <span>Finalizado</span>
                                            </div>
                                        @break

                                        @case('cancelled')
                                            <div class="text-danger">
                                                <i class="fas fa-times"></i>
                                                <span>Cancelado</span>
                                            </div>
                                        @break

                                        @case('transferred')
                                            <div class="text-warning">
                                                <i class="fas fa-exchange-alt"></i>
                                                <span>Transferido</span>
                                            </div>
                                        @break

                                        @case('pending-transferred')
                                            <div class="text-info">
                                                <i class="fas fa-hourglass-half"></i>
                                                <span>Transferido pendiente</span>
                                            </div>
                                        @case('qualified')
                                            <div class="text-success">
                                                <i class="fas fa-check"></i>
                                                <span>Calificado</span>
                                            </div>
                                        @break

                                        @case('distracted')
                                            <div class="text-danger">
                                                <i class="fas fa-times"></i>
                                                <span>Distraido</span>
                                            </div>
                                        @break

                                        @default
                                    @endswitch
                                </td>
                                <td>{{ $shift->qualification->qualification }}</td>
                                <td>{{ $shift->created_at }}</td>
                                <td>{{ $shift->updated_at }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="6">{{ $shifts->links('custom.pagination') }}></td>
                        </tr>
                    @else
                        <tr>
                            <td colspan="6">
                                <span>No se encontraron turnos</span>
                            </td>
                        </tr>
                    @endif

                </x-adminlte-datatable>

            </x-adminlte-card>
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
