@extends('adminlte::page')

@section('title', 'Detalle de la sala')

@section('content_header')
    <h1>Detalle de la sala {{ $room->name }}</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8">
            <x-adminlte-card title="Detalle de la sala" theme="primary" icon="fas fa-edit">
                <x-adminlte-input name="name" label="Nombre" value="{{ $room->name }}" disabled />
                <x-adminlte-input name="branch" label="Seccional" value="{{ $room->branch->name }}" disabled />
            </x-adminlte-card>
        </div>

        <div class="col-md-4">
            <x-adminlte-card title="Perfiles de atención" theme="primary" icon="fas fa-list">

                @php
                    $heads = ['attentionProfiles.id', 'attentionProfiles.name', 'attentionProfiles.actions.label'];
                    $heads = array_map(function ($head) {
                        return trans($head);
                    }, $heads);
                @endphp
                <x-adminlte-datatable id="attention-profiles" :heads="$heads" :config="[
                    'data' => [],
                    'order' => [[0, 'asc'], [1, 'asc']],
                ]">
                    @foreach ($room->attentionProfiles as $attentionProfile)
                        <tr>
                            <td>{{ $attentionProfile->id }}</td>
                            <td>{{ $attentionProfile->name }}</td>
                            <td>
                                <div class="btn-group">
                                    <button type="button"
                                        class="btn btn-default">{{ __('attentionProfiles.actions.label') }}</button>
                                    <button type="button" class="btn btn-default dropdown-toggle dropdown-icon"
                                        data-toggle="dropdown" aria-expanded="true">
                                        <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <div class="dropdown-menu" role="menu"
                                        style="position: absolute; transform: translate3d(68px, 38px, 0px); top: 0px; left: 0px; will-change: transform;"
                                        x-placement="bottom-start">

                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" data-toggle="modal"
                                            data-target="#modal-delete-{{ $attentionProfile->id }}">
                                            <div class="row">
                                                <div class="col-6">
                                                    <i class="fas fa-trash"></i>
                                                </div>
                                                <div class="col-6">
                                                    {{ __('attentionProfiles.actions.delete') }}
                                                </div>
                                            </div>
                                        </a>

                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item"
                                            href="{{ route('attention-profiles.show', $attentionProfile) }}">
                                            <div class="row">
                                                <div class="col-6">
                                                    <i class="fas fa-eye"></i>
                                                </div>
                                                <div class="col-6">
                                                    {{ __('attentionProfiles.actions.show') }}
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                                <x-adminlte-modal id="modal-delete-{{ $attentionProfile->id }}" theme="danger"
                                    title="Eliminar ({{ $attentionProfile->name }}) del la sala">
                                    <span>
                                        El perfil de atención <strong>{{ $attentionProfile->name }}</strong> será
                                        eliminado de la sala. Esto significa que no podrá ser utilizado para atender a los
                                        clientes en esta sala.
                                    </span>
                                    <br>
                                    <span>¿Desea continuar?</span>
                                    <x-slot name="footerSlot">
                                        <form method="POST"
                                            action="{{ route('attention-profiles.destroy', $attentionProfile) }}">
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
                        <td colspan="3">
                            <x-adminlte-button id="create-attention-profile" label="Agregar perfil de atención"
                                theme="primary" icon="fas fa-plus" />
                        </td>
                    </tr>
                </x-adminlte-datatable>

            </x-adminlte-card>
        </div>

        <div class="col-12">
            <x-adminlte-card title="Modulos de la sala" theme="primary" icon="fas fa-list">


                <div class="row">

                    @foreach ($room->modules as $module)
                        <div class="col-lg-3 col-md-4">
                            @php
                                $icon = 'fas fa-question';
                                $theme = 'primary';
                                if ($module->moduleType->id === 1) {
                                    $icon = 'fas fa-laptop';
                                    $theme = 'info';
                                }
                                if ($module->moduleType->id === 2) {
                                    $icon = 'fas fa-home';
                                    $theme = 'info';
                                }
                                if ($module->moduleType->id === 3) {
                                    $icon = 'fas fa-bell';
                                    $theme = 'danger';
                                }
                                if ($module->moduleType->id === 4) {
                                    $icon = 'fas fa-desktop';
                                    $theme = 'success';
                                }

                                if ($module->moduleType->id === 5) {
                                    $icon = 'fas fa-hand-back-point-up';
                                    $theme = 'info';
                                }

                                if ($module->moduleType->id === 6) {
                                    $icon = 'fas fa-exclamation-triangle';
                                    $theme = 'danger';
                                }

                            @endphp

                            <x-adminlte-info-box description="{{ $module->moduleType->name }}" theme="{{ $theme }}"
                                icon="{{ $icon }}"
                                title="{{ $module->name }} {{ $module->clientType?->name ? '(' . $module->clientType->name . ')' : '' }}"
                                text="{{ $module->attentionProfile?->name }}"
                                url="{{ route('modules.show', $module->id) }}" style="height: 130px" />
                        </div>
                    @endforeach
                </div>

            </x-adminlte-card>
        </div>


    </div>

@stop


@push('js')
    <script>
        $(document).ready(function() {
            $('#create-attention-profile').click(function() {
                window.location.href = "{{ route('attention-profiles.create', $room) }}";
            });
        });
    </script>
@endpush
