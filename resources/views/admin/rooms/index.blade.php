@extends('adminlte::page')

@section('title', 'Salas')

@section('content_header')
    <h1>Salas</h1>
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
            $heads = ['rooms.id', 'rooms.name', 'rooms.branch', 'rooms.actions.label'];
            $heads = array_map(function ($head) {
                return trans($head);
            }, $heads);
            $config = [
                'data' => $rooms,
                'order' => [[1, 'asc']],
                'columns' => [null, null, null, null, null, ['orderable' => false]],
            ];
        @endphp
        <x-adminlte-datatable id="table1" :heads="$heads" :config="$config">
            @foreach ($rooms as $room)
                <tr>
                    <td>{{ $room->id }}</td>
                    <td>{{ $room->name }}</td>
                    <td>{{ $room->branch->name }}</td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-default">{{ __('rooms.actions.label') }}</button>
                            <button type="button" class="btn btn-default dropdown-toggle dropdown-icon"
                                data-toggle="dropdown" aria-expanded="true">
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <div class="dropdown-menu" role="menu"
                                style="position: absolute; transform: translate3d(68px, 38px, 0px); top: 0px; left: 0px; will-change: transform;"
                                x-placement="bottom-start">
                                <a class="dropdown-item" href="{{ route('rooms.edit', $room->id) }}">
                                    <div class="row">
                                        <div class="col-6">
                                            <i class="fas fa-edit"></i>
                                        </div>
                                        <div class="col-6">
                                            {{ __('rooms.actions.edit') }}
                                        </div>
                                    </div>
                                </a>

                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" data-toggle="modal"
                                    data-target="#modal-delete-{{ $room->id }}">
                                    <div class="row">
                                        <div class="col-6">
                                            <i class="fas fa-trash"></i>
                                        </div>
                                        <div class="col-6">
                                            {{ __('rooms.actions.delete') }}
                                        </div>
                                    </div>
                                </a>



                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ route('rooms.show', $room->id) }}">
                                    <div class="row">
                                        <div class="col-6">
                                            <i class="fas fa-eye"></i>
                                        </div>
                                        <div class="col-6">
                                            {{ __('rooms.actions.view') }}
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <x-adminlte-modal id="modal-delete-{{ $room->id }}" theme="danger"
                            title="Eliminar ({{ $room->name }})">
                            <span>La sala sera eliminada permanentemente y no podra ser recuperada</span>
                            <br>
                            <span>¿Desea continuar?</span>
                            <x-slot name="footerSlot">
                                <form method="POST" action="{{ route('rooms.destroy', $room) }}">
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
                    <x-adminlte-button id="create-room" theme="primary" label="Crear" />
                </td>
                <td colspan="3">
                    <div class="float-right">
                        {{ $rooms->links('custom.pagination') }}
                    </div>
                </td>
            </tr>
        </x-adminlte-datatable>
    </x-adminlte-card>

@stop


@push('js')
    <script>
        $(document).ready(function() {
            $('#create-room').click(function() {
                window.location.href = "{{ route('rooms.create') }}";
            });
        });
    </script>
@endpush
