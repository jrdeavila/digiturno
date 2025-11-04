@extends('adminlte::page')


@section('title', 'Acceso denegado')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>403 Acceso denegado</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Inicio</a></li>
                    <li class="breadcrumb-item active">403 Acceso denegado</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="error-page">
        <h2 class="headline text-primary"> 403</h2>

        <div class="error-content">
            <h3><i class="fas fa-exclamation-triangle text-primary"></i> {{ $exception->getMessage() }}</h3>

            <p>
                No tienes permiso para acceder a esta sección.
                Mientras tanto, puedes <a href="{{ url('/') }}">volver al panel</a>
            </p>
        </div>
    </div>
@stop
