@extends('adminlte::page')

@section('title', 'Recepción de clientes')

@section('content_header')
    <div class="mb-1"></div>
@stop

@section('content')
    <div x-data="{ edit: {{ $searched && !isset($client) ? 'true' : 'false' }} }" class="row h-100">
        <div class="col-md-3">
            <x-adminlte-card title="Crear turno" icon="fas fa-plus">
                <form action="{{ route('attention.customer-reception.create-shift') }}" method="POST">
                    <div class="row align-items-end">
                        <div class="col-10">
                            <x-adminlte-input name="dni" label="Cédula" placeholder="Ingrese la cédula del cliente"
                                value="{{ request('dni', $client?->dni) }}" />
                        </div>
                        <div class="col-2">
                            @if ($searched)
                                <x-adminlte-button class="btn mb-3" theme="danger" icon="fas fa-times"
                                    id="clear-search-client" />
                            @else
                                <x-adminlte-button type="submit" class="btn btn-primary btn-block mb-3" theme="primary"
                                    icon="fas fa-search" />
                            @endif

                        </div>
                    </div>


                    @if ($searched)
                        <div class="row align-items-end">
                            <div class="col-10">
                                <x-adminlte-input name="name" label="Nombre" placeholder="Ingrese el nombre del cliente"
                                    value="{{ request('name', $client?->name) }}" x-bind:disabled="!edit" />
                            </div>
                            <div class="col-2">
                                <x-adminlte-button class="btn mb-3" x-bind:class="edit ? 'bg-secondary' : 'bg-primary'"
                                    theme="secondary" icon="fas fa-edit" x-on:click="edit = !edit" />
                            </div>
                        </div>
                        <x-adminlte-select name="client_type_id" label="Tipo de cliente" x-bind:disabled="!edit">
                            @foreach ($clientTypes as $clientType)
                                <option value="{{ $clientType->id }}"
                                    {{ request('client_type_id', $client?->client_type_id) == $clientType->id ? 'selected' : '' }}>
                                    {{ $clientType->name }}
                                </option>
                            @endforeach
                        </x-adminlte-select>
                    @endif
                </form>

                <form action="{{ route('attention.customer-reception.create-shift') }}" method="POST">
                    @csrf
                    <label for="attention_profile_id">Perfil de atención</label>
                    @foreach ($attentionProfiles as $attentionProfile)
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="attention_profile_id"
                                id="attention_profile_{{ $attentionProfile->id }}" value="{{ $attentionProfile->id }}"
                                {{ request('attention_profile_id', $client?->attention_profile_id) == $attentionProfile->id ? 'checked' : '' }}>
                            <label class="form-check-label" for="attention_profile_{{ $attentionProfile->id }}">
                                <div class="d-flex align-items-center">
                                    <span>{{ $attentionProfile->name }}</span>
                                </div>
                        </div>
                    @endforeach


                </form>
            </x-adminlte-card>
            <x-adminlte-card title="Distraidos" icon="fas fa-times-circle">
                @if ($distractedShifts->isEmpty())
                    <p class="text-center">No hay turnos distraidos.</p>
                @else
                    <ul class="list-group">
                        @foreach ($distractedShifts as $shift)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $shift->client->name }}</strong> ({{ $shift->client->dni }})
                                </div>
                                <div class="d-flex align-items-center">
                                    @php
                                        $definition = \App\Utils\ClientDefinitionProvider::getDefinition(
                                            $shift->client->clientType->slug,
                                        );
                                    @endphp
                                    <div class="avatar {{ $definition['color'] }} avatar-xs rounded-circle mr-2 d-flex align-items-center justify-content-center"
                                        style="width: 30px; height: 30px;">
                                        <i class="{{ $definition['icon'] }}"></i>
                                    </div>
                                    <span>
                                        {{ $definition['text'] }}
                                    </span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif

            </x-adminlte-card>
        </div>

        <div class="col-md-6">
            <x-adminlte-card title="Turnos pendientes" theme="primary" icon="fas fa-users">
                @if ($shifts->isEmpty())
                    <p class="text-center">No hay turnos pendientes.</p>
                @else
                    <ul class="list-group">
                        @foreach ($shifts as $shift)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $shift->client->name }}</strong> ({{ $shift->client->dni }})
                                </div>
                                <div class="d-flex align-items-center">
                                    @php
                                        $definition = \App\Utils\ClientDefinitionProvider::getDefinition(
                                            $shift->client->clientType->slug,
                                        );
                                    @endphp
                                    <div class="avatar {{ $definition['color'] }} avatar-xs rounded-circle mr-2 d-flex align-items-center justify-content-center"
                                        style="width: 30px; height: 30px;">
                                        <i class="{{ $definition['icon'] }}"></i>
                                    </div>
                                    <span>
                                        {{ $definition['text'] }}
                                    </span>
                                    <div class="btn-group ml-2">
                                        <button type="button" class="btn btn-default dropdown-toggle dropdown-icon"
                                            data-toggle="dropdown" aria-expanded="true">
                                            <span class="sr-only">Toggle Dropdown</span>
                                        </button>
                                        <div class="dropdown-menu" role="menu"
                                            style="position: absolute; transform: translate3d(68px, 38px, 0px); top: 0px; left: 0px; will-change: transform;"
                                            x-placement="bottom-start">
                                            <a class="dropdown-item" href="">
                                                <div class="row">
                                                    <div class="col-2">
                                                        <i class="fas fa-exchange-alt"></i>
                                                    </div>
                                                    <div class="col-10 text-truncate">
                                                        Cambiar perfil de atención
                                                    </div>
                                                </div>
                                            </a>

                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" data-toggle="modal" data-target="#modal-delete">
                                                <div class="row">
                                                    <div class="col-2">
                                                        <i class="fas fa-exchange-alt"></i>
                                                    </div>
                                                    <div class="col-10">
                                                        Cambiar de sala
                                                    </div>
                                                </div>
                                            </a>
                                            <a class="dropdown-item" href="">
                                                <div class="row">
                                                    <div class="col-2">
                                                        <i class="fas fa-times"></i>
                                                    </div>
                                                    <div class="col-10">
                                                        Cancelar turno
                                                    </div>
                                                </div>
                                            </a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="">
                                                <div class="row">
                                                    <div class="col-2">
                                                        <i class="fas fa-eye"></i>
                                                    </div>
                                                    <div class="col-10">
                                                        Ver detalles
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                    {{ $shifts->links('custom.pagination') }}
                @endif


            </x-adminlte-card>


        </div>

        <div class="col-md-3">
            <x-adminlte-card title="Estadisticas" icon="fas fa-chart-bar">
                <div class="row">
                    <div class="col-md-6">

                        <x-adminlte-info-box title="Atendidos" text="{{ $toDayCount }}" theme="info"
                            icon="far fa-check-circle" />
                    </div>
                    <div class="col-md-6">

                        <x-adminlte-info-box title="Pendientes" text="{{ $pendingCount }}" theme="info"
                            icon="far fa-clock" />
                    </div>
                    <div class="col-md-12">
                        <x-adminlte-info-box title="Distraidos" text="{{ $distractedCount }}" theme="info"
                            icon="far fa-times-circle" />
                    </div>
                </div>
            </x-adminlte-card>

            <x-adminlte-card title="Modulos de la sala" icon="fas fa-desktop" maximizable collapsable>
                <div class="row position-relative">
                    @foreach ($modules as $module)
                        <div class="col-lg-3 col-md-6 mb-2">
                            @php
                                $theme = 'primary';
                                switch ($module->status) {
                                    case 'offline':
                                        $theme = 'danger';
                                        break;

                                    case 'online':
                                        $theme = 'success';
                                        break;
                                    default:
                                        $theme = 'warning';
                                        break;
                                }
                            @endphp
                            <x-adminlte-info-box title="{{ $module->name }}" text="{{ $module->description }}"
                                theme="{{ $theme }}" icon="fas fa-desktop" />
                        </div>
                    @endforeach
                </div>
            </x-adminlte-card>



        </div>
    </div>
@stop

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const clearSearchButton = document.getElementById('clear-search-client');
            if (clearSearchButton) {
                clearSearchButton.addEventListener('click', function(event) {
                    event.preventDefault();
                    window.location.href = '{{ route('attention.customer-reception.index') }}';
                });
            }
        });
    </script>
@endpush
