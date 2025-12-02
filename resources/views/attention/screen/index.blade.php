@extends('layouts.app')

@section('title', $currentRoom->name)

@section('content_header')
    <h1>Atención</h1>
@stop

@php
    $distractedShifts = $currentRoom->distractedShifts()->get();
    $shifts = $currentRoom->calledShifts()->get();
@endphp

@section('content')
    <div x-data="{
        voices: [],
        voice: null,
        loadVoices() {
            window.speechSynthesis.getVoices();
            let localStorage = window.localStorage.getItem('voice.name');
            setTimeout(() => {
                let voices = window.speechSynthesis.getVoices();
                this.voices = voices;
                if (voices.length === 0) {
                    sweetalert.fire({
                        title: 'No hay voces disponibles',
                        text: 'No se pudo emitir el sonido',
                        icon: 'warning',
                        showConfirmButton: false,
                        allowOutsideClick: false
                    })
                    return true;
                } else {
                    if (localStorage) {
                        let voice = voices.find(voice => voice.name === localStorage);
                        if (voice) {
                            this.voice = voice;
                            this.testVoice(voice);
                            window.localStorage.setItem('voice.name', voice.name);
                            sweetalert.fire({
                                title: 'Voz seleccionada',
                                text: 'Ahora se usara la voz: ' + voice.name,
                                icon: 'info',
                                showConfirmButton: false,
                            })
                        }
                    } else {
                        sweetalert.fire({
                            title: 'Voces disponibles',
                            text: 'Seleccione la voz que desea usar',
                            icon: 'info',
                            showConfirmButton: false,
                        })
                    }
                    return false;
                }
            }, 2000);
        },
        testVoice(voice) {
            let utterance = new SpeechSynthesisUtterance('Prueba de voz con la voz seleccionada por el usuario');
            utterance.voice = voice;
            window.speechSynthesis.speak(utterance);
        },
        selectVoice(voice) {
            if (voice) {
                window.localStorage.setItem('voice.name', voice.name);
                location.reload();
            } else {
                sweetalert.fire({
                    title: 'Voz no seleccionada',
                    text: 'No se ha seleccionado ninguna voz',
                    icon: 'warning',
                    showConfirmButton: false,
                    allowOutsideClick: false
                })
            }
        },
    }" x-init="loadVoices()">
        <div x-show="voice" class="row h-100">
            <div class="col-12">
                @foreach (['error', 'success'] as $type)
                    @if (session()->has($type))
                        <x-adminlte-alert theme="{{ $type === 'error' ? 'danger' : $type }}" dismissable>
                            {{ session($type) }}
                        </x-adminlte-alert>
                    @endif
                @endforeach
                @if ($errors->any())
                    <x-adminlte-alert theme="danger" dismissable>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </x-adminlte-alert>
                @endif
            </div>
            <div class="col-md-12 mb-2">
                <x-adminlte-button label="Cambiar voz" theme="primary" icon="fas fa-check" x-on:click="voice = null" />
            </div>
            <div class="col-lg-5 col-md-6">
                @include('attention.screen.fragments.distracted', [
                    'distractedShifts' => $distractedShifts->take(5),
                    'currentRoom' => $currentRoom,
                ])
            </div>

            <div class="col-lg-7 col-md-6">
                @include('attention.screen.fragments.shifts', ['shifts' => $shifts])
            </div>
        </div>
        <div x-show="!voice" class="row h-100">
            <div class="col-12">
                <x-adminlte-card title="Voces disponibles" icon="fas fa-users">
                    <x-slot name="toolsSlot">
                        <x-adminlte-button label="Cargar voces" theme="primary" icon="fas fa-check"
                            x-on:click="loadVoices" />
                    </x-slot>
                    <template x-if="voices.length > 0">
                        <div class="row">

                            <template x-for="voice in voices">
                                <div class="col-md-4 mb-2">
                                    <div class="btn-group">
                                        <x-adminlte-button class="w-100 dropdown-toggle dropdown-icon" theme="primary"
                                            icon="fas fa-check" data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                            <x-slot name="label">
                                                <span x-text="voice.name"></span>
                                            </x-slot>
                                        </x-adminlte-button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" x-on:click="testVoice(voice)">Probar voz</a>
                                            <a class="dropdown-item" x-on:click="selectVoice(voice)">Selecionar voz</a>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                </x-adminlte-card>
            </div>
        </div>
    @stop

    @push('js')
        <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
        <script src="{{ asset('vendor/sweetalert2/sweetalert2.all.min.js') }}"></script>
        <script>
            let sweetalert = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            });
        </script>
    @endpush
