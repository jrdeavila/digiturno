@extends('adminlte::page')

@section('title', 'Reporte de turnos')

@section('content_header')
    <h1>Reporte de turnos</h1>
@stop

@section('content')

    <div class="container py-3">
        <div class="row justify-content-center">

            <div class="card" style="width: 18rem; cursor: pointer">
                <div class="card-body">
                    <div class="d-flex flex-column align-items-center">
                        <i class="fas fa-download fa-3x text-primary"></i>
                        <a href="{{ $url }}"
                            class="text-center font-weight-bold text-uppercase text-muted  mt-3">Descargar reporte</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop
