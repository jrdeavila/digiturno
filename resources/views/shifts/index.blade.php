@extends('adminlte::page')

@section('title', 'Turnos')

@section('content_header')
    <h1>Turnos</h1>
@stop


@section('content')

    <div class="row">
        <div class="col-md-12">
            @session('success')
                <blockquote class="quote quote-success">
                    <h5>Genial! </h5>
                    <p>{{ session('success') }}</p>
                </blockquote>
            @endsession
            @session('error')
                <blockquote class="quote quote-danger">
                    <h5>Error! </h5>
                    <p>{{ session('error') }}</p>
                </blockquote>
            @endsession
        </div>

        <div class="col-md-12">
            <x-adminlte-card theme="light" icon="fas fa-list" title="Filtros">
                <form class="row" action="{{ route('shifts.index') }}" method="GET">
                    @csrf
                    <div class="col-md-2">
                        <x-adminlte-select id="branch_id" label="Seccional" name="branch_id">
                            <option value="">Todos</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}"
                                    {{ request()->get('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}
                                </option>
                            @endforeach
                        </x-adminlte-select>
                    </div>
                    <div class="col-md-2">
                        <x-adminlte-select id="room_id" label="Sala" name="room_id">
                            <option value="">Todos</option>
                            @foreach ($rooms as $room)
                                <option value="{{ $room->id }}"
                                    {{ request()->get('room_id') == $room->id ? 'selected' : '' }}>{{ $room->name }}
                                </option>
                            @endforeach
                        </x-adminlte-select>
                    </div>
                    <div class="col-md-2">
                        <x-adminlte-select id="attention_profile_id" label="Perfil de atencion" name="attention_profile_id">
                            <option value="">Todos</option>
                            @foreach ($attentionProfiles as $attentionProfile)
                                <option value="{{ $attentionProfile->id }}"
                                    {{ request()->get('attention_profile_id') == $attentionProfile->id ? 'selected' : '' }}>
                                    {{ $attentionProfile->name }}</option>
                            @endforeach
                        </x-adminlte-select>
                    </div>
                    <div class="col-md-1">
                        @php
                            $states = [
                                'pending' => 'Pendiente',
                                'pending-transferred' => 'Pendiente transferido',
                                'transferred' => 'Transferido',
                                'distracted' => 'Distraido',
                                'in_progress' => 'En progreso',
                                'completed' => 'Completado',
                                'qualified' => 'Calificado',
                            ];
                        @endphp
                        <x-adminlte-select id="state_id" label="Estado" name="state">
                            <option value="">Todos</option>
                            @foreach ($states as $key => $state)
                                <option value="{{ $key }}"
                                    {{ request()->get('state') == $key ? 'selected' : '' }}>
                                    {{ $state }}</option>
                            @endforeach
                        </x-adminlte-select>
                    </div>
                    <div class="col-md-1">
                        @php
                            $qualifications = [
                                'no_qualified' => 'No calificado',
                                'bad' => 'Malo',
                                'regular' => 'Regular',
                                'good' => 'Bueno',
                                'excellent' => 'Excelente',
                            ];
                        @endphp
                        <x-adminlte-select id="qualification_id" label="Calificación" name="qualification">

                            <option value="">Todos</option>
                            @foreach ($qualifications as $key => $qualification)
                                <option value="{{ $key }}"
                                    {{ request()->get('qualification') == $key ? 'selected' : '' }}>
                                    {{ $qualification }}</option>
                            @endforeach
                        </x-adminlte-select>
                    </div>
                    <div class="col-md-4">
                        <div class="row">
                            <div class="col-md-12">
                                <label for="date">Rango de fechas</label>
                            </div>
                            <div class="col-md-6">
                                <x-adminlte-input value="{{ request()->get('start_date') }}" type="date" id="date"
                                    name="start_date" />
                            </div>
                            <div class="col-md-6">
                                <x-adminlte-input value="{{ request()->get('end_date') }}" type="date" id="date"
                                    name="end_date" />
                            </div>
                        </div>

                    </div>
                    <div class="col-md-2 row justify-content-start align-items-center">
                        <x-adminlte-button type="submit" label="Filtrar" theme="primary" icon="fas fa-filter"
                            id="filter" class="w-100 mt-3 mr-2" />
                    </div>

                    <div class="col-md-2 row justify-content-start align-items-center">
                        <x-adminlte-button type="button" label="Exportar" theme="primary" icon="fas fa-download"
                            class="w-100 mt-3 mr-2" id="export" />
                    </div>


                    <div class="col-md-2 row justify-content-start align-items-center">
                        <x-adminlte-button label="Limpiar" theme="primary" id="clear-filters" class="w-100 mt-3 mr-2"
                            icon="fas fa-eraser" id="clear-filters" />
                    </div>
                </form>

            </x-adminlte-card>
        </div>
        <div class="col-md-12">
            <x-adminlte-card theme="light" icon="fas fa-list" title="Turnos ({{ $shifts->total() }})">

                @php
                    $heads = ['ID', 'Cliente', 'Modulo', 'Estado', 'Calificación', 'Creado En', 'Acciones'];
                    $heads = array_map(function ($head) {
                        return trans($head);
                    }, $heads);
                    $config = [
                        'data' => $shifts,
                        'order' => [[1, 'asc']],
                        'columns' => [null, null, null, null, null, ['orderable' => false]],
                    ];
                @endphp
                <x-adminlte-datatable id="table1" :heads="$heads" :config="$config">
                    @foreach ($shifts as $shift)
                        <tr>
                            <td>{{ $shift->id }}</td>
                            <td>
                                <div class="d-flex flex-column">
                                    <a class="text-bold" href="{{ route('clients.show', $shift->client) }}">
                                        {{ $shift->client->name }}
                                    </a>
                                    <span class="text-muted">
                                        {{ number_format($shift->client->dni, 0, '', '.') }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                @if ($shift->module)
                                    <div class="d-flex flex-column">
                                        <a class="text-bold" href="{{ route('modules.show', $shift->module) }}">
                                            {{ $shift->module->name }}
                                        </a>
                                        <span class="text-muted">
                                            {{ $shift->module->room->name }}
                                        </span>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="text-bold">
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
                                            @break

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
                                    </span>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="text-bold">
                                        {{ $shift->qualification?->qualification }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="text-bold">
                                        {{ Carbon\Carbon::parse($shift->created_at)->translatedFormat('j \d\e F \d\e\l Y') }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-default">Acciones</button>
                                    <button type="button" class="btn btn-default dropdown-toggle dropdown-icon"
                                        data-toggle="dropdown" aria-expanded="true">
                                        <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <div class="dropdown-menu" role="menu"
                                        style="position: absolute; transform: translate3d(68px, 38px, 0px); top: 0px; left: 0px; will-change: transform;"
                                        x-placement="bottom-start">
                                        <a class="dropdown-item" data-toggle="modal"
                                            data-target="#modal-delete-{{ $shift->id }}">
                                            <div class="row">
                                                <div class="col-6">
                                                    <i class="fas fa-trash"></i>
                                                </div>
                                                <div class="col-6">
                                                    Eliminar
                                                </div>
                                            </div>
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="{{ route('shifts.show', $shift->id) }}">
                                            <div class="row">
                                                <div class="col-6">
                                                    <i class="fas fa-eye"></i>
                                                </div>
                                                <div class="col-6">
                                                    Ver mas
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                                <x-adminlte-modal id="modal-delete-{{ $shift->id }}" theme="danger"
                                    title="Eliminar el turno del cliente ({{ $shift->client->name }})">
                                    <span>Esta seguro de eliminar el turno del cliente ({{ $shift->client->name }})</span>
                                    <br>
                                    <span>¿Desea continuar?</span>
                                    <x-slot name="footerSlot">
                                        <form method="POST" action="{{ route('shifts.destroy', $shift) }}">
                                            @csrf
                                            @method('DELETE')
                                            <x-adminlte-button type="submit" label="Eliminar" theme="danger"
                                                icon="fas fa-lg fa-trash" class="mr-2" />
                                            <x-adminlte-button theme="default" label="Cerrar" data-dismiss="modal"
                                                icon="fas fa-lg fa-times" />
                                        </form>
                                    </x-slot>
                                </x-adminlte-modal>
                            </td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="6">
                            <div class="float-right">
                                {{ $shifts->links('custom.pagination') }}
                            </div>
                        </td>
                    </tr>
                </x-adminlte-datatable>
            </x-adminlte-card>
        </div>
    </div>


@stop


@push('js')
    <script>
        $(document).ready(function() {
            $('#create-room').click(function() {
                window.location.href = "{{ route('rooms.create') }}";
            });
            $("#export").click(function() {});
            $("#clear-filters").click(function() {
                window.location.href = "{{ route('shifts.index') }}";
            })
        });
    </script>
@endpush
