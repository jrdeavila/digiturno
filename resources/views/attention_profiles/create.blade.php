@extends('adminlte::page')

@section('title', 'Crear servicio')

@section('content_header')
    <h1>Crear servicio</h1>
@stop

@section('content')

    <form action="{{ route('attention-profiles.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-12">
                @if ($errors->any())
                    <blockquote class="quote quote-danger">
                        <h5>Ups! Parece que hubo algun error.</h5>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </blockquote>
                @endif
                @session('success')
                    <blockquote class="quote quote-success">
                        <h5>Genial!</h5>
                        <p>{{ session('success') }}</p>
                    </blockquote>
                @endsession
                @session('error')
                    <blockquote class="quote quote-danger">
                        <h5>Ups! Parece que hubo algun error.</h5>
                        <p>{{ session('error') }}</p>
                    </blockquote>
                @endsession
            </div>
            <div class="col-md-3">
                <x-adminlte-card title="Crear servicio" theme="primary" icon="fas fa-edit">
                    <x-adminlte-input name="name" label="Nombre" value="{{ old('name') }}" />
                    <x-adminlte-button type="submit" label="Guardar" theme="primary" icon="fas fa-edit" />
                </x-adminlte-card>
            </div>
            <div class="col-md-9">
                <x-adminlte-card title="Listado de servicios" theme="primary" icon="fas fa-edit">
                    @include('services.partials.service_tree', [
                        'services' => $services->whereNull('service_id')->sortBy('name'),
                        'root' => true,
                        'edit' => true,
                    ])
                </x-adminlte-card>
            </div>
        </div>
    </form>

@stop
