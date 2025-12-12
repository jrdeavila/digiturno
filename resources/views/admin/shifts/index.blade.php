@extends('adminlte::page')

@section('title', 'Turnos')

@section('content_header')
    <h1>Turnos</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            @session('success')
                <x-adminlte-alert theme="success" title="Genial!" dismissable>
                    {{ session('success') }}
                </x-adminlte-alert>
            @endsession
            @session('error')
                <x-adminlte-alert theme="danger" title="Error!" dismissable>
                    {{ session('error') }}
                </x-adminlte-alert>
            @endsession
        </div>

        <div class="col-md-12">
            <x-adminlte-card theme="light" icon="fas fa-list" title="Filtros" class="elevation-1">
                <form id="filters-form" class="row" action="{{ route('shifts.index') }}" method="GET">
                    @csrf
                    <div class="col-md-2">
                        <x-adminlte-select id="branch_id" label="Seccional" name="branch_id">
                            <option value="">Todos</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}"
                                    {{ request()->get('branch_id') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </x-adminlte-select>
                    </div>
                    <div class="col-md-2">
                        <x-adminlte-select id="room_id" label="Sala" name="room_id">
                            <option value="">Todos</option>
                            @foreach ($rooms as $room)
                                <option value="{{ $room->id }}"
                                    {{ request()->get('room_id') == $room->id ? 'selected' : '' }}>
                                    {{ $room->name }}
                                </option>
                            @endforeach
                        </x-adminlte-select>
                    </div>
                    <div class="col-md-2">
                        <x-adminlte-select id="attention_profile_id" label="Perfil de atención" name="attention_profile_id">
                            <option value="">Todos</option>
                            @foreach ($attentionProfiles as $attentionProfile)
                                <option value="{{ $attentionProfile->id }}"
                                    {{ request()->get('attention_profile_id') == $attentionProfile->id ? 'selected' : '' }}>
                                    {{ $attentionProfile->name }}
                                </option>
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
                                'finished' => 'Finalizado',
                                'cancelled' => 'Cancelado',
                                'qualified' => 'Calificado',
                            ];
                        @endphp
                        <x-adminlte-select id="state_id" label="Estado" name="state">
                            <option value="">Todos</option>
                            @foreach ($states as $key => $state)
                                <option value="{{ $key }}"
                                    {{ request()->get('state') == $key ? 'selected' : '' }}>
                                    {{ $state }}
                                </option>
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
                                    {{ $qualification }}
                                </option>
                            @endforeach
                        </x-adminlte-select>
                    </div>
                    <div class="col-md-4">
                        <div class="row">
                            <div class="col-md-12">
                                <label for="date">Rango de fechas</label>
                            </div>
                            <div class="col-md-6">
                                <x-adminlte-input value="{{ request()->get('start_date') }}" type="date" id="start_date"
                                    name="start_date" />
                            </div>
                            <div class="col-md-6">
                                <x-adminlte-input value="{{ request()->get('end_date') }}" type="date" id="end_date"
                                    name="end_date" />
                            </div>
                        </div>
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <x-adminlte-button type="submit" label="Filtrar" theme="primary" icon="fas fa-filter"
                            id="filter" class="w-100 mt-3 mr-2" />
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <x-adminlte-button type="button" label="Exportar" theme="primary" icon="fas fa-download"
                            class="w-100 mt-3 mr-2" id="export" />
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <x-adminlte-button type="button" label="Limpiar" theme="outline-primary" class="w-100 mt-3 mr-2"
                            icon="fas fa-eraser" id="clear-filters" />
                    </div>
                </form>
            </x-adminlte-card>
        </div>

        <div class="col-md-12">
            <x-adminlte-card theme="light" icon="fas fa-list" title="Turnos ({{ $shifts->total() }})" class="elevation-1">
                @php
                    $heads = ['ID', 'Cliente', 'Modulo', 'Estado', 'Calificación', 'Creado En', 'Acciones'];
                    $heads = array_map(fn($head) => __($head), $heads);
                    $config = [
                        'data' => $shifts,
                        'order' => [[0, 'asc']],
                        'columns' => [
                            null, // ID
                            null, // Cliente
                            null, // Modulo
                            ['orderable' => false], // Estado
                            null, // Calificación
                            null, // Creado En
                            ['orderable' => false], // Acciones
                        ],
                    ];
                    $stateMap = [
                        'pending' => ['class' => 'text-info', 'icon' => 'fas fa-hourglass-half', 'text' => 'Pendiente'],
                        'in_progress' => ['class' => 'text-warning', 'icon' => 'fas fa-clock', 'text' => 'En progreso'],
                        'finished' => ['class' => 'text-success', 'icon' => 'fas fa-check', 'text' => 'Finalizado'],
                        'cancelled' => ['class' => 'text-danger', 'icon' => 'fas fa-times', 'text' => 'Cancelado'],
                        'transferred' => [
                            'class' => 'text-warning',
                            'icon' => 'fas fa-exchange-alt',
                            'text' => 'Transferido',
                        ],
                        'pending-transferred' => [
                            'class' => 'text-info',
                            'icon' => 'fas fa-hourglass-half',
                            'text' => 'Transferido pendiente',
                        ],
                        'qualified' => ['class' => 'text-success', 'icon' => 'fas fa-check', 'text' => 'Calificado'],
                        'distracted' => ['class' => 'text-danger', 'icon' => 'fas fa-times', 'text' => 'Distraido'],
                    ];
                @endphp

                <x-adminlte-datatable id="table1" :heads="$heads" :config="$config">
                    @foreach ($shifts as $shift)
                        @php
                            $stateDef = $stateMap[$shift->state] ?? null;
                        @endphp
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
                                @if ($stateDef)
                                    <div class="d-flex align-items-center {{ $stateDef['class'] }}">
                                        <i class="{{ $stateDef['icon'] }} mr-2"></i>
                                        <span class="text-bold">{{ $stateDef['text'] }}</span>
                                    </div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="text-bold">
                                        {{ optional($shift->qualification)->qualification ?? '—' }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="text-bold">
                                        {{ \Carbon\Carbon::parse($shift->created_at)->translatedFormat('j \d\e F \d\e\l Y') }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                <div class="btn-group" role="group" aria-label="Acciones">
                                    <button type="button" class="btn btn-danger btn-delete-shift"
                                        data-id="{{ $shift->id }}" data-client="{{ $shift->client->name }}">
                                        <i class="fas fa-trash mr-1"></i> Eliminar
                                    </button>
                                    <a class="btn btn-primary" href="{{ route('shifts.show', $shift->id) }}">
                                        <i class="fas fa-eye mr-1"></i> Ver más
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="7">
                            <div class="float-right">
                                {{ $shifts->links('custom.pagination') }}
                            </div>
                        </td>
                    </tr>
                </x-adminlte-datatable>

                {{-- Formulario oculto para eliminación con SweetAlert2 --}}
                <form id="delete-form" method="POST" style="display:none;">
                    @csrf
                    @method('DELETE')
                </form>
            </x-adminlte-card>
        </div>
    </div>
@stop

@push('js')
    {{-- SweetAlert2 y lógica de interacción --}}
    <script src="{{ asset('vendor/sweetalert2/sweetalert2.all.min.js') }}"></script>
    <script>
        $(function() {
            // Exporta manteniendo los filtros actuales vía query string
            $('#export').on('click', function() {
                const params = new URLSearchParams($('#filters-form').serialize());
                const url = "{{ route('shifts.report') }}" + '?' + params.toString();
                window.location.href = url;
            });

            // Limpia filtros manteniendo ruta base
            $('#clear-filters').on('click', function() {
                $('#filters-form').find('input[type="date"], select').val('');
                window.location.href = "{{ route('shifts.index') }}";
            });

            // Dependencia Branch -> Rooms
            const allRooms = {!! json_encode(
                $rooms->map(
                    fn($r) => [
                        'id' => $r->id,
                        'name' => $r->name,
                        'branch_id' => $r->branch_id ?? null,
                    ],
                ),
                JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT,
            ) !!};

            $('#branch_id').on('change', function() {
                const branchId = $(this).val();
                const $roomSelect = $('#room_id');
                const selectedRoom = "{{ request()->get('room_id') }}";
                const rooms = branchId ?
                    allRooms.filter(r => String(r.branch_id) === String(branchId)) :
                    allRooms;

                $roomSelect.empty().append($('<option>', {
                    value: '',
                    text: 'Todos'
                }));
                rooms.forEach(r => {
                    $roomSelect.append($('<option>', {
                        value: r.id,
                        text: r.name,
                        selected: String(selectedRoom) === String(r.id)
                    }));
                });
            }).trigger('change');

            // Confirmación de eliminación con SweetAlert2
            $('.btn-delete-shift').on('click', function() {
                const shiftId = $(this).data('id');
                const clientName = $(this).data('client');
                Swal.fire({
                    title: 'Eliminar turno',
                    html: `Está seguro de eliminar el turno del cliente <b>${clientName}</b>?<br><small>¿Desea continuar?</small>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#e3342f',
                    cancelButtonColor: '#6c757d',
                    reverseButtons: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.getElementById('delete-form');
                        form.action = "{{ route('shifts.destroy', '__ID__') }}".replace('__ID__',
                            shiftId);
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
