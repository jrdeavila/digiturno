@extends('adminlte::page')

@section('title', 'Crear servicio')

@section('content_header')
    <h1>Crear servicio</h1>
@stop

@section('content')

    <div class="row">
        <div class="col-md-6">
            <x-adminlte-card title="Crear servicio" theme="primary" icon="fas fa-edit">
                <form action="{{ route('services.store') }}" method="POST">
                    @csrf
                    <x-adminlte-input name="name" label="Nombre" value="{{ old('name') }}" />
                    <x-adminlte-select name="service_id" label="Servicio padre">
                        <option value="">Ninguno</option>
                        @foreach ($services as $service)
                            <option value="{{ $service->id }}">{{ $service->name }}</option>
                        @endforeach
                    </x-adminlte-select>
                    <x-adminlte-button type="submit" label="Guardar" theme="primary" icon="fas fa-edit" />
                </form>
            </x-adminlte-card>
        </div>
    </div>

@stop
