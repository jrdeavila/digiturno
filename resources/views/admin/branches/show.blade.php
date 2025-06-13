@extends('adminlte::page')

@section('title', 'Detalle de seccional')

@section('content_header')
    <h1>Seccional {{ $branch->name }}</h1>
@stop

@section('content')

    <div class="row">
        <div class="col-md-6">
            <x-adminlte-card title="Detalle de seccional" theme="primary" icon="fas fa-edit">
                <x-adminlte-input name="name" label="Nombre" value="{{ $branch->name }}" disabled />
                <x-adminlte-input name="address" label="Direccion" value="{{ $branch->address }}" disabled />
            </x-adminlte-card>
        </div>

        <div class="col-md-6">
            <x-adminlte-card title="Salas" theme="primary" icon="fas fa-list">
                @php
                    $heads = ['ID', 'Nombre'];
                    $config = [
                        'data' => [],
                        'order' => [[0, 'asc'], [1, 'asc']],
                    ];
                @endphp
                <x-adminlte-datatable id="rooms" :heads="$heads" :config="$config">
                    @foreach ($branch->rooms as $room)
                        <tr>
                            <td>{{ $room->id }}</td>
                            <td>{{ $room->name }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="2">
                            <x-adminlte-button id="create-room" label="Crear sala" theme="primary" icon="fas fa-plus" />
                        </td>
                    </tr>
                </x-adminlte-datatable>
            </x-adminlte-card>
        </div>
    </div>

@stop


@push('js')
    <script>
        $(document).ready(function() {
            $('#create-room').click(function() {
                window.location.href = "{{ route('branches.rooms.create', $branch) }}";
            });
        });
    </script>
@endpush
