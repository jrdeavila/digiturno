            <x-adminlte-card title="Turnos pendientes" theme="primary" icon="fas fa-users">
                @if ($shifts->isEmpty())
                    <p class="text-center">No hay turnos pendientes.</p>
                @else
                    <ul class="list-group">
                        @foreach ($shifts as $shift)
                            <li x-data="{
                                moment: '{{ $shift->created_at }}',
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
                                }
                            }" x-init="updateTime()" x-on:destroy="onUnmount()"
                                class="list-group-item d-flex justify-content-between align-items-center">
                                <div class="d-flex flex-column">
                                    <div>
                                        <i class="fas fa-desktop text-primary"></i>
                                        <strong class="text-primary ml-2">{{ $shift->module->name }}</strong>
                                    </div>
                                    <span class="text-muted" x-text="time"></span>
                                </div>
                                <div>
                                    <strong>{{ $shift->client->name }}</strong> ({{ $shift->client->dni }})
                                </div>
                                <div class="d-flex align-items-center">
                                    @php
                                        $definition = \App\Utils\ClientDefinitionProvider::getDefinition(
                                            $shift->client->clientType->slug,
                                        );
                                    @endphp
                                    <div class="avatar {{ $definition['color'] }} avatar-xs rounded-circle mr-2 d-flex align-items-center justify-content-center"
                                        style="width: 30px; height: 30px;">
                                        <i class="{{ $definition['icon'] }}"></i>
                                    </div>
                                    <span>
                                        {{ $definition['text'] }}
                                    </span>
                                    <div class="btn-group ml-2">
                                        <button type="button" class="btn btn-default dropdown-toggle dropdown-icon"
                                            data-toggle="dropdown" aria-expanded="true">
                                            <span class="sr-only">Toggle Dropdown</span>
                                        </button>
                                        <div class="dropdown-menu" role="menu"
                                            style="position: absolute; transform: translate3d(68px, 38px, 0px); top: 0px; left: 0px; will-change: transform;"
                                            x-placement="bottom-start">
                                            <a class="dropdown-item" href="">
                                                <div class="row">
                                                    <div class="col-2">
                                                        <i class="fas fa-exchange-alt"></i>
                                                    </div>
                                                    <div class="col-10 text-truncate">
                                                        Cambiar perfil de atención
                                                    </div>
                                                </div>
                                            </a>

                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" data-toggle="modal" data-target="#modal-delete">
                                                <div class="row">
                                                    <div class="col-2">
                                                        <i class="fas fa-exchange-alt"></i>
                                                    </div>
                                                    <div class="col-10">
                                                        Cambiar de sala
                                                    </div>
                                                </div>
                                            </a>
                                            <form id="form-delete-{{ $shift->id }}"
                                                action="{{ route('attention.customer-reception.shifts.destroy', $shift) }}"
                                                method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <a class="dropdown-item"
                                                    onclick="document.getElementById('form-delete-{{ $shift->id }}').submit();">
                                                    <div class="row">
                                                        <div class="col-2">
                                                            <i class="fas fa-times"></i>
                                                        </div>
                                                        <div class="col-10">
                                                            Cancelar turno
                                                        </div>
                                                    </div>
                                            </form>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                    {{ $shifts->links('custom.pagination') }}
                @endif


            </x-adminlte-card>
