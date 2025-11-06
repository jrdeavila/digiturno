@props([
    'full' => false,
])

@php
    $width = $full ? '98%' : '75%';
@endphp
<div class="p-2" style="max-height: inherit; width: {{ $width }}; position: relative;">
    <div style="position: fixed; width: calc({{ $width }} - 250px);">
        <x-adminlte-card title="Turno actual" theme="primary" icon="fas fa-users">
            <div x-data="{
                moment: '{{ $shift->created_at }}',
                time: '{{ $shift->created_at }}',
                interval: null,
                updateTime() {
                    let interval = setInterval(() => {
                        // Calcular el timpo transcurrido en minutos
                        let now = new Date();
                        let then = new Date(this.moment);
                        let diff = now.getTime() - then.getTime();
                        let minutes = Math.floor(diff / 1000 / 60);

                        if (minutes < 1) {
                            let seconds = Math.floor(diff / 1000);
                            this.time = seconds + ' seg';
                        } else if (minutes < 60) {
                            let seconds = Math.floor(diff / 1000) % 60;
                            this.time = minutes + ' min ' + seconds + ' seg';
                        } else {
                            let hours = Math.floor(minutes / 60);
                            let remainingMinutes = minutes % 60;
                            this.time = hours + ' hr ' + remainingMinutes + ' min';
                        }

                    }, 1000);
                    this.interval = interval
                },
                onUnmount() {
                    clearInterval(this.interval)
                }
            }" x-init="updateTime()" x-on:destroy="onUnmount()" class="row">
                <div class="col-md-4">
                    <x-adminlte-info-box icon="far fa-user" title="{{ $shift->client->name }}"
                        text="{{ $shift->client->dni }}" theme="light" />
                </div>
                <div class="col-md-4">
                    <x-adminlte-info-box icon="far fa-clock" title="Tiempo transcurrido" theme="light">
                        <x-slot name="text">
                            <span x-text="time"></span>
                        </x-slot>
                    </x-adminlte-info-box>
                </div>
                <div class="col-md-4">
                    <x-adminlte-info-box title="Perfil de atencion" theme="light"
                        text="{{ $shift->attentionProfile->name }}" />
                </div>
                <div class="col-md-12 btn-group">
                    <x-adminlte-button id="finish-shift" label="Finalizar" type="submit"
                        class="btn btn-primary btn-block mt-3" theme="success" icon="fas fa-check" />
                    <x-adminlte-button id="distracted-shift" label="Distraido" type="submit"
                        class="btn btn-primary btn-block mt-3" theme="danger" icon="fas fa-times" />
                    <x-adminlte-button id="call-shift" label="Llamar" type="submit"
                        class="btn btn-primary btn-block mt-3" theme="primary" icon="fas fa-volume-up" />
                </div>

                <form id="form-finish-shift" action="{{ route('attention.attention.shifts.finish', $shift) }}"
                    method="POST">
                    @csrf
                </form>
                <form id="form-distracted-shift" action="{{ route('attention.attention.shifts.distracted', $shift) }}"
                    method="POST">
                    @csrf
                </form>
                <form id="form-call-shift" action="{{ route('attention.attention.shifts.call', $shift) }}"
                    method="POST">
                    @csrf
                </form>
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
