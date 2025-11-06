@props([
    'full' => false,
])
@php
    $width = $full ? '98%' : '75%';
@endphp
<div class="p-2" style="max-height: inherit; width: {{ $width }}; position: relative;">
    <x-adminlte-card title="Turnos pendientes" theme="primary" icon="fas fa-users"
        style="position: fixed; width: calc({{ $width }} - 250px);">
        @php
            $shifts = $currentModule->pendingShifts->take(5);
            $total = $currentModule->pendingShifts->count();
        @endphp
        <x-slot name="toolsSlot">
            Total: <strong><span class="badge badge-success">{{ $total }}</span></strong>
        </x-slot>
        <div>
            @foreach ($shifts as $shift)
                <li class="list-group-item d-flex justify-content-between align-items-center w-100">
                    <div x-data="{
                        clock: '{{ $shift->created_at }}',
                        time: '',
                        interval: null,
                        updateTime() {
                            let interval = setInterval(() => {
                                // Calcular el timpo transcurrido en minutos
                                let now = new Date();
                                let then = new Date(this.clock);
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
                        class="row align-items-center">
                        <div class="mr-2">
                            <i class="fas fa-clock text-primary"></i>
                            <strong class="text-primary ml-2" x-text="time"></strong>
                        </div>
                        @php
                            $definition = \App\Utils\ClientDefinitionProvider::getDefinition(
                                $shift->client->clientType->slug,
                            );
                        @endphp
                        <div class="d-flex align-items-center">
                            <div class="avatar {{ $definition['color'] }} avatar-xs rounded-circle mr-2 d-flex align-items-center justify-content-center"
                                style="width: 30px; height: 30px;">
                                <i class="{{ $definition['icon'] }}"></i>
                            </div>
                            <span>
                                {{ $definition['text'] }}
                            </span>
                        </div>
                    </div>

                    <div>
                        <strong>{{ $shift->client->name }}</strong> ({{ $shift->client->dni }})
                    </div>
                    <div class="d-flex align-items-center">


                        <div class="btn-group ml-2">
                            <form action="{{ route('attention.attention.shifts.up', $shift) }}" method="POST">
                                @csrf
                                <x-adminlte-button class="ml-1" type="submit" theme="success"
                                    icon="fas fa-arrow-up" />
                            </form>
                            <form action="{{ route('attention.attention.shifts.distracted', $shift) }}" method="POST">
                                @csrf
                                <x-adminlte-button class="ml-1" type="submit" theme="danger"
                                    icon="fas fa-arrow-down" />
                            </form>
                            <form action="{{ route('attention.attention.shifts.call', $shift) }}" method="POST">
                                @csrf
                                <x-adminlte-button class="ml-1" type="submit" theme="primary"
                                    icon="fas fa-volume-up" />
                            </form>
                        </div>
                    </div>
                </li>
            @endforeach
        </div>
    </x-adminlte-card>

</div>
