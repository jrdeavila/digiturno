@props([
    'full' => false,
])
@php
    $width = $full ? '98%' : '70%';
    $modules = $currentModule->room->modules()->enabled()->online()->with('attentionProfiles')->get();
@endphp
<div x-data="{
    shifts: JSON.parse('{{ json_encode($shifts->toArray()) }}'),
    total: {{ $shifts->count() }},
    callShiftUrl: '{{ route('attention.attention.shifts.call', '__ID__') }}',
    changeModuleUrl: '{{ route('attention.attention.shifts.change-module', '__ID__') }}',
    callShift(shift) {
        let url = this.callShiftUrl.replace('__ID__', shift.id);
        return url;
    },
    distractedShiftUrl: '{{ route('attention.attention.shifts.distracted', '__ID__') }}',
    distractedShift(shift) {
        let url = this.distractedShiftUrl.replace('__ID__', shift.id);
        return url;
    },
    upShiftUrl: '{{ route('attention.attention.shifts.up', '__ID__') }}',
    upShift(shift) {
        let url = this.upShiftUrl.replace('__ID__', shift.id);
        return url;
    },
    changeModule(shift) {
        let availableModules = modules.filter((module) => module.id !== shift.module_id && module.attention_profiles.some((profile) => profile.id === shift.attention_profile_id)).reduce((acc, module) => {
            acc[module.id] = `Modulo ${module.name}`;
            return acc;
        }, {});

        if (Object.keys(availableModules).length > 0) {

            sweetalert.fire({
                title: 'Cambiar de módulo',
                text: '¿Desea cambiar de módulo del turno?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si, cambiar',
                cancelButtonText: 'Cancelar',
                input: 'select',
                inputOptions: availableModules,
                inputPlaceholder: 'Seleccione el módulo',

            }).then((result) => {
                if (result.value) {
                    let input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'module_id';
                    input.value = result.value;
                    $refs.changeModuleForm.appendChild(input);
                    $refs.changeModuleForm.action = this.changeModuleUrl.replace('__ID__', shift.id);
                    $refs.changeModuleForm.submit();
                }
            })
        } else {
            sweetalert.fire({
                title: 'Cambiar de módulo',
                text: 'No hay módulos disponibles',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Aceptar',
                cancelButtonText: 'Cancelar',
            })
        }
    },

    listener() {
        let channel = Echo.channel('modules.' + {{ $currentModule->id }} + '.shifts');

        let roomChannel = Echo.channel('rooms.' + {{ $currentModule->room->id }} + '.shifts');

        channel.subscribed(() => {
            console.log('subscribed to modules.' + {{ $currentModule->id }} + '.shifts');
        })

        channel.cancelSubscription = () => {
            console.log('unsubscribed from modules.' + {{ $currentModule->id }} + '.shifts');
        }

        roomChannel.subscribed(() => {
            console.log('subscribed to rooms.' + {{ $currentModule->room->id }} + '.shifts');
        })

        roomChannel.cancelSubscription = () => {
            console.log('unsubscribed from rooms.' + {{ $currentModule->room->id }} + '.shifts');
        }

        channel.listen('.shift.created', (e) => {
            this.shifts.push(e.shift)
        })



        channel.listen('.shift.updated', (e) => {
            let findShift = this.shifts.find((shift) => {
                return shift.id === e.shift.id
            });
            if (findShift) {

                this.shifts = this.shifts.map((shift) => {
                    if (shift.id === e.shift.id && (e.shift.state ===
                            '{{ \App\Enums\ShiftState::Pending }}' || e.shift.state ===
                            '{{ \App\Enums\ShiftState::PendingTransferred }}')) {
                        return e.shift;
                    }
                    return shift;
                })
            } else {
                if (e.shift.state === '{{ \App\Enums\ShiftState::Pending }}' || e.shift.state ===
                    '{{ \App\Enums\ShiftState::PendingTransferred }}' && e.shift.module_id === {{ $currentModule->id }}) {
                    this.shifts.push(e.shift)
                }
            }

            this.shifts = this.shifts.filter((shift) => {
                return shift.module_id === {{ $currentModule->id }}
            })
        });
        roomChannel.listen('.shift.updated', (e) => {
            let findShift = this.shifts.find((shift) => {
                return shift.id === e.shift.id
            });
            if (findShift) {

                this.shifts = this.shifts.map((shift) => {
                    if (shift.id === e.shift.id && (e.shift.state ===
                            '{{ \App\Enums\ShiftState::Pending }}' || e.shift.state ===
                            '{{ \App\Enums\ShiftState::PendingTransferred }}')) {
                        return e.shift;
                    }
                    return shift;
                })
            } else {
                if (e.shift.state === '{{ \App\Enums\ShiftState::Pending }}' || e.shift.state ===
                    '{{ \App\Enums\ShiftState::PendingTransferred }}' && e.shift.module_id === {{ $currentModule->id }}) {
                    this.shifts.push(e.shift)
                }
            }

            this.shifts = this.shifts.filter((shift) => {
                return shift.module_id === {{ $currentModule->id }}
            })
        });

        channel.listen('.shift.deleted', (e) => {
            this.shifts = this.shifts.filter((shift) => {
                return shift.id !== e.shift.id
            })
        })
    }
}" x-init="listener()" class="p-2"
    style="max-height: inherit; width: {{ $width }}; position: relative;">
    <x-adminlte-card title="Turnos pendientes" theme="primary" icon="fas fa-users"
        style="position: fixed; width: calc({{ $width }} - 100px);">
        <x-slot name="toolsSlot">
            Total: <strong><span class="badge badge-success" x-text="total">/span></strong>
        </x-slot>
        <div>
            <template x-if="shifts.length == 0">
                <p class="text-center">No hay turnos pendientes.</p>
            </template>
            <template x-if="shifts.length > 0">
                <ul class="list-group">
                    <template x-for="shift in shifts">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div class="d-flex flex-column">
                                <div class="d-flex flex-column justify-content-start align-items-start">
                                    <div>
                                        <div class="badge badge-primary">
                                            <span class="font-weight-bold text-md mx-2"
                                                x-text="shift.attention_profile.name"></span>
                                        </div>
                                    </div>
                                    <div class="row align-items-center justify-content-start">
                                        <i class="fas fa-clock text-primary mr-2"></i>
                                        <span x-data="{
                                            moment: shift.created_at,
                                            time: '',
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
                                                        this.time = minutes + ' min';
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
                                        }" x-init="updateTime()" x-on:destroy="onUnmount()"
                                            class="text-muted" x-text="time"></span>
                                    </div>

                                </div>

                            </div>
                            <div class="d-flex align-items-center">
                                <div class="d-flex align-items-center" x-data="{
                                    shift: shift,
                                    slug: shift.client.client_type.slug,
                                    definition: {
                                        color: '',
                                        icon: '',
                                        text: ''
                                    },
                                    loadDefinition() {
                                        let definition = null;
                                        if (this.slug == 'standard') {
                                            definition = {
                                                color: 'bg-primary',
                                                icon: 'fas fa-user',
                                                text: 'Estándar'
                                            }
                                        }
                                        if (this.slug == 'preferential') {
                                            definition = {
                                                color: 'bg-warning',
                                                icon: 'fas fa-star',
                                                text: 'Preferencial'
                                            }
                                        }
                                        if (this.slug == 'processor') {
                                            definition = {
                                                color: 'bg-info',
                                                icon: 'fas fa-bolt',
                                                text: 'Tramitador'
                                            }
                                        }
                                        if (this.slug == 'afiliate') {
                                            definition = {
                                                color: 'bg-success',
                                                icon: 'fas fa-users',
                                                text: 'Afiliado'
                                            }
                                        }
                                        this.definition = definition
                                    }
                                }" x-init="loadDefinition()">
                                    <div x-bind:class="`${definition.color} avatar-xs rounded-circle mr-2 d-flex align-items-center justify-content-center`"
                                        style="width: 30px; height: 30px;">
                                        <i x-bind:class="definition.icon"></i>
                                    </div>


                                </div>
                                <strong x-text="shift.client.name"></strong> (<span x-text="shift.client.dni"></span>)
                            </div>

                            <div class="d-flex align-items-center">

                                <div class="btn-group ml-2">
                                    <form x-ref="changeModuleForm" method="POST">
                                        @csrf
                                    </form>
                                    <x-adminlte-button class="ml-1" type="submit" theme="primary"
                                        icon="fas fa-exchange-alt" x-on:click="changeModule(shift)" />
                                    <form x-ref="upShiftForm" method="POST">
                                        @csrf
                                        <x-adminlte-button class="ml-1" type="submit" theme="success"
                                            icon="fas fa-arrow-up"
                                            x-on:click="$refs.upShiftForm.action=upShift(shift); $refs.upShiftForm.submit()" />
                                    </form>
                                    <form x-ref="distractedShiftForm" method="POST">
                                        @csrf
                                        <x-adminlte-button class="ml-1" type="submit" theme="danger"
                                            icon="fas fa-arrow-down"
                                            x-on:click="$refs.distractedShiftForm.action=distractedShift(shift); $refs.distractedShiftForm.submit()" />
                                    </form>
                                    <form x-ref="callShiftForm" method="POST">
                                        @csrf
                                        <x-adminlte-button class="ml-1" type="submit" theme="primary"
                                            icon="fas fa-volume-up"
                                            x-on:click="$refs.callShiftForm.action=callShift(shift); $refs.callShiftForm.submit()" />
                                    </form>
                                </div>
                            </div>

                        </li>
                    </template>
                </ul>
            </template>
        </div>
    </x-adminlte-card>

</div>

@push('js')
    <script>
        let modules = {!! json_encode($modules) !!};
    </script>
@endpush
