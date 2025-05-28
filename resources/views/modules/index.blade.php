@extends('adminlte::page')

@section('title', 'Modulos')

@section('content_header')
    <h1>Modulos</h1>
@stop

@section('content')
    <div class="col-md-12">
        @session('success')
            <blockquote class="quote quote-success">
                <h5>Genial!</h5>
                <p>{{ session('success') }}</p>
            </blockquote>
        @endsession
        @session('error')
            <blockquote class="quote quote-danger">
                <h5>Ups! Parece que hubo algun error.</h5>
                <p>{{ session('error') }}</p>
            </blockquote>
        @endsession
    </div>

    <div class="row">
        <div class="col-md-12">
            <x-adminlte-card title="Filtros" theme="primary" icon="fas fa-edit">
                <form action="{{ route('modules.index') }}" method="GET" class="row align-items-center">
                    <div class="col-md-4">
                        <x-adminlte-input name="name" label="Nombre" value="{{ request()->get('name') }}" />
                    </div>
                    <div class="col-md-2">
                        <x-adminlte-select name="branch_id" label="Seccional">
                            <option value="">Todos</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}"
                                    {{ request()->get('branch_id') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}</option>
                            @endforeach
                        </x-adminlte-select>
                    </div>
                    <div class="col-md-2">
                        <x-adminlte-select name="room_id" label="Sala">
                            <option value="">Todos</option>
                            @foreach ($rooms as $room)
                                <option value="{{ $room->id }}"
                                    {{ request()->get('room_id') == $room->id ? 'selected' : '' }}>
                                    {{ $room->name }}</option>
                            @endforeach
                        </x-adminlte-select>
                    </div>
                    @if ($attentionProfiles->count() > 0)
                        <div class="col-md-2">
                            <x-adminlte-select name="attention_profile_id" label="Perfil de atencion">
                                <option value="">Todos</option>
                                @foreach ($attentionProfiles as $attentionProfile)
                                    <option value="{{ $attentionProfile->id }}"
                                        {{ request()->get('attention_profile_id') == $attentionProfile->id ? 'selected' : '' }}>
                                        {{ $attentionProfile->name }}</option>
                                @endforeach
                            </x-adminlte-select>
                        </div>
                    @endif
                    @if ($clientTypes->count() > 0)
                        <div class="col-md-2">
                            <x-adminlte-select name="client_type_id" label="Tipo de cliente">
                                <option value="">Todos</option>
                                @foreach ($clientTypes as $clientType)
                                    <option value="{{ $clientType->id }}"
                                        {{ request()->get('client_type_id') == $clientType->id ? 'selected' : '' }}>
                                        {{ $clientType->name }}</option>
                                @endforeach
                            </x-adminlte-select>
                        </div>
                    @endif
                    <div class="col-md-2">
                        <x-adminlte-select name="module_type_id" label="Tipo de modulo">
                            <option value="">Todos</option>
                            @foreach ($moduleTypes as $moduleType)
                                <option value="{{ $moduleType->id }}"
                                    {{ request()->get('module_type_id') == $moduleType->id ? 'selected' : '' }}>
                                    {{ $moduleType->name }}</option>
                            @endforeach
                        </x-adminlte-select>
                    </div>

                    <div class="col-md-2">
                        <div class="row">
                            <div class="col-md-6">
                                <x-adminlte-button class="mt-3 w-100" type="submit" label="Filtrar" theme="primary"
                                    icon="fas fa-search" />
                            </div>
                            <div class="col-md-6">
                                <x-adminlte-button class="mt-3 w-100" type="button" label="Limpiar" theme="primary"
                                    id="clear-filters" />
                            </div>
                        </div>
                    </div>

                </form>

            </x-adminlte-card>
        </div>
        <div class="col-md-12">
            <x-adminlte-card title="Crear modulo" theme="primary" icon="fas fa-edit">
                @php
                    $heads = [
                        'modules.id',
                        'modules.name',
                        'modules.room',
                        'modules.responsable',
                        'modules.clientType',
                        'modules.moduleType',
                        'modules.actions.label',
                    ];
                    $heads = array_map(function ($head) {
                        return trans($head);
                    }, $heads);
                    $config = [
                        'data' => [],
                        'order' => [[0, 'asc'], [1, 'asc']],
                    ];
                @endphp
                <x-adminlte-datatable id="modules" :heads="$heads" :config="$config">
                    @if ($modules->count() > 0)

                        @foreach ($modules as $module)
                            <tr>
                                <td>{{ $module->id }}</td>
                                <td>{{ $module->name }}</td>
                                <td>{{ $module->room?->name }}</td>
                                <td>{{ $module->responsable?->employee->full_name }}</td>
                                <td>{{ $module->clientType?->name }}</td>
                                <td>{{ $module->moduleType->name }}</td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button"
                                            class="btn btn-default">{{ __('modules.actions.label') }}</button>
                                        <button type="button" class="btn btn-default dropdown-toggle dropdown-icon"
                                            data-toggle="dropdown" aria-expanded="true">
                                            <span class="sr-only">Toggle Dropdown</span>
                                        </button>
                                        <div class="dropdown-menu" role="menu"
                                            style="position: absolute; transform: translate3d(68px, 38px, 0px); top: 0px; left: 0px; will-change: transform;"
                                            x-placement="bottom-start">
                                            <a class="dropdown-item" href="{{ route('modules.edit', $module->id) }}">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <i class="fas fa-edit"></i>
                                                    </div>
                                                    <div class="col-6">
                                                        {{ __('modules.actions.edit') }}
                                                    </div>
                                                </div>
                                            </a>

                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" data-toggle="modal"
                                                data-target="#modal-delete-{{ $module->id }}">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <i class="fas fa-trash"></i>
                                                    </div>
                                                    <div class="col-6">
                                                        {{ __('modules.actions.delete') }}
                                                    </div>
                                                </div>
                                            </a>



                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="{{ route('modules.show', $module->id) }}">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <i class="fas fa-eye"></i>
                                                    </div>
                                                    <div class="col-6">
                                                        {{ __('modules.actions.view') }}
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                    <x-adminlte-modal id="modal-delete-{{ $module->id }}" theme="danger"
                                        title="Eliminar ({{ $module->name }})">
                                        <span>El modulo sera eliminado permanentemente y no podra ser recuperado</span>
                                        <br>
                                        <span>¿Desea continuar?</span>
                                        <x-slot name="footerSlot">
                                            <form method="POST" action="{{ route('modules.destroy', $room) }}">
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
                    @else
                        <tr>
                            <td colspan="7">
                                <span>No se encontraron modulos</span>
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <td colspan="7">
                            {{ $modules->appends($_GET)->links('custom.pagination') }}
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
            $('#clear-filters').click(function() {
                window.location.href = "{{ route('modules.index') }}";
            });
        });
    </script>
@endpush
