@props([
    'full' => false,
])

@php
    $width = $full ? '98%' : '70%';
@endphp
<div class="p-2" style="max-height: inherit; width: {{ $width }}; position: relative;">
    <div style="position: fixed; width: calc({{ $width }} - 100px);">
        <x-adminlte-card title="Turno actual" theme="primary" icon="fas fa-users">
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
            <div x-data="{
                moment: '{{ $shift->in_progress_started_at ?? $shift->created_at }}',
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
                    <x-adminlte-button id="finish-shift" label="Finalizar" class="btn btn-primary btn-block mt-3"
                        theme="success" icon="fas fa-check" />
                    <x-adminlte-button id="transfer-shift" label="Transferir" class="btn btn-primary btn-block mt-3"
                        theme="warning" icon="fas fa-exchange-alt" />
                    <x-adminlte-button id="distracted-shift" label="Distraido" class="btn btn-primary btn-block mt-3"
                        theme="danger" icon="fas fa-times" />
                    <x-adminlte-button id="call-shift" label="Llamar" class="btn btn-primary btn-block mt-3"
                        theme="primary" icon="fas fa-volume-up" />
                </div>

            </div>
        </x-adminlte-card>
    </div>
    <form id="form-distracted-shift" action="{{ route('attention.attention.shifts.distracted', $shift) }}"
        method="POST">
        @csrf
    </form>
    <form id="form-transfer-shift" action="{{ route('attention.attention.shifts.transfer', $shift) }}" method="POST">
        @csrf
    </form>
    <form id="form-call-shift" action="{{ route('attention.attention.shifts.call', $shift) }}" method="POST">
        @csrf
    </form>
    <form id="form-finish-shift" action="{{ route('attention.attention.shifts.finish', $shift) }}" method="POST">
        @csrf
    </form>
</div>

@php
    $attentionProfiles = $shift->room->attentionProfiles;
@endphp

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const finishShiftButton = document.getElementById('finish-shift');
            const distractedShiftButton = document.getElementById('distracted-shift');
            const callShiftButton = document.getElementById('call-shift');
            const transferShiftButton = document.getElementById('transfer-shift');

            function addServices(form) {
                let services = document.querySelectorAll('.service-checkbox');
                services = Array.from(services).map(service => {
                    let inputName = service.getAttribute('name');
                    let id = inputName.split('[')[1].split(']')[0];
                    return {
                        id: id,
                        checked: service.checked
                    }
                }).filter(service => service.checked);

                for (let i = 0; i < services.length; i++) {
                    let input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'services[' + services[i].id + ']';
                    input.value = services[i].id;
                    form.appendChild(input);
                }
            }

            transferShiftButton.addEventListener('click', function(event) {
                event.preventDefault();
                let form = document.getElementById('form-transfer-shift');
                let attentionProfiles = {!! json_encode($attentionProfiles) !!};
                attentionProfiles = attentionProfiles.reduce((acc, attentionProfile) => {
                    acc[attentionProfile.id] = attentionProfile.name;
                    return acc;
                }, {});
                addServices(form);
                sweetalert.fire({
                    title: 'Transferir turno',
                    text: '¿Estas seguro de transferir el turno?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Si, transferir',
                    cancelButtonText: 'Cancelar',
                    input: 'select',
                    inputOptions: attentionProfiles,

                    inputPlaceholder: 'Seleccione el perfil de atención',
                }).then((result) => {
                    let attentionProfileId = result.value;
                    let input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'attention_profile_id';
                    input.value = attentionProfileId;
                    form.appendChild(input);
                    form.submit();
                })

            })

            finishShiftButton.addEventListener('click', function(event) {
                event.preventDefault();
                let form = document.getElementById('form-finish-shift');
                addServices(form);
                form.submit();
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
