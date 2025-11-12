@props(['shifts' => []])

<div x-data="{
    shifts: JSON.parse('{{ json_encode($shifts->toArray()) }}'),
    removeShiftUrl: '{{ route("attention.customer-reception.shifts.destroy", "__ID__") }}',
    removeShift(shift) {
        let url = this.removeShiftUrl.replace('__ID__', shift.id);
        return url;
    },
}">
    <p></p>
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
                                }" x-init="updateTime()" x-on:destroy="onUnmount()" class="text-muted" x-text="time"></span>
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

                            <x-adminlte-button title="Acciones" icon="fas fa-ellipsis-v" theme="primary" data-toggle="dropdown" />

                            <div class="dropdown-menu" role="menu"
                                style="position: absolute; transform: translate3d(68px, 38px, 0px); top: 0px; left: 0px; will-change: transform;"
                                x-placement="bottom-start">
                                <a class="dropdown-item cursor-pointer">
                                    <div class="row">
                                        <div class="col-2">
                                            <i class="text-primary fas fa-desktop"></i>
                                        </div>
                                        <div class="col-10">
                                            Cambiar de modulo
                                        </div>
                                    </div>
                                </a>
                                <a class="dropdown-item cursor-pointer">
                                    <div class="row">
                                        <div class="col-2">
                                            <i class="text-primary fas fa-edit"></i>
                                        </div>
                                        <div class="col-10">
                                            Cambiar Perfil de Atención
                                        </div>
                                    </div>
                                </a>
                                <a class="dropdown-item cursor-pointer">
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
                                <a class="dropdown-item cursor-pointer" x-on:click="
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
</div>