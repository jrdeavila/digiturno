@extends('adminlte::page')

@section('title', 'Detalles del cliente')

@section('content_header')
    <h1>Detalles del cliente {{ $client->name }}</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-4">
            <x-adminlte-card title="Detalle del cliente" theme="primary" icon="fas fa-edit">
                <x-adminlte-input name="name" label="Nombre" value="{{ $client->name }}" disabled />
                <x-adminlte-input name="dni" label="Documento de identidad" value="{{ $client->dni }}" disabled />
                <x-adminlte-input name="client_type" label="Tipo de cliente" value="{{ $client->clientType->name }}"
                    disabled />
                <x-adminlte-button label="Editar" theme="primary" icon="fas fa-edit" id="edit-client" />
            </x-adminlte-card>
        </div>
        <div class="col-md-8">
            <div class="timeline" style="height: calc(100vh - 200px); overflow-y: scroll">
                @php
                    $groupedShifts = $client
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
            $('#edit-client').click(function() {
                window.location.href = "{{ route('clients.edit', $client) }}";
            });
        });
    </script>
@endpush
