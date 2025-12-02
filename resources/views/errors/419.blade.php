@extends('adminlte::page')

@section('title', '')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>La sesión ha caducado</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Inicio</a></li>
                    <li class="breadcrumb-item active">419 Error de sesión</li>
                </ol>
            </div>
        </div>
    </div>
@stop



@section('content')
    <div class="error-page">
        <h2 class="headline text-danger">419</h2>

        <div class="error-content">
            <h3><i class="fas fa-exclamation-triangle text-danger"></i> ¡Ups! Algo salió mal.</h3>

            <p>
                Esto puede suceder si tu sesión ha caducado.
                Mientras tanto, puedes <a href="{{ url('/') }}">volver al panel</a> o intentar usar el formulario de
                búsqueda.
            </p>

            <form class="search-form">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Buscar">

                    <div class="input-group-append">
                        <button type="submit" name="submit" class="btn btn-danger"><i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop
