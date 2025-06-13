@extends('adminlte::page')

@section('title', 'Editar sala')

@section('content_header')
    <h1>Editar sala {{ $room->name }}</h1>
@stop

@section('content')

    <div class="row">
        <div class="col-md-6">
            <x-adminlte-card title="Editar sala" theme="primary" icon="fas fa-edit">
                <form action="{{ route('rooms.update', $room) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <x-adminlte-input name="name" label="Nombre" value="{{ old('name', $room->name) }}" />
                    <x-adminlte-select name="branch_id" label="Seccional">
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}"
                                {{ $branch->id == old('branch_id', $room->branch_id) ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </x-adminlte-select>
                    <input type="hidden" name="branch_id" value="{{ $branch->id }}">
                    <x-adminlte-button type="submit" label="Guardar" theme="primary" />
                </form>
            </x-adminlte-card>
        </div>
    </div>

@stop
