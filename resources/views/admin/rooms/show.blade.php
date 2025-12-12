@extends('adminlte::page')

@section('title', 'Detalle de la sala')

@section('content_header')
    <div class="d-flex align-items-center justify-content-between">
        <h1 class="mb-0">Detalle de la sala {{ $room->name }}</h1>
        <div>
            <a href="{{ route('rooms.index') }}" class="btn btn-outline-primary mr-2">
                <i class="fas fa-arrow-left mr-1"></i> Volver
            </a>
            <a href="{{ route('rooms.edit', $room) }}" class="btn btn-primary">
                <i class="fas fa-edit mr-1"></i> Editar sala
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        {{-- Detalle de la sala --}}
        <div class="col-md-8">
            <x-adminlte-card title="Detalle de la sala" theme="primary" icon="fas fa-door-open" class="elevation-1">
                <div class="row">
                    <div class="col-md-6">
                        <x-adminlte-input name="name" label="Nombre" value="{{ $room->name }}" disabled />
                    </div>
                    <div class="col-md-6">
                        <x-adminlte-input name="branch" label="Seccional" value="{{ optional($room->branch)->name }}"
                            disabled />
                    </div>
                </div>
                <div class="d-flex justify-content-end">
                    <a href="{{ route('branches.show', optional($room->branch)->id) }}"
                        class="btn btn-outline-primary btn-sm" {{ optional($room->branch)->id ? '' : 'disabled' }}>
                        <i class="fas fa-eye mr-1"></i> Ver seccional
                    </a>
                </div>
            </x-adminlte-card>
        </div>

        {{-- Perfiles de atención --}}
        <div class="col-md-4">
            <x-adminlte-card title="Perfiles de atención" theme="primary" icon="fas fa-list" class="elevation-1">
                @php
                    $heads = ['ID', 'Nombre', 'Acciones'];
                    $heads = array_map(fn($h) => trans($h), $heads);
                @endphp

                <x-adminlte-datatable id="attention-profiles" :heads="$heads" :config="['data' => [], 'order' => [[0, 'asc'], [1, 'asc']]]">
                    @forelse ($room->attentionProfiles as $attentionProfile)
                        <tr>
                            <td>{{ $attentionProfile->id }}</td>
                            <td>
                                <a href="{{ route('attention-profiles.show', $attentionProfile) }}" class="text-primary">
                                    {{ $attentionProfile->name }}
                                </a>
                            </td>
                            <td>
                                <div class="btn-group" role="group"
                                    aria-label="{{ __('attentionProfiles.actions.label') }}">
                                    <a href="{{ route('attention-profiles.show', $attentionProfile) }}"
                                        class="btn btn-primary btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger btn-sm btn-delete-attention-profile"
                                        data-id="{{ $attentionProfile->id }}" data-name="{{ $attentionProfile->name }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-muted">Sin perfiles asociados a esta sala.</td>
                        </tr>
                    @endforelse

                    <tr>
                        <td colspan="3">
                            <a href="{{ route('attention-profiles.create', $room) }}" class="btn btn-primary btn-block"
                                id="create-attention-profile">
                                <i class="fas fa-plus mr-1"></i> Agregar perfil de atención
                            </a>
                        </td>
                    </tr>
                </x-adminlte-datatable>

                {{-- Formulario oculto para eliminación de perfil --}}
                <form id="delete-attention-profile-form" method="POST" style="display:none;">
                    @csrf
                    @method('DELETE')
                </form>
            </x-adminlte-card>
        </div>

        {{-- Módulos de la sala: tarjetas dinámicas --}}
        <div class="col-12">
            <x-adminlte-card title="Módulos de la sala" theme="primary" icon="fas fa-list" class="elevation-1">
                <div class="row">
                    @forelse ($room->modules as $module)
                        @php
                            // Mapa de iconos y temas por tipo
                            $typeId = optional($module->moduleType)->id;
                            $map = [
                                1 => ['icon' => 'fas fa-laptop', 'theme' => 'info'],
                                2 => ['icon' => 'fas fa-home', 'theme' => 'info'],
                                3 => ['icon' => 'fas fa-bell', 'theme' => 'danger'],
                                4 => ['icon' => 'fas fa-desktop', 'theme' => 'success'],
                                5 => ['icon' => 'fas fa-hand-pointer', 'theme' => 'info'],
                                6 => ['icon' => 'fas fa-exclamation-triangle', 'theme' => 'danger'],
                            ];
                            $def = $map[$typeId] ?? ['icon' => 'fas fa-question', 'theme' => 'primary'];

                            $clientTypeName = optional($module->clientType)->name;
                            $attentionProfiles = $module->attentionProfiles;
                        @endphp

                        <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                            <div class="card h-100 shadow-sm">
                                <div class="card-header d-flex align-items-center">
                                    <i class="{{ $def['icon'] }} text-{{ $def['theme'] }} mr-2"></i>
                                    <strong class="mb-0">{{ $module->name }}</strong>
                                    @if ($clientTypeName)
                                        <span class="badge badge-light ml-2">{{ $clientTypeName }}</span>
                                    @endif
                                    <div class="ml-auto">
                                        {{-- Estado opcional: coloca tu lógica si aplica --}}
                                        @if ($module->responsable)
                                            <span class="badge badge-success">Asignado</span>
                                        @else
                                            <span class="badge badge-secondary">Sin responsable</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="text-muted small mb-2">
                                        Tipo: {{ optional($module->moduleType)->name ?? '—' }}
                                    </div>
                                    <div class="mb-2">
                                        Perfiles de atención:

                                    </div>
                                    <div class="d-flex flex-wrap mb-2">

                                        @forelse ($attentionProfiles as $attentionProfile)
                                            <span class="badge badge-primary">
                                                {{ $attentionProfile->name }}
                                            </span>
                                        @empty
                                            <div class="alert alert-light text-muted">
                                                No hay perfiles de atención asociados a este módulo.
                                            </div>
                                        @endforelse
                                    </div>

                                    @if (optional($module->responsable)->employee)
                                        <div class="mb-2">
                                            Responsable:
                                            <span
                                                class="font-weight-bold">{{ $module->responsable->employee->full_name }}</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="card-footer bg-white">
                                    <div class="btn-group btn-group-sm" role="group" aria-label="Acciones módulo">
                                        <a href="{{ route('modules.show', $module->id) }}" class="btn btn-outline-primary">
                                            <i class="fas fa-eye mr-1"></i> Ver
                                        </a>
                                        <a href="{{ route('modules.edit', $module->id) }}" class="btn btn-outline-warning">
                                            <i class="fas fa-edit mr-1"></i> Editar
                                        </a>
                                        <button type="button" class="btn btn-outline-danger btn-delete-module"
                                            data-id="{{ $module->id }}" data-name="{{ $module->name }}">
                                            <i class="fas fa-trash mr-1"></i> Eliminar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-light text-muted">
                                No hay módulos configurados en esta sala.
                            </div>
                        </div>
                    @endforelse
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('modules.create', ['room_id' => $room->id]) }}" class="btn btn-success">
                        <i class="fas fa-plus mr-1"></i> Crear módulo
                    </a>
                </div>

                {{-- Formulario oculto para eliminación de módulo --}}
                <form id="delete-module-form" method="POST" style="display:none;">
                    @csrf
                    @method('DELETE')
                </form>
            </x-adminlte-card>
        </div>
    </div>
