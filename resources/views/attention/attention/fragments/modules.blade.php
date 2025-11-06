  @props([
      'modules' => collect([]),
  ])
  <x-adminlte-card title="Modulos asignados" icon="fas fa-desktop">
      @php
          $modulePerProfile = $modules->groupBy('attentionProfile.name');
      @endphp
      @forelse ($modulePerProfile as $name => $modules)
          <div class="d-flex flex-wrap">
              <p class="col-12 font-weight-bold">{{ $name }}</p>
              @foreach ($modules as $module)
                  <div class="col-lg-3 col-md-6 mb-2">
                      <x-adminlte-info-box title="Modulo {{ $module->name }}"
                          text="{{ join(', ', $module->attentionProfiles->pluck('name')->toArray()) }}" theme="primary"
                          icon="fas fa-desktop"
                          onclick="window.location.href='{{ route(Route::currentRouteName()) }}?module={{ $module->id }}'" />
                  </div>
              @endforeach
          </div>
      @empty
          <div class="d-flex flex-wrap">
              <p class="col-12 font-weight-bold">No hay modulos asignados</p>
          </div>
      @endforelse

  </x-adminlte-card>
