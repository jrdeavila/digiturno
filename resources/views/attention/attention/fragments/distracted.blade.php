<div class="p-2" style="max-height: inherit; width: 25%;">
    <x-adminlte-card title="Distraidos" icon="fas fa-times-circle">
        @if ($distractedShifts->isEmpty())
            <p class="text-center">No hay turnos distraidos.</p>
        @else
            <ul class="list-group">
                @foreach ($distractedShifts as $shift)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            @php
                                $definition = \App\Utils\ClientDefinitionProvider::getDefinition(
                                    $shift->client->clientType->slug,
                                );
                            @endphp
                            <div class="avatar {{ $definition['color'] }} avatar-xs rounded-circle  mr-1 d-flex align-items-center justify-content-center"
                                style="width: 30px; height: 30px;">
                                <i class="{{ $definition['icon'] }}"></i>
                            </div>
                            <div>
                                <strong>{{ $shift->client->name }}</strong> ({{ $shift->client->dni }})
                            </div>
                        </div>
                        <div class="d-flex align-items-center">


                            <form id="form-to-up-{{ $shift->id }}"
                                action="{{ route('attention.attention.shifts.distracted', $shift) }}" method="POST">
                                @csrf
                                @method('POST')
                                <x-adminlte-button class="ml-1" type="submit" theme="success"
                                    icon="fas fa-arrow-up" />
                            </form>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif

    </x-adminlte-card>

</div>
