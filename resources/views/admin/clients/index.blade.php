@extends('adminlte::page')

@section('title', 'Clientes')

@section('content_header')
    <h1>Clientes ({{ number_format($clients->total()) }})</h1>
@stop

@section('content')

    <div class="row">
        <div class="col-xl-12">
            @session('success')
                <blockquote class="quote quote-success">
                    <h5>Genial!</h5>
                    <p>{{ session('success') }}</p>
                </blockquote>
            @endsession
            @session('error')
                <blockquote class="quote quote-danger">
                    <h5>Ups! Parece que hubo algún error.</h5>
                    <p>{{ session('error') }}</p>
                </blockquote>
            @endsession
        </div>

        <div class="col-xl-12">
            <x-adminlte-card title="Filtros" theme="primary" icon="fas fa-filter">
                <form id="filters-form" class="row" action="{{ route('clients.index') }}" method="GET">
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
                    <div class="col-md-4 d-flex align-items-end">
                        <x-adminlte-button class="mt-3 mr-2 w-100" type="submit" label="Buscar" theme="primary"
                            icon="fas fa-search" />
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <x-adminlte-button id="filters-clear" class="mt-3 w-100" type="button" label="Limpiar"
                            theme="outline-primary" icon="fas fa-eraser" />
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <x-adminlte-button id="create-client-top" class="mt-3 w-100" type="button" label="Crear cliente"
                            theme="success" icon="fas fa-plus" />
                    </div>
                </form>
            </x-adminlte-card>
        </div>

        <div class="col-xl-12">
            <x-adminlte-card title="Clientes" theme="primary" icon="fas fa-users">
                @php
                    $heads = [
                        'ID',
                        ['label' => 'Nombre', 'width' => 30],
                        ['label' => 'Documento de identidad', 'width' => 20],
                        '# de turnos',
                        'Fecha de registro',
                        ['label' => 'Acciones', 'width' => 20],
                    ];
                    $config = [
                        'data' => [],
                        'order' => [[0, 'asc'], [1, 'asc']],
                    ];
                @endphp

                <x-adminlte-datatable id="clients" :heads="$heads" :config="$config">
                    @foreach ($clients as $client)
                        <tr>
                            <td>{{ $client->id }}</td>
                            <td>
                                <a href="{{ route('clients.show', $client->id) }}" class="text-primary font-weight-bold">
                                    {{ $client->name }}
                                </a>
                            </td>
                            <td>
                                <button class="btn btn-outline-primary w-100 btn-copy-dni" data-dni="{{ $client->dni }}">
                                    <span>{{ number_format((int) $client->dni, 0, '', '.') }}</span>
                                    <i class="fas fa-copy ml-2"></i>
                                </button>
                            </td>
                            <td>{{ $client->shifts->count() }}</td>
                            <td>{{ \Carbon\Carbon::parse($client->created_at)->translatedFormat('d-m-Y') }}</td>
                            <td>
                                <div class="btn-group" role="group" aria-label="Acciones">
                                    <a class="btn btn-primary" href="{{ route('clients.show', $client->id) }}">
                                        <i class="fas fa-eye mr-1"></i> Ver
                                    </a>
                                    <a class="btn btn-warning" href="{{ route('clients.edit', $client->id) }}">
                                        <i class="fas fa-edit mr-1"></i> Editar
                                    </a>
                                    <button type="button" class="btn btn-danger btn-delete-client"
                                        data-id="{{ $client->id }}" data-name="{{ $client->name }}">
                                        <i class="fas fa-trash mr-1"></i> Eliminar
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach

                    {{-- Fila de acciones inferiores --}}
                    <tr>
                        <td colspan="5"></td>
                        <td class="text-right">
                            <a href="{{ route('clients.create') }}" class="btn btn-success">
                                <i class="fas fa-plus mr-1"></i> Crear cliente
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6">
                            <div class="d-flex justify-content-end">
                                {{ $clients->appends($_GET)->links('custom.pagination') }}
                            </div>
                        </td>
                    </tr>
                </x-adminlte-datatable>

                {{-- Formulario oculto para eliminación con SweetAlert2 --}}
                <form id="delete-client-form" method="POST" style="display:none;">
                    @csrf
                    @method('DELETE')
                </form>
            </x-adminlte-card>
        </div>
    </div>

@stop

@push('js')
    {{-- SweetAlert2 (usa tu vendor si ya lo tienes) --}}
    <script src="{{ asset('vendor/sweetalert2/sweetalert2.all.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Limpiar filtros
            document.getElementById('filters-clear').addEventListener('click', function() {
                const form = document.getElementById('filters-form');
                form.querySelectorAll('input, select').forEach(el => el.value = '');
                window.location.href = "{{ route('clients.index') }}";
            });

            // Crear cliente desde filtros
            document.getElementById('create-client-top').addEventListener('click', function() {
                window.location.href = "{{ route('clients.create') }}";
            });

            // Copiar DNI con toast
            document.querySelectorAll('.btn-copy-dni').forEach(btn => {
                btn.addEventListener('click', function() {
                    const dni = this.getAttribute('data-dni') || '';
                    navigator.clipboard.writeText(dni).then(() => {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'DNI copiado',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }).catch(() => {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            title: 'No se pudo copiar',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    });
                });
            });

            // Eliminar cliente con SweetAlert2
            document.querySelectorAll('.btn-delete-client').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const name = this.getAttribute('data-name');

                    Swal.fire({
                        title: `Eliminar cliente`,
                        html: `El cliente <b>${name}</b> será eliminado permanentemente.<br><small>Esta acción no se puede deshacer.</small>`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar',
                        confirmButtonColor: '#e3342f',
                        cancelButtonColor: '#6c757d',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const form = document.getElementById('delete-client-form');
                            form.action = "{{ route('clients.destroy', '__ID__') }}"
                                .replace('__ID__', id);
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
@endpush
