@extends('adminlte::page')

@section('title', 'Atención')

@section('content_header')
    <h1>Atención</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <p>Esta es la pantalla de atención. Aquí se mostrarán los pacientes que están siendo atendidos.</p>
            <p>Los pacientes se ordenan por el tiempo de espera, y se muestran en orden de llegada.</p>
            <p>El personal puede actualizar el estado de los pacientes desde aquí.</p>
        </div>
    </div>
@stop
