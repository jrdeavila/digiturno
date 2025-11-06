@props([
    'full' => false,
])

@php
    $width = $full ? '98%' : '75%';
@endphp

<div class="p-2" style="max-height: inherit; width: {{ $width }}; position: relative;">
    <div style="position: fixed; width: calc({{ $width }} - 250px);">
        <x-adminlte-card title="Calificar turno" theme="primary" icon="fas fa-users">
            <div class="row">
                <div class="col-md-6">
                    <x-adminlte-info-box icon="far fa-user" title="{{ $shift->client->name }}"
                        text="{{ $shift->client->dni }}" theme="light" />
                </div>

                <div class="col-md-6">
                    <x-adminlte-info-box title="Perfil de atencion" theme="light"
                        text="{{ $shift->attentionProfile->name }}" />
                </div>
            </div>
        </x-adminlte-card>
    </div>

</div>

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const finishShiftButton = document.getElementById('finish-shift');
            const distractedShiftButton = document.getElementById('distracted-shift');
            const callShiftButton = document.getElementById('call-shift');

            finishShiftButton.addEventListener('click', function(event) {
                event.preventDefault();
                document.getElementById('form-finish-shift').submit();
            });

            distractedShiftButton.addEventListener('click', function(event) {
                event.preventDefault();
                document.getElementById('form-distracted-shift').submit();
            });

            callShiftButton.addEventListener('click', function(event) {
                event.preventDefault();
                document.getElementById('form-call-shift').submit();
            });
        });
    </script>
@endpush
