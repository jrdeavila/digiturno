@extends('adminlte::page')

@section('title', 'Clientes')

@section('content_header')
    <h1>Clientes ({{ number_format($clients->total()) }})</h1>
@stop

@section('content')

    <div class="row">
        <div class="col-xl-8">
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
        <div class="col-xl-8">
            <x-adminlte-card title="Filtros" theme="primary" icon="fas fa-edit">
                <form class="row" action="{{ route('clients.index') }}" method="GET">
                    @csrf
                    <div class="col-md-4">
                        <x-adminlte-input name="name" label="Nombre" value="{{ request()->get('name') }}" />
                    </div>
                    <div class="col-md-4">
                        <x-adminlte-input name="dni" label="Documento de identidad"
                            value="{{ request()->get('dni') }}" />
                    </div>
                    <div class="col-md-4">
                        <x-adminlte-select name="client_type_id" label="Tipo de cliente">
                            <option value="">Todos</option>
                            @foreach ($clientTypes as $clientType)
                                <option value="{{ $clientType->id }}"
                                    {{ $clientType->id == request()->get('client_type_id') ? 'selected' : '' }}>
                                    {{ $clientType->name }}
                                </option>
                            @endforeach
                        </x-adminlte-select>
                    </div>
                    <div class="col-md-2 row align-items-center">
                        <x-adminlte-button class="mt-3" type="submit" label="Buscar" theme="primary"
                            icon="fas fa-search" />
                    </div>
                </form>
            </x-adminlte-card>
        </div>
        <div class="col-xl-8">
            <x-adminlte-card title="Clientes" theme="primary" icon="fas fa-edit">
                @php
                    $heads = [
                        'ID',
                        ['label' => 'Nombre', 'width' => 30],
                        ['label' => 'Documento de identidad', 'width' => 20],
                        '# de turnos',
                        'Fecha de registro',
                        'Acciones',
                    ];
                    $config = [
                        'data' => [],
                        'order' => [[0, 'asc'], [1, 'asc']],
                    ];
                @endphp
                <x-adminlte-datatable x-data="{
                    editClient: (id) => {
                        window.location.href = '{{ route('clients.edit', ':id') }}'.replace(':id', id);
                    },
                    copyToClipboard: (dni) => {
                        navigator.clipboard.writeText(dni);
                    },
                    createClient: () => {
                        window.location.href = '{{ route('clients.create') }}';
                    },
                }" id="clients" :heads="$heads" :config="$config">
                    @foreach ($clients as $client)
                        <tr>
                            <td>{{ $client->id }}</td>
                            <td>{{ $client->name }}</td>
                            <td>
                                {{-- Copiar el numero en el portapapeles para facilitar la busqueda --}}
                                <button class="btn btn-primary w-100" x-on:click="copyToClipboard('{{ $client->dni }}')">
                                    <span>{{ number_format(intval($client->dni)) }}</span>
                                    <i class="fas fa-copy"></i>
                                </button>
                            </td>
                            <td>{{ $client->shifts->count() }}</td>
                            <td>{{ \Carbon\Carbon::parse($client->created_at)->format('d-m-Y') }}</td>
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
                                        <a class="dropdown-item" href="{{ route('clients.edit', $client->id) }}">
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
                                            data-target="#modal-delete-{{ $client->id }}">
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
                                        <a class="dropdown-item" href="{{ route('clients.show', $client->id) }}">
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
                                <x-adminlte-modal id="modal-delete-{{ $client->id }}" theme="danger"
                                    title="Eliminar ({{ $client->name }})">
                                    <span>El cliente {{ $client->name }} sera eliminado permanentemente y no podra ser
                                        recuperado</span>
                                    <br>
                                    <span>¿Desea continuar?</span>
                                    <x-slot name="footerSlot">
                                        <form method="POST" action="{{ route('clients.destroy', $client) }}">
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
                        <td colspan="5"></td>
                        <td colspan="1">
                            <x-adminlte-button x-on:click="createClient" id="create-client" label="Crear cliente"
                                theme="primary" icon="fas fa-plus" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5">
                            {{ $clients->appends($_GET)->links('custom.pagination') }}
                        </td>
                    </tr>
                </x-adminlte-datatable>
            </x-adminlte-card>
        </div>
    </div>

@stop


@push('js')
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
@endpush
