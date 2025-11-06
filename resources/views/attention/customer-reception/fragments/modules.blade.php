     @props([
         'modules' => collect([]),
     ])
     <x-adminlte-card title="Modulos de la sala" icon="fas fa-desktop" maximizable collapsable>
         @php
             $modulePerProfile = $modules->groupBy('attentionProfile.name');
         @endphp
         @foreach ($modulePerProfile as $name => $modules)
             <div class="d-flex flex-wrap">
                 <p class="col-12 font-weight-bold">{{ $name }}</p>
                 @foreach ($modules as $module)
                     <div class="col-lg-6 col-md-6 mb-2">
                         @php
                             $theme = 'primary';
                             switch ($module->status) {
                                 case 'offline':
                                     $theme = 'danger';
                                     break;

                                 case 'online':
                                     $theme = 'success';
                                     break;
                                 default:
                                     $theme = 'warning';
                                     break;
                             }
                         @endphp
                         <x-adminlte-info-box title="Modulo {{ $module->name }} ({{ $module->pendingShifts->count() }})"
                             text="{{ $module->description }}" theme="{{ $theme }}" icon="fas fa-desktop" />
                     </div>
                 @endforeach
             </div>
         @endforeach

     </x-adminlte-card>
