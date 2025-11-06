@extends('adminlte::page')

@section('title', 'Atención')

@section('content_header')
    @if (null !== $currentModule)
        <h1>Sede {{ $currentModule->room?->branch?->name }} - Sala {{ $currentModule->room?->name }} (Modulo
            {{ $currentModule->name }})</h1>
    @else
        <h1>Atención</h1>
    @endif
@stop

@section('content')
    <div class="row p-2" style="min-height: calc(100vh - 130px); width:100%;">
        @if (null === $currentModule)
            <div class="row h-100 w-100">
                <div class="col-md-12 mb-2">
                    <h4>Seleccione un modulo</h4>
                </div>
                <div class="col-md-12">
                    @include('attention.attention.fragments.modules', ['modules' => $modules])
                </div>
            </div>
        @else
            @php
                $completedShift = $currentModule->completedShifts->first();
                $currentShift = $currentModule->inProgressShifts->first();
                $showLeftColumn = null !== $currentShift || $currentModule->distractedShifts->count() > 0;
            @endphp
            @if (null !== $currentShift)
                @include('attention.attention.fragments.attention-profiles', [
                    'attentionProfiles' => $currentModule->attentionProfiles,
                ])
            @else
                @if ($currentModule->distractedShifts->count() > 0)
                    @include('attention.attention.fragments.distracted', [
                        'distractedShifts' => $currentModule->distractedShifts,
                    ])
                @endif
            @endif

            @if ($completedShift)
                @include('attention.attention.fragments.qualify-shift', [
                    'shift' => $completedShift,
                    'full' => !$showLeftColumn,
                ])
            @else
                @if ($currentShift)
                    @include('attention.attention.fragments.current-shift', [
                        'shift' => $currentShift,
                        'full' => !$showLeftColumn,
                    ])
                @else
                    @include('attention.attention.fragments.shifts', [
                        'shifts' => $currentModule->shifts,
                        'full' => !$showLeftColumn,
                    ])
                @endif
            @endif
        @endif
    </div>
@stop


@push('js')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
@endpush
