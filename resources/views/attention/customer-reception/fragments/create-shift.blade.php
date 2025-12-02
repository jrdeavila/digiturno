 <div x-data="{
     client_type_id: '{{ old('client_type_id') }}',
     dni: '{{ old('dni') }}',
     name: '{{ old('name') }}',
 }">
     <x-adminlte-card title="Crear turno" icon="fas fa-plus">
         <form action="{{ route('attention.customer-reception.create-shift') }}" method="POST">
             @csrf
             <div class="row align-items-end">
                 <div class="col-12">
                     <x-adminlte-input name="dni" label="Cédula" placeholder="Ingrese la cédula del cliente"
                         x-model="dni" />
                 </div>
             </div>

             @if (session()->has('searched') && session('searched') === true)
                 <x-adminlte-input id="input_name" name="name" label="Nombre"
                     placeholder="Ingrese el nombre del cliente" x-model="name" />
                 <x-adminlte-select id="input_client_type_id" name="client_type_id" label="Tipo de cliente"
                     x-model="client_type_id" default="Seleccione un tipo de cliente">
                     <option value="Seleccione un tipo de cliente">Seleccione un tipo de cliente</option>
                     @foreach ($clientTypes as $clientType)
                         <option value="{{ $clientType->id }}"
                             {{ old('client_type_id') === $clientType->id ? 'selected' : '' }}>
                             {{ $clientType->name }}
                         </option>
                     @endforeach
                 </x-adminlte-select>
             @endif

             <input type="hidden" name="room_id" value="{{ $currentRoom?->id }}">
             <label for="attention_profile_id">Perfil de atención</label>
             @foreach ($attentionProfiles as $attentionProfile)
                 <div class="form-check">
                     <input class="form-check-input" type="radio" name="attention_profile_id"
                         id="attention_profile_{{ $attentionProfile->id }}" value="{{ $attentionProfile->id }}"
                         {{ old('attention_profile_id') == $attentionProfile->id ? 'checked' : '' }}>
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
             nameInput && nameInput.addEventListener('input', function() {
                 nameInput.value = nameInput.value.toUpperCase();
             });
             const dniInput = document.querySelector('input[name="dni"]');
             dniInput && dniInput.addEventListener('input', function() {
                 if (/[^\d]/.test(dniInput.value)) {
                     dniInput.value = dniInput.value.replace(/[^\d]/g, '');
                     sweetalert.fire({
                         title: 'Error',
                         text: 'El campo Cedula debe contener solo caracteres numéricos',
                         icon: 'error',
                         confirmButtonText: 'Aceptar'
                     })
                 }
             });
         });
     </script>
 @endpush
