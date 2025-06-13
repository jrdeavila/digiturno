@extends('adminlte::page')

@section('title', 'Crear seccional')

@section('content_header')
    <h1>Crear seccional</h1>
@stop

@section('content')

    <div class="row">
        <div class="col-md-6">
            <x-adminlte-card title="Editar seccional" theme="primary" icon="fas fa-edit">
                <form action="{{ route('branches.store') }}" method="POST">
                    @csrf
                    <x-adminlte-input name="name" label="Nombre" value="{{ old('name') }}" />
                    <x-adminlte-input name="address" label="Direccion" value="{{ old('address') }}" />
                    <x-adminlte-button type="submit" label="Guardar" theme="primary" />
                </form>
            </x-adminlte-card>
        </div>
    </div>
@stop
