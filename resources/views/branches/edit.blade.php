@extends('adminlte::page')

@section('title', 'Editar seccional')

@section('content_header')
    <h1>Seccional {{ $branch->name }}</h1>
@stop

@section('content')

    <div class="row">
        <div class="col-md-6">
            <x-adminlte-card title="Editar seccional" theme="primary" icon="fas fa-edit">
                <form action="{{ route('branches.update', $branch) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <x-adminlte-input name="name" label="Nombre" value="{{ $branch->name }}" />
                    <x-adminlte-input name="address" label="Direccion" value="{{ $branch->address }}" />
                    <x-adminlte-button type="submit" label="Guardar" theme="primary" />
                </form>
            </x-adminlte-card>
        </div>
    </div>
@stop
