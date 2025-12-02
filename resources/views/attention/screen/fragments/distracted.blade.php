<div x-data="{
    distractedShifts: JSON.parse('{{ json_encode($distractedShifts->toArray()) }}'),
    total: {{ $distractedShifts->count() }},
    filterShifts(shifts) {
        this.distractedShifts = shifts.filter((shift) => {
            return shift.state === '{{ \App\Enums\ShiftState::Distracted }}'
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
            if (this.distractedShifts.find((s) => {
                    return s.id === e.shift.id
                })) {
                this.distractedShifts = this.distractedShifts.map((s) => {
                    if (s.id === e.shift.id) {
                        return e.shift;
                    }
                    return s;
                })
            } else {
                this.distractedShifts.push(e.shift);
            }
            this.filterShifts(this.distractedShifts)
        })

        channel.listen('.shift.deleted', (e) => {
            this.shifts = this.distractedShifts.filter((shift) => {
                return shift.id !== e.shift.id
            })
        })
    },

}" x-init="listener()">
    <x-adminlte-card title="Distraidos" icon="fas fa-times-circle">

        <template x-if="distractedShifts.length == 0">
            <p class="text-center">No hay turnos distraidos.</p>
        </template>
        <template x-if="distractedShifts.length > 0">
            <ul class="list-group">
                <template x-for="shift in distractedShifts">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <strong class="text-xl" x-text="shift.client.name"></strong>
                    </li>
                </template>
            </ul>
        </template>
    </x-adminlte-card>

</div>
