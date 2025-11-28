@props(['shifts' => []])

<div x-data="{
    shifts: JSON.parse('{{ json_encode($shifts->toArray()) }}'),
    removeShiftUrl: '{{ route('attention.customer-reception.shifts.destroy', '__ID__') }}',
    removeShift(shift) {
        let url = this.removeShiftUrl.replace('__ID__', shift.id);
        return url;
    },
    filterShifts(shifts) {
        this.shifts = shifts.filter((shift) => {
            return shift.state === '{{ \App\Enums\ShiftState::Called }}';
        })
    },
    emitVoice(text) {
        if (voice) {
            let utterance = new SpeechSynthesisUtterance(text);
            utterance.voice = this.voice;
            window.speechSynthesis.speak(utterance);
        }
    },
    callShift(shift) {
        if (shift.state === '{{ \App\Enums\ShiftState::Called }}') {
            this.emitVoice(`${shift.client.name}, por favor diríjase al módulo ${shift.module.name}`);
        }
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
            this.callShift(shift);
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

            this.filterShifts(this.shifts);
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

                        </div>
                    </li>
                </template>
            </ul>
        </template>
    </x-adminlte-card>
</div>
