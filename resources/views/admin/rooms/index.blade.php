@extends('adminlte::page')

@section('title', 'Salas')

@section('content_header')
    <h1>Salas</h1>
@stop

@section('content')

    <x-adminlte-card theme="light" icon="fas fa-list" title="Salas" class="elevation-1">
        @session('success')
            <blockquote class="quote quote-success">
                <h5>{{ __('messages.success_title') ?? 'Genial!' }}</h5>
                <p>{{ session('success') }}</p>
            </blockquote>
        @endsession
        @session('error')
            <blockquote class="quote quote-danger">
                <h5>{{ __('messages.error_title') ?? 'Error!' }}</h5>
                <p>{{ session('error') }}</p>
            </blockquote>
        @endsession

        @php
            $heads = ['rooms.id', 'rooms.name', 'rooms.branch', 'rooms.actions.label'];
            $heads = array_map(fn($h) => trans($h), $heads);

            $config = [
                'data' => $rooms,
                'order' => [[1, 'asc']], // Orden por nombre
                'columns' => [
                    null, // ID
                    null, // Nombre
                    null, // Seccional
                    ['orderable' => false], // Acciones
                ],
            ];
        @endphp

        <x-slot name="toolsSlot">
            <x-adminlte-button label="Crear sala" theme="primary" icon="fas fa-plus" href="{{ route('rooms.create') }}" />
        </x-slot>

        <x-adminlte-datatable id="rooms-table" :heads="$heads" :config="$config">
            @foreach ($rooms as $room)
                <tr>
                    <td>{{ $room->id }}</td>
                    <td>
                        <a href="{{ route('rooms.show', $room->id) }}" class="text-primary font-weight-bold">
                            {{ $room->name }}
                        </a>
                    </td>
                    <td>{{ optional($room->branch)->name }}</td>
                    <td>
                        <div class="btn-group" role="group" aria-label="{{ __('rooms.actions.label') }}">
                            <a href="{{ route('rooms.show', $room->id) }}" class="btn btn-primary">
                                <i class="fas fa-eye mr-1"></i> {{ __('rooms.actions.view') }}
                            </a>
                            <a href="{{ route('rooms.edit', $room->id) }}" class="btn btn-warning">
                                <i class="fas fa-edit mr-1"></i> {{ __('rooms.actions.edit') }}
                            </a>
                            <button type="button" class="btn btn-danger btn-delete-room" data-id="{{ $room->id }}"
                                data-name="{{ $room->name }}">
                                <i class="fas fa-trash mr-1"></i> {{ __('rooms.actions.delete') }}
                            </button>
                        </div>
                    </td>
                </tr>
            @endforeach

            <tr>
                <td colspan="4">
                    <div class="float-right">
                        {{ $rooms->links('custom.pagination') }}
                    </div>
                </td>
            </tr>
        </x-adminlte-datatable>

        {{-- Formulario oculto para eliminación (reutilizable) --}}
        <form id="delete-room-form" method="POST" style="display:none;">
            @csrf
            @method('DELETE')
        </form>
    </x-adminlte-card>

@stop

@push('js')
    {{-- SweetAlert2: usa vendor si lo tienes instalado, o CDN en su defecto --}}
    <script src="{{ asset('vendor/sweetalert2/sweetalert2.all.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Eliminar sala con SweetAlert2
            document.querySelectorAll('.btn-delete-room').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const name = this.getAttribute('data-name');

                    Swal.fire({
                        title: "{{ __('rooms.delete.confirm_title') ?? 'Eliminar sala' }}",
                        html: `{{ __('rooms.delete.confirm_text_prefix') ?? 'La sala' }} <b>${name}</b> {{ __('rooms.delete.confirm_text_suffix') ?? 'será eliminada permanentemente. Esta acción no se puede deshacer.' }}`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: "{{ __('rooms.delete.confirm_yes') ?? 'Sí, eliminar' }}",
                        cancelButtonText: "{{ __('rooms.delete.confirm_cancel') ?? 'Cancelar' }}",
                        confirmButtonColor: '#e3342f',
                        cancelButtonColor: '#6c757d',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const form = document.getElementById('delete-room-form');
                            form.action = "{{ route('rooms.destroy', '__ID__') }}".replace(
                                '__ID__', id);
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
@endpush
