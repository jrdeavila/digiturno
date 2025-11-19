<div x-data="{
    distractedShifts: JSON.parse('{{ json_encode($distractedShifts->toArray()) }}'),
    total: {{ $distractedShifts->count() }},
    upShift(shift) {
        let url = '{{ route('attention.customer-reception.shifts.to-up', '__ID__') }}';
        $refs.upShiftForm.action = url.replace('__ID__', shift.id);
        $refs.upShiftForm.submit();
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
            this.distractedShifts = this.distractedShifts.filter((shift) => {
                return shift.state !== '{{ \App\Enums\ShiftState::Distracted }}'
            })
        })

        channel.listen('.shift.deleted', (e) => {
            this.shifts = this.distractedShifts.filter((shift) => {
                return shift.id !== e.shift.id
            })
        })
    }
}" x-init="listener()" x-effect="console.log(distractedShifts)" class="p-2">
    <x-adminlte-card title="Distraidos" icon="fas fa-times-circle">
        <x-slot name="toolsSlot">
            Total: <strong><span class="badge badge-success" x-text="total">/span></strong>
        </x-slot>

        <template x-if="distractedShifts.length == 0">
            <p class="text-center">No hay turnos distraidos.</p>
        </template>
        <template x-if="distractedShifts.length > 0">
            <ul class="list-group">
                <template x-for="shift in distractedShifts">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span x-text="shift.client.name"></span>
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
                        <form x-ref="upShiftForm" method="POST">
                            @csrf
                        </form>
                        <x-adminlte-button icon="fas fa-arrow-up" theme="success" x-on:click="upShift(shift)" />

                    </li>
                </template>
            </ul>
        </template>
    </x-adminlte-card>

</div>
