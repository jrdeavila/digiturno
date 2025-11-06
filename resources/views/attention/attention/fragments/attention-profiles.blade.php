<div class="p-2" style="max-height: inherit; width: 25%;">
    <x-adminlte-input id="input_name" name="search" label="Buscar"
        placeholder="Ingrese el nombre del perfil de atención" />
    <ul id="attention-profiles" class="list-group">
        @foreach ($attentionProfiles as $attentionProfile)
            <li>
                <strong>{{ $attentionProfile->name }}</strong>
            </li>
            <ul>
                @foreach ($attentionProfile->services as $service)
                    <li>
                        <input type="checkbox" name="services[{{ $service->id }}]" />
                        <label id="{{ $service->name }}" for="service-{{ $service->id }}">{{ $service->name }}</label>
                    </li>
                @endforeach
            </ul>
        @endforeach
    </ul>
</div>
