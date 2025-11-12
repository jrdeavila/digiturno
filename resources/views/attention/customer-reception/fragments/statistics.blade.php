@props([
'toDayCount' => 0,
'pendingCount' => 0,
'distractedCount' => 0,
])

<x-adminlte-card title="Estadisticas" icon="fas fa-chart-bar">
    <x-slot name="toolsSlot">
        <x-adminlte-button label="Acciones" theme="primary" id="dropdownMenuButton" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" />
        <div class="dropdown-menu" role="menu"
            style="position: absolute; transform: translate3d(68px, 38px, 0px); top: 0px; left: 0px; will-change: transform;"
            x-placement="bottom-start">

            <div class="dropdown-divider"></div>

            <form id="form-delete-distracted"
                action="{{ route('attention.customer-reception.shifts.distracted-delete', $currentRoom) }}"
                method="POST">
                @csrf
                @method('DELETE')
                <a class="dropdown-item" onclick="document.getElementById('form-delete-distracted').submit();">
                    <div class="row">
                        <div class="col-2">
                            <i class="fas fa-upload"></i>
                        </div>
                        <div class="col-10">
                            Eliminar todos los distraidos
                        </div>
                    </div>
                </a>
            </form>
        </div>
    </x-slot>
    <div class="row">
        <div class="col-md-6">

            <x-adminlte-info-box title="Atendidos" text="{{ $toDayCount }}" theme="info" icon="far fa-check-circle" />
        </div>
        <div class="col-md-6">

            <x-adminlte-info-box title="Pendientes" text="{{ $pendingCount }}" theme="info" icon="far fa-clock" />
        </div>
        <div class="col-md-6">
            <x-adminlte-info-box title="Distraidos"
                text="{{ $distractedCount }}" theme="info" icon="far fa-times-circle" />

        </div>
    </div>
</x-adminlte-card>