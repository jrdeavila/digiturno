  <x-adminlte-card title="Distraidos" icon="fas fa-times-circle">
      @if ($distractedShifts->isEmpty())
          <p class="text-center">No hay turnos distraidos.</p>
      @else
          <ul class="list-group">
              @foreach ($distractedShifts as $shift)
                  <li class="list-group-item d-flex justify-content-between align-items-center">
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
                                  <div class="dropdown-divider"></div>
                                  <form id="form-to-up-{{ $shift->id }}"
                                      action="{{ route('attention.customer-reception.shifts.to-up', $shift) }}"
                                      method="POST">
                                      @csrf
                                      @method('POST')
                                      <a class="dropdown-item"
                                          onclick="document.getElementById('form-to-up-{{ $shift->id }}').submit();">
                                          <div class="row">
                                              <div class="col-2">
                                                  <i class="fas fa-upload"></i>
                                              </div>
                                              <div class="col-10">
                                                  Subir
                                              </div>
                                          </div>
                                      </a>
                                  </form>
                              </div>
                          </div>
                      </div>
                  </li>
              @endforeach
          </ul>
      @endif

  </x-adminlte-card>
