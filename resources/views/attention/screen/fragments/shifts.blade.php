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
                                <i class="fas fa-desktop text-primary text-xl"></i>
                                <strong class="text-primary text-xl ml-2" x-text="shift.module.name"></strong>
                            </div>
                        </div>
                        <div>
                            <strong class="text-xl" x-text="shift.client.name"></strong>
                        </div>
                    </li>
                </template>
            </ul>
        </template>
    </x-adminlte-card>
</div>
