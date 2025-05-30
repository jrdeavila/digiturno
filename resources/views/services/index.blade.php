@extends('adminlte::page')

@section('title', 'Servicios')

@section('content_header')
    <h1>Servicios</h1>
@stop


@section('content')

    <x-adminlte-card theme="light" icon="fas fa-list" title="Seccionales">
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
        @php
            $heads = ['ID', 'Nombre', 'Servicio Padre', 'Acciones'];
            $heads = array_map(function ($head) {
                return trans($head);
            }, $heads);
            $config = [
                'data' => $services,
                'order' => [[1, 'asc']],
                'columns' => [null, null, null, null, null, ['orderable' => false]],
            ];
        @endphp
        <x-adminlte-datatable id="table1" :heads="$heads" :config="$config">
            @foreach ($services as $service)
                <tr>
                    <td>{{ $service->id }}</td>
                    <td>{{ $service->name }}</td>
                    <td>{{ $service->service?->name }}</td>
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
                                <a class="dropdown-item" href="{{ route('services.edit', $service->id) }}">
                                    <div class="row">
                                        <div class="col-6">
                                            <i class="fas fa-edit"></i>
                                        </div>
                                        <div class="col-6">
                                            Editar
                                        </div>
                                    </div>
                                </a>

                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" data-toggle="modal"
                                    data-target="#modal-delete-{{ $service->id }}">
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
                                <a class="dropdown-item" href="{{ route('services.show', $service->id) }}">
                                    <div class="row">
                                        <div class="col-6">
                                            <i class="fas fa-eye"></i>
                                        </div>
                                        <div class="col-6">
                                            Ver
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <x-adminlte-modal id="modal-delete-{{ $service->id }}" theme="danger"
                            title="Eliminar ({{ $service->name }})">
                            <span>Se eliminara el servicio {{ $service->name }}</span>
                            <br>
                            <span>¿Desea continuar?</span>
                            <x-slot name="footerSlot">
                                <form method="POST" action="{{ route('services.destroy', $service) }}">
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
                <td colspan="2">
                    <x-adminlte-button id="create-service" theme="primary" label="Crear" />
                </td>
                <td colspan="3">
                    <div class="float-right">
                        {{ $services->links('custom.pagination') }}
                    </div>
                </td>
            </tr>
        </x-adminlte-datatable>
    </x-adminlte-card>

@stop


@push('js')
    <script>
        $(document).ready(function() {
            $('#create-service').click(function() {
                window.location.href = "{{ route('services.create') }}";
            });
        });
    </script>
@endpush
