@extends('adminlte::page')

@section('title', 'Detalle del servicio')

@section('content_header')
    <h1>Detalle del servicio {{ $service->name }}</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-4">
            <x-adminlte-card title="Detalle del servicio" theme="primary" icon="fas fa-edit">
                <x-adminlte-input name="name" label="Nombre" value="{{ $service->name }}" disabled />
                @if ($service->service)
                    <x-adminlte-input name="parent_service" label="Servicio padre" value="{{ $service->service->name }}"
                        disabled />
                @endif
            </x-adminlte-card>
        </div>

        @if ($service->attentionProfiles->count() > 0)
            <div class="col-md-4">
                <x-adminlte-card title="Perfiles de atención" theme="primary" icon="fas fa-list">

                    @php
                        $heads = ['ID', 'Nombre'];
                        $heads = array_map(function ($head) {
                            return trans($head);
                        }, $heads);
                    @endphp
                    <x-adminlte-datatable id="attention-profiles" :heads="$heads" :config="[
                        'data' => [],
                        'order' => [[0, 'asc'], [1, 'asc']],
                    ]">
                        @foreach ($service->attentionProfiles as $attentionProfile)
                            <tr>
                                <td>{{ $attentionProfile->id }}</td>
                                <td>{{ $attentionProfile->name }}</td>
                            </tr>
                        @endforeach
                    </x-adminlte-datatable>

                </x-adminlte-card>
            </div>
        @endif

        @if ($service->services->count() > 0)
            <div class="col-md-4">
                <x-adminlte-card title="Subservicios" theme="primary" icon="fas fa-list">
                    @include('services.partials.service_tree', [
                        'services' => $service->services()->get(),
                        'root' => true,
                    ])
                    </ul>
                </x-adminlte-card>
            </div>
        @endif
    </div>
@stop
