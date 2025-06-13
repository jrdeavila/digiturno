@extends('adminlte::page')

@section('title', 'Crear sala')

@section('content_header')
    <h1>Crear sala para la seccional {{ $branch?->name }}</h1>
@stop

@section('content')

    <div class="row">
        <div class="col-md-6">
            <x-adminlte-card title="Crear sala" theme="primary" icon="fas fa-edit">
                <form action="{{ route('rooms.store') }}" method="POST">
                    @csrf
                    <x-adminlte-input name="name" label="Nombre" value="{{ old('name') }}" />
                    @if (count($branch->toArray()) === 0)
                        <x-adminlte-select name="branch_id" label="Seccional">
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </x-adminlte-select>
                    @else
                        <input type="hidden" name="branch_id" value="{{ $branch->id }}">
                    @endif
                    <x-adminlte-button type="submit" label="Guardar" theme="primary" />
                </form>
            </x-adminlte-card>
        </div>
    </div>

@stop
