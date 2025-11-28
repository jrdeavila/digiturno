@props([
    'full' => false,
])

@php
    $width = $full ? '98%' : '70%';
@endphp

<div x-data="{
    device: null,
    qualifyShift(qualification) {
        $refs.qualificationInput.value = ({
            1: '{{ \App\Enums\QualificationOption::Bad }}',
            2: '{{ \App\Enums\QualificationOption::Regular }}',
            3: '{{ \App\Enums\QualificationOption::Good }}',
            4: '{{ \App\Enums\QualificationOption::Excellent }}',
        })[qualification];
        $refs.qualifyShiftForm.submit();
    },
    addListener() {
        if (this.device) {
            this.device.oninputreport = (e) => {
                const { data } = e;
                const arr = new Int8Array(data.buffer);
                const option = arr[0];

                sweetalert.fire({
                    title: 'Turno calificado',
                    icon: 'success',
                    showConfirmButton: false,
                    timer: 1500,
                }).then(() => {
                    this.qualifyShift(option);
                })
            }
            sweetalert.fire({
                title: 'Esperando calificación',
                icon: 'info',
                showConfirmButton: false,
                timer: 1500
            })
        }
    },
    verifyDevice() {
        if (!this.device) {
            requestQualificationModule()
                .then(device => {
                    if (device) {
                        sweetalert.fire({
                            title: 'Dispositivo conectado',
                            icon: 'info',
                            showConfirmButton: false,
                            timer: 1500
                        })
                        device.open();
                        this.device = device;
                        this.addListener();
                    }
                }, error => {
                    sweetalert.fire({
                        title: 'Dispositivo no conectado',
                        icon: 'error',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                    })
                });
        }
        this.addListener();
    }
}" class="p-2" style="max-height: inherit; width: {{ $width }}; position: relative;">
    <form x-ref="qualifyShiftForm" action="{{ route('attention.attention.shifts.qualify', $shift) }}" method="POST">
        <input type="hidden" name="qualification" x-ref="qualificationInput">
        @csrf
    </form>
    <div style="position: fixed; width: calc({{ $width }} - 100px);">
        <x-adminlte-card title="Calificar turno" theme="light" icon="fas fa-users">

            <x-slot name="toolsSlot">
                <button x-ref="verifyDeviceButton" type="button" style="display: none"
                    x-on:click="verifyDevice()"></button>
                <x-adminlte-button label="Calificar" theme="primary" icon="fas fa-check" x-on:click="verifyDevice()" />
            </x-slot>
            <div class="row">
                <div class="col-md-12">
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
        async function requestQualificationModule() {
            try {
                // Request Device
                const devices = await navigator.hid.requestDevice({
                    filters: [{
                        vendorId: 0x461
                    }],
                });
                if (devices.length > 0) {
                    return devices[0];
                }
                return null;
            } catch (error) {
                console.error(error);
                return null;
            }
        }
    </script>
@endpush
