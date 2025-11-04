 <div x-data="{
     client_type_id: '{{ request('client_type_id', $client?->client_type_id) }}',
     dni: '{{ request('dni', $client?->dni) }}',
     name: '{{ request('name', $client?->name) }}',
     edit: {{ $searched && !isset($client) ? 'true' : 'false' }}
 }">
     <x-adminlte-card title="Crear turno" icon="fas fa-plus">
         <form action="{{ route('attention.customer-reception.index') }}" method="GET">
             <div class="row align-items-end">
                 <div class="col-10">
                     <x-adminlte-input name="dni" label="Cédula" placeholder="Ingrese la cédula del cliente"
                         value="{{ request('dni', $client?->dni) }}" />
                 </div>

                 <div class="col-2">
                     @if ($searched)
                         <x-adminlte-button class="btn mb-3" theme="danger" icon="fas fa-times"
                             id="clear-search-client" />
                     @else
                         <x-adminlte-button type="submit" class="btn btn-primary btn-block mb-3" theme="primary"
                             icon="fas fa-search" />
                     @endif

                 </div>
             </div>


             @if ($searched)
                 <div class="row align-items-end">
                     <div class="col-10">
                         <x-adminlte-input id="input_name" name="name" label="Nombre"
                             placeholder="Ingrese el nombre del cliente" x-model="name" x-bind:disabled="!edit" />
                     </div>
                     <div class="col-2">
                         <x-adminlte-button class="btn mb-3" x-bind:class="edit ? 'bg-secondary' : 'bg-primary'"
                             theme="secondary" icon="fas fa-edit" x-on:click="edit = !edit" />
                     </div>
                 </div>
                 <x-adminlte-select id="input_client_type_id" name="client_type_id" label="Tipo de cliente"
                     x-bind:disabled="!edit" x-model="client_type_id">
                     @foreach ($clientTypes as $clientType)
                         <option value="{{ $clientType->id }}">
                             {{ $clientType->name }}
                         </option>
                     @endforeach
                 </x-adminlte-select>
             @endif
         </form>

         <form action="{{ route('attention.customer-reception.create-shift') }}" method="POST">
             @csrf
             <input type="hidden" name="room_id" value="{{ $currentRoom?->id }}">
             <input type="hidden" name="client_type_id" x-bind:value="client_type_id">
             <input type="hidden" name="dni" x-bind:value="dni">
             <input type="hidden" name="name" x-bind:value="name">
             <input type="hidden" name="client_id" value="{{ $client?->id }}">
             <label for="attention_profile_id">Perfil de atención</label>
             @foreach ($attentionProfiles as $attentionProfile)
                 <div class="form-check">
                     <input class="form-check-input" type="radio" name="attention_profile_id"
                         id="attention_profile_{{ $attentionProfile->id }}" value="{{ $attentionProfile->id }}"
                         {{ request('attention_profile_id', $client?->attention_profile_id) == $attentionProfile->id ? 'checked' : '' }}>
                     <label class="form-check-label" for="attention_profile_{{ $attentionProfile->id }}">
                         <div class="d-flex align-items-center">
                             <span>{{ $attentionProfile->name }}</span>
                         </div>
                 </div>
             @endforeach
             <x-adminlte-button label="Crear turno" type="submit" class="btn btn-primary btn-block mt-3"
                 theme="primary" icon="fas fa-plus" />
         </form>
     </x-adminlte-card>
 </div>

 @push('js')
     <script>
         document.addEventListener('DOMContentLoaded', function() {
             const nameInput = document.querySelector('input[name="name"]');
             nameInput.addEventListener('input', function() {
                 nameInput.value = nameInput.value.toUpperCase();
             });
         });
     </script>
 @endpush
