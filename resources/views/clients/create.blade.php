@extends('adminlte::page')

@section('title', 'Crear cliente')

@section('content_header')
    <h1>Crear cliente</h1>
@stop

@section('content')

    <div class="row">

        <div class="col-md-6">
            <x-adminlte-card title="Crear cliente" theme="primary" icon="fas fa-edit">
                <form class="row" action="{{ route('clients.store') }}" method="POST">
                    @csrf
                    <div class="col-md-6">
                        <x-adminlte-input name="name" label="Nombre" value="{{ old('name') }}" />
                    </div>
                    <div class="col-md-6">
                        <x-adminlte-input name="dni" label="Documento de identidad" value="{{ old('dni') }}" />
                    </div>
                    <div class="col-md-12">

                        <x-adminlte-select name="client_type_id" label="Tipo de cliente">
                            @foreach ($clientTypes as $clientType)
                                <option value="{{ $clientType->id }}"
                                    {{ old('client_type_id') == $clientType->id ? 'selected' : '' }}>
                                    {{ $clientType->name }}</option>
                            @endforeach
                        </x-adminlte-select>
                    </div>
                    <div class="col-md-12">
                        <x-adminlte-button type="submit" label="Guardar" theme="primary" />
                    </div>
                </form>
            </x-adminlte-card>
        </div>
    </div>

@stop
