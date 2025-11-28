@props(['shifts' => []])

@php
    $modules = $currentRoom->modules()->enabled()->online()->with('attentionProfiles')->get();
    $attentionProfiles = $currentRoom->attentionProfiles;
    $rooms = $currentRoom->branch->rooms;
@endphp
<div x-data="{
    shifts: JSON.parse('{{ json_encode($shifts->toArray()) }}'),
    removeShiftUrl: '{{ route('attention.customer-reception.shifts.destroy', '__ID__') }}',
    changeModuleUrl: '{{ route('attention.customer-reception.shifts.change-module', '__ID__') }}',
    changeAttentionProfileUrl: '{{ route('attention.customer-reception.shifts.change-attention-profile', '__ID__') }}',
    changeRoomUrl: '{{ route('attention.customer-reception.shifts.change-room', '__ID__') }}',
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
    changeAttentionProfile(shift) {
        let availableAttentionProfiles = attentionProfiles.filter((profile) => profile.id !== shift.attention_profile_id).reduce((acc, profile) => {
            acc[profile.id] = profile.name;
            return acc;
        }, {});

        if (Object.keys(availableAttentionProfiles).length > 0) {

            sweetalert.fire({
                title: 'Cambiar de perfil de atención',
                text: '¿Desea cambiar de perfil de atención del turno?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si, cambiar',
                cancelButtonText: 'Cancelar',
                input: 'select',
                inputOptions: availableAttentionProfiles,
                inputPlaceholder: 'Seleccione el perfil de atención',
            }).then((result) => {
                if (result.value) {
                    let input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'attention_profile_id';
                    input.value = result.value;
                    $refs.changeAttentionProfileForm.appendChild(input);
                    $refs.changeAttentionProfileForm.action = this.changeAttentionProfileUrl.replace('__ID__', shift.id);
                    $refs.changeAttentionProfileForm.submit();
                }
            })
        } else {
            sweetalert.fire({
                title: 'Cambiar de perfil de atención',
                text: 'No hay perfiles de atención disponibles',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Aceptar',
                cancelButtonText: 'Cancelar',
            })
        }
    },
    changeRoom(shift) {
        let availableRooms = rooms.filter((room) => room.id !== shift.room_id).reduce((acc, room) => {
            acc[room.id] = room.name;
            return acc;
        }, {});

        if (Object.keys(availableRooms).length > 0) {

            sweetalert.fire({
                title: 'Cambiar de sala',
                text: '¿Desea cambiar de sala del turno?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si, cambiar',
                cancelButtonText: 'Cancelar',
                input: 'select',
                inputOptions: availableRooms,
                inputPlaceholder: 'Seleccione la sala',
            }).then((result) => {
                if (result.value) {
                    let input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'room_id';
                    input.value = result.value;
                    $refs.changeRoomForm.appendChild(input);
                    $refs.changeRoomForm.action = this.changeRoomUrl.replace('__ID__', shift.id);
                    $refs.changeRoomForm.submit();
                }
            })
        } else {
            sweetalert.fire({
                title: 'Cambiar de sala',
                text: 'No hay salas disponibles',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Aceptar',
                cancelButtonText: 'Cancelar',
            })
        }
    },
    removeShift(shift) {
        let url = this.removeShiftUrl.replace('__ID__', shift.id);
        return url;
    },
    filterShifts(shifts) {
        this.shifts = shifts.filter((shift) => {
            return shift.state === '{{ \App\Enums\ShiftState::Pending }}';
        })
    },
    listener() {
        let channel = Echo.channel('rooms.' + {{ $currentRoom->id }} + '.shifts');

        channel.subscribed(() => {
            console.log('subscribed to rooms.' + {{ $currentRoom->id }} + '.shifts');
        })

        channel.cancelSubscription = () => {
            console.log('unsubscribed from rooms.' + {{ $currentRoom->id }} + '.shifts');
        }

        channel.listen('.shift.updated', (e) => {
            let shift = e.shift;
            if (!this.shifts.find((s) => {
                    return s.id === shift.id
                })) {
                this.shifts.push(shift);
            } else {
                this.shifts = this.shifts.map((s) => {
                    if (s.id === shift.id) {
                        return shift;
                    }
                    return s;
                })
            }
            this.filterShifts(this.shifts)
        })
    },

}" x-init="listener()">
    <x-adminlte-card title="Turnos pendientes" theme="primary" icon="fas fa-users">
        <template x-if="shifts.length == 0">
            <p class="text-center">No hay turnos pendientes.</p>
        </template>
        <template x-if="shifts.length > 0">
            <ul class="list-group">
                <template x-for="shift in shifts">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div class="d-flex flex-column">
                            <div>
                                <i class="fas fa-desktop text-primary"></i>
                                <strong class="text-primary ml-2" x-text="shift.module.name"></strong>
                            </div>
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
                        <div>
                            <strong x-text="shift.client.name"></strong> (<span x-text="shift.client.dni"></span>)
                        </div>
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
                            <span class="text-muted font-italic mr-2" x-text="definition.text"></span>

                            <x-adminlte-button title="Acciones" icon="fas fa-ellipsis-v" theme="primary"
                                data-toggle="dropdown" />

                            <div class="dropdown-menu" role="menu"
                                style="position: absolute; transform: translate3d(68px, 38px, 0px); top: 0px; left: 0px; will-change: transform;"
                                x-placement="bottom-start">
                                <a class="dropdown-item cursor-pointer" x-on:click="changeModule(shift)">
                                    <div class="row">
                                        <div class="col-2">
                                            <i class="text-primary fas fa-desktop"></i>
                                        </div>

                                        <div class="col-10">
                                            Cambiar de modulo
                                        </div>
                                    </div>
                                </a>
                                <a class="dropdown-item cursor-pointer" x-on:click="changeAttentionProfile(shift)">
                                    <div class="row">
                                        <div class="col-2">
                                            <i class="text-primary fas fa-edit"></i>
                                        </div>
                                        <div class="col-10">
                                            Cambiar Perfil de Atención
                                        </div>
                                    </div>
                                </a>
                                <a class="dropdown-item cursor-pointer" x-on:click="changeRoom(shift)">
                                    <div class="row">
                                        <div class="col-2">
                                            <i class="text-primary fas fa-exchange-alt"></i>
                                        </div>
                                        <div class="col-10">
                                            Cambiar de Sala
                                        </div>
                                    </div>
                                </a>
                                <div class="dropdown-divider"></div>
                                <form x-ref="deleteShiftForm" method="POST">
                                    @csrf
                                    @method('DELETE')
                                </form>
                                <a class="dropdown-item cursor-pointer"
                                    x-on:click="
                                    $refs.deleteShiftForm.action = removeShift(shift); 
                                    $refs.deleteShiftForm.submit();
                                ">
                                    <div class="row">
                                        <div class="col-2">
                                            <i class="text-danger fas fa-trash"></i>
                                        </div>
                                        <div class="col-10">
                                            Eliminar Turno
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </li>
                </template>
            </ul>
        </template>
    </x-adminlte-card>
    <form x-ref="changeRoomForm" method="POST">
        @csrf
    </form>
    <form x-ref="changeAttentionProfileForm" method="POST">
        @csrf
    </form>
    <form x-ref="changeModuleForm" method="POST">
        @csrf
    </form>
</div>

@push('js')
    <script src="{{ asset('vendor/sweetalert2/sweetalert2.all.min.js') }}"></script>
    <script>
        let sweetalert = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-primary',
                cancelButton: 'btn btn-danger'
            },
            buttonsStyling: false
        })
        let modules = {!! json_encode($modules) !!};
        let attentionProfiles = {!! json_encode($attentionProfiles) !!};
        let rooms = {!! json_encode($rooms) !!};
    </script>
@endpush
