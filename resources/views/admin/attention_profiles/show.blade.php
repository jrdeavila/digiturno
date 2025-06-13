@extends('adminlte::page')

@section('title', 'Detalle del perfil de atención')

@section('content_header')
    <h1>Detalle de la sala {{ $attentionProfile->name }}</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-4">
            <x-adminlte-card title="Detalle de la sala" theme="primary" icon="fas fa-edit">
                <x-adminlte-input name="name" label="Nombre" value="{{ $attentionProfile->name }}" disabled />
            </x-adminlte-card>
        </div>

        <div class="col-md-8">
            <x-adminlte-card title="Servicios" theme="primary" icon="fas fa-list">
                <div class="row"></div>
                @include('services.partials.service_tree', [
                    'services' => $attentionProfile->services->whereNull('service_id')->sortBy('name'),
                    'root' => true,
                ])

                <x-adminlte-button label="Cambiar servicios" theme="primary" icon="fas fa-edit" id="edit-services" />

            </x-adminlte-card>
        </div>

    </div>

@stop


@push('js')
    <script>
        $(document).ready(function() {
            $('#edit-services').click(function() {
                window.location.href = "{{ route('attention-profiles.services.edit', $attentionProfile) }}";
            });
        });
    </script>
@endpush
