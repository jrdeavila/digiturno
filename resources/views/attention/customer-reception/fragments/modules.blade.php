@props([
    'modules' => collect([]),
])
<div>
    <x-adminlte-card title="Modulos de la sala" icon="fas fa-desktop">
        <x-slot name="toolsSlot">
            <x-adminlte-button label="Acciones" theme="primary" id="dropdownMenuButton" class="dropdown-toggle"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" />
            <div class="dropdown-menu" role="menu"
                style="position: absolute; transform: translate3d(68px, 38px, 0px); top: 0px; left: 0px; will-change: transform;"
                x-placement="bottom-start">

                <div class="dropdown-divider"></div>

                <form id="form-delete-modules-offline"
                    action="{{ route('attention.customer-reception.modules.offline', $currentRoom) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <a class="dropdown-item" onclick="document.getElementById('form-delete-modules-offline').submit();">
                        <div class="row">
                            <div class="col-2">
                                <i class="fas fa-upload"></i>
                            </div>
                            <div class="col-10">
                                Desactivar todos los modulos
                            </div>
                        </div>
                    </a>
                </form>
            </div>
        </x-slot>
        @php
            // Los modulos tienen muchos perfiles de atencion "attentionProfiles" y necesito agruparlos por perfil de atencion sin importar si el modulo se repite
            $modulePerProfile = $modules->groupBy('attentionProfiles.*.name');
        @endphp
        @foreach ($modulePerProfile as $name => $modules)
            <div x-data="{
                modules: {{ json_encode($modules->toArray()) }},
                listener() {
                    this.modules.forEach((module) => {
                        let channel = Echo.channel('modules.' + module.id);
                        channel.listen('.module.updated', (e) => {
                            let module = e.module;
                            this.modules = this.modules.map((m) => {
                                if (m.id === module.id) {
                                    return module;
                                }
                                return m;
                            })
                        })
                        channel.listen('.shift.updated', (e) => {
            
                        })
                    })
                }
            }" x-init="listener()" class="d-flex flex-wrap">
                <p class="col-12 font-weight-bold">{{ $name }}</p>
                <template x-for="module in modules">
                    <div class="col-lg-3 col-md-6 mb-2">
                        <div class="badge badge-light p-2">

                            <i class="fas fa-desktop"
                                x-bind:class="`text-${({ offline: 'danger', online: 'success',})[module.status]}`"></i>
                            <strong class="ml-2"
                                x-bind:class="`text-${({ offline: 'danger', online: 'success',})[module.status]}`"
                                x-text="`${module.name} (${module.current_shifts_count})`">
                            </strong>
                        </div>
                    </div>
                </template>
            </div>
        @endforeach

    </x-adminlte-card>
</div>
