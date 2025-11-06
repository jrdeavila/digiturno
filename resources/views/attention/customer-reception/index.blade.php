@extends('adminlte::page')

@section('title', 'Recepción de clientes')

@section('content_header')
    <div class="mb-1"></div>
@stop

@section('content')
    <div x-data="{ edit: {{ $searched && !isset($client) ? 'true' : 'false' }} }" class="row h-100">
        <div class="col-12">
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
        <div class="{{ count($shifts) > 0 ? 'col-md-3' : 'col-md-7' }}">
            @include('attention.customer-reception.fragments.create-shift')

        </div>
        @if (count($distractedShifts) > 0)
            <div class="col-md-3">
                @include('attention.customer-reception.fragments.distracted')
            </div>
        @endif
        @if (count($shifts) > 0)
            <div class="col-md-5">
                @include('attention.customer-reception.fragments.shifts')
            </div>
        @endif

        <div class="col-md-4">
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
                    window.location.href = '{{ route('attention.customer-reception.index') }}';
                });
            }
        });
    </script>
@endpush
