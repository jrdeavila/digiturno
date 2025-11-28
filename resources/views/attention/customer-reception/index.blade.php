@extends('layouts.app')

@section('title', 'Recepción de clientes')

@section('content_header')
    <div class="mb-1"></div>
@stop

@section('content')
    <div x-data="{ edit: {{ $searched && !isset($client) ? 'true' : 'false' }} }" class="row h-100">
        <div class="col-lg-12">
            @foreach (['error', 'success'] as $type)
                @if (session()->has($type))
                    <x-adminlte-alert theme="{{ $type === 'error' ? 'danger' : $type }}" dismissable>
                        {{ session($type) }}
                    </x-adminlte-alert>
                @endif
            @endforeach
            @if ($errors->any())
                <x-adminlte-alert theme="danger" dismissable>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </x-adminlte-alert>
            @endif
        </div>
        <div class="col-lg-3 col-md-6">
            @include('attention.customer-reception.fragments.create-shift')
            <div>
                @include('attention.customer-reception.fragments.distracted', [
                    'distractedShifts' => $distractedShifts->take(5),
                    'currentRoom' => $currentRoom,
                ])
            </div>
        </div>

        <div class="col-lg-5 col-md-6">
            @include('attention.customer-reception.fragments.shifts', ['shifts' => $shifts])
        </div>

        <div class="col-lg-4 col-md-12">
            @include('attention.customer-reception.fragments.statistics')
            @include('attention.customer-reception.fragments.modules', ['modules' => $modules])
        </div>
    </div>
@stop

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const clearSearchButton = document.getElementById('clear-search-client');
            if (clearSearchButton) {
                clearSearchButton.addEventListener('click', function(event) {
                    event.preventDefault();
                    window.location.href = "{{ route('attention.customer-reception.index') }}";
                });
            }
        });
    </script>
@endpush