@stop

@push('js')
    {{-- SweetAlert2: usa vendor si lo tienes, o CDN si prefieres --}}
    <script src="{{ asset('vendor/sweetalert2/sweetalert2.all.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Eliminar perfil de atención
            document.querySelectorAll('.btn-delete-attention-profile').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const name = this.getAttribute('data-name');
                    Swal.fire({
                        title: 'Eliminar perfil de atención',
                        html: `El perfil <b>${name}</b> será eliminado de la sala.<br><small>Esta acción no se puede deshacer.</small>`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar',
                        confirmButtonColor: '#e3342f',
                        cancelButtonColor: '#6c757d',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const form = document.getElementById(
                                'delete-attention-profile-form');
                            form.action =
                                "{{ route('attention-profiles.destroy', '__ID__') }}"
                                .replace('__ID__', id);
                            form.submit();
                        }
                    });
                });
            });

            // Eliminar módulo
            document.querySelectorAll('.btn-delete-module').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const name = this.getAttribute('data-name');
                    Swal.fire({
                        title: 'Eliminar módulo',
                        html: `El módulo <b>${name}</b> será eliminado.<br><small>Esta acción no se puede deshacer.</small>`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar',
                        confirmButtonColor: '#e3342f',
                        cancelButtonColor: '#6c757d',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const form = document.getElementById('delete-module-form');
                            form.action = "{{ route('modules.destroy', '__ID__') }}"
                                .replace('__ID__', id);
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
@endpush
