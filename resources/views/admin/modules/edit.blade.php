@extends('adminlte::page')

@section('title', 'Crear modulo')

@section('content_header')
    <h1>Crear modulo</h1>
@stop

@section('content')

    <div x-data="{
        moduleTypeSelected: {{ old('module_type_id', $module->module_type_id) }},
        clientTypeSelected: {{ old('client_type_id', $module->client_type_id) }},
        useQualificationModule: false,
    }" class="row">
        <div class="col-md-12">
            @session('error')
                <blockquote class="quote quote-danger">
                    <h5>Ups! Parece que hubo algun error.</h5>
                    <p>{{ session('error') }}</p>
                </blockquote>
            @endsession
            @if ($errors->any())
                <blockquote class="quote quote-danger">
                    <h5>Ups! Parece que hubo algun error.</h5>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </blockquote>
            @endif
        </div>
        <div x-show="moduleTypeSelected == 1 || moduleTypeSelected == 6" class="col-md-6">
            @if ($userSelected === null)
                <x-adminlte-card title="Funcionario responsable" theme="primary" icon="fas fa-user">
                    <form action="{{ route('modules.edit', $module) }}" method="GET"
                        class="row justify-content-center align-items-center">
                        @csrf
                        <div class="col-md-6">
                            <x-adminlte-input name="dni" label="Documento del funcionario"
                                value="{{ old('dni') }}" />
                        </div>
                        <div class="col-md-6">
                            <x-adminlte-button type="submit" label="Buscar" theme="primary" icon="fas fa-search"
                                class="mt-3" />
                        </div>
                    </form>
                </x-adminlte-card>
            @else
                <x-adminlte-card title="Informacion del funcionario" theme="primary" icon="fas fa-user">
                    <div class="row">
                        <div class="col-md-6">
                            <x-adminlte-input name="user-name" label="Nombre"
                                value="{{ $userSelected->employee->full_name }}" disabled />

                        </div>
                        <div class="col-md-6">
                            <x-adminlte-input name="cargo" label="Cargo" value="{{ $userSelected->employee->job->name }}"
                                disabled />
                        </div>
                        <div class="col-md-6">
                            <x-adminlte-input name="email" label="Correo" value="{{ $userSelected->employee->email }}"
                                disabled />
                        </div>

                        <div class="col-md-6">
                            <x-adminlte-input name="dni" label="Documento de identidad"
                                value="{{ $userSelected->employee->documentNumber }}" disabled />
                        </div>

                        <div class="col-12">
                            <form action="{{ route('modules.edit', $module) }}" method="GET">
                                <input type="hidden" name="dni" value="">
                                <x-adminlte-button type="submit" id="create-module" label="Cambiar responsable"
                                    theme="danger" icon="fas fa-edit" />
                            </form>
                        </div>
                    </div>
                </x-adminlte-card>
            @endif
        </div>
        <div class="col-md-6">
            <x-adminlte-card title="Crear modulo" theme="primary" icon="fas fa-edit">
                <form action="{{ route('modules.update', $module) }}" method="POST" class="row">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="user_id" value="{{ $userSelected?->id }}">
                    <div class="col-md-6">
                        <x-adminlte-input name="name" label="Nombre" value="{{ old('name', $module->name) }}" />
                    </div>

                    @if (count($clientTypes) > 0)
                        <div class="col-md-4">
                            <x-adminlte-select x-model="clientTypeSelected"
                                value="{{ old('client_type_id', $module->client_type_id) }}" id="client_type_id"
                                name="client_type_id" label="Tipo de cliente">
                                @foreach ($clientTypes as $clientType)
                                    <option value="{{ $clientType->id }}">
                                        {{ $clientType->name }}
                                    </option>
                                @endforeach
                            </x-adminlte-select>
                        </div>
                    @endif

                    @if (count($moduleTypes) > 0)
                        <div class="col-md-6">
                            <x-adminlte-select x-model="moduleTypeSelected" id="module_type_id" name="module_type_id"
                                label="Tipo de modulo">
                                @foreach ($moduleTypes as $moduleType)
                                    <option value="{{ $moduleType->id }}"
                                        {{ old('module_type_id', $module->module_type_id) === $moduleType->id ? 'selected' : '' }}>
                                        {{ $moduleType->name }}
                                    </option>
                                @endforeach
                            </x-adminlte-select>
                        </div>
                    @endif

                    <div class="col-md-3">
                        <div class="form-group" name="attention_profiles">
                            <label for="attentionProfiles">Perfiles de atencion</label>
                            <div x-show="(moduleTypeSelected == 1 || moduleTypeSelected == 6)">
                                @foreach ($attentionProfiles as $attentionProfile)
                                    <div class="custom-control custom-checkbox">
                                        <input class="custom-control-input" type="checkbox"
                                            id="{{ $attentionProfile->id }}" value="{{ $attentionProfile->id }}"
                                            name="attention_profiles[]"
                                            {{ in_array($attentionProfile->id, old('attention_profiles', $module->attentionProfiles->pluck('id')->toArray())) ? 'checked' : '' }}>
                                        <label for="{{ $attentionProfile->id }}"
                                            class="custom-control-label">{{ $attentionProfile->name }}</label>
                                    </div>
                                @endforeach
                                {{-- <template x-for="attentionProfile in attentionProfiles">
                                    <div class="custom-control custom-checkbox">
                                        <input class="custom-control-input" type="checkbox"
                                            :id="'attentionProfile' + attentionProfile.id" :value="attentionProfile.id"
                                            name="attention_profiles[]"
                                            :checked="{{ json_encode(old('attention_profiles', $module->attention_profiles)) }}
                                                .includes(attentionProfile
                                                    .id
                                                    .toString())">
                                        <label :for="'attentionProfile' + attentionProfile.id" class="custom-control-label"
                                            x-text="attentionProfile.name"></label>
                                    </div>
                                </template> --}}
                            </div>
                            <div x-show="moduleTypeSelected != 1 && moduleTypeSelected != 6">
                                <span class="text-muted font-italic"
                                    x-text="`El modulo de tipo ${ @js($moduleTypes).find(moduleType => moduleType.id == moduleTypeSelected).name } no tiene perfiles de atencion`"></span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <x-adminlte-button type="submit" label="Guardar" theme="primary" />
                    </div>
                </form>
            </x-adminlte-card>
        </div>
    </div>
@stop


@push('js')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
@endpush
