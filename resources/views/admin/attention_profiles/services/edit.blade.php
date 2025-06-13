@extends('adminlte::page')

@section('title', 'Editar servicios del perfil de atención ' . $attentionProfile->name)

@section('content_header')
    <div class="row justify-content-between align-items-center">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Editar los servicios del perfil de atención {{ $attentionProfile->name }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('attention-profiles.index') }}">Perfiles de atención</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('attention-profiles.show', $attentionProfile) }}">
                                {{ $attentionProfile->name }} </a>
                        </li>
                        <li class="breadcrumb-item active">Editar servicios</li>
                    </ol>
                </div>
            </div>
        </div>
    @stop

    @section('content')
        <div class="row">
            <div class="col-md-8">
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
            </div>
            <div class="col-md-8">
                <x-adminlte-card title="Editar servicios" theme="primary" icon="fas fa-edit">
                    @php
                        $services1 = $services->slice(0, floor($services->count() / 2));
                        $services2 = $services->slice(
                            floor($services->count() / 2),
                            $services->count() - floor($services->count() / 2),
                        );
                        $selectedServices = $attentionProfile->services->pluck('id')->toArray();
                    @endphp
                    <form action="{{ route('attention-profiles.services.update', $attentionProfile) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                @include('services.partials.service_tree', [
                                    'services' => $services1,
                                    'root' => true,
                                    'edit' => true,
                                    'selectedServices' => $selectedServices,
                                ])
                            </div>
                            <div class="col-md-6">
                                @include('services.partials.service_tree', [
                                    'services' => $services2,
                                    'root' => true,
                                    'edit' => true,
                                    'selectedServices' => $selectedServices,
                                ])
                            </div>
                        </div>

                        <x-adminlte-button type="submit" label="Guardar" theme="primary" icon="fas fa-edit" />
                    </form>

                </x-adminlte-card>
            </div>
        </div>

    @stop
