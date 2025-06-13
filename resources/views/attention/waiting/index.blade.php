@extends('adminlte::page')

@section('title', 'Pantalla de Espera')

@section('content_header')
    <h1>Pantalla de Espera</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <p>Esta es la pantalla de espera. Aquí se mostrarán los pacientes que están esperando para ser atendidos.</p>
            <p>Los pacientes se ordenan por el tiempo de espera, y se muestran en orden de llegada.</p>
            <p>El personal puede actualizar el estado de los pacientes desde aquí.</p>
        </div>
    </div>
@stop
