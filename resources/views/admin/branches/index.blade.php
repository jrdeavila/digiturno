@extends('adminlte::page')

@section('title', __('adminlte::menu.branches.index'))

@section('content_header')
    <h1>{{ __('adminlte::menu.branches.index') }} {{ __('adminlte::menu.branches.label') }}</h1>
@stop

@section('content')

    <x-adminlte-card theme="light" icon="fas fa-list" title="{{ __('adminlte::menu.branches.label') }}">
        @session('success')
            <blockquote class="quote quote-success">
                <h5>{{ __('messages.success_title') ?? 'Genial!' }}</h5>
                <p>{{ session('success') }}</p>
            </blockquote>
        @endsession
        @session('error')
            <blockquote class="quote quote-danger">
                <h5>{{ __('messages.error_title') ?? 'Error!' }}</h5>
                <p>{{ session('error') }}</p>
            </blockquote>
        @endsession

        @php
            $heads = ['branches.id', 'branches.name', 'branches.address', 'branches.actions.label'];
            $heads = array_map(fn($head) => trans($head), $heads);
            $config = [
                'data' => $branches,
                'order' => [[1, 'asc']],
                'columns' => [
                    null, // ID
                    null, // Nombre
                    null, // Dirección
                    ['orderable' => false], // Acciones
                ],
            ];
        @endphp

        <x-adminlte-datatable id="branches-table" :heads="$heads" :config="$config">
            @foreach ($branches as $branch)
                <tr>
                    <td>{{ $branch->id }}</td>
                    <td>
                        <a href="{{ route('branches.show', $branch->id) }}" class="text-primary font-weight-bold">
                            {{ $branch->name }}
                        </a>
                    </td>
                    <td>{{ $branch->address }}</td>
                    <td>
                        <div class="btn-group" role="group" aria-label="{{ __('branches.actions.label') }}">
                            <a href="{{ route('branches.show', $branch->id) }}" class="btn btn-primary">
                                <i class="fas fa-eye mr-1"></i> {{ __('branches.actions.view') }}
                            </a>
                            <a href="{{ route('branches.edit', $branch->id) }}" class="btn btn-warning">
                                <i class="fas fa-edit mr-1"></i> {{ __('branches.actions.edit') }}
                            </a>
                            <button type="button" class="btn btn-danger btn-delete-branch" data-id="{{ $branch->id }}"
                                data-name="{{ $branch->name }}">
                                <i class="fas fa-trash mr-1"></i> {{ __('branches.actions.delete') }}
                            </button>
                        </div>
                    </td>
                </tr>
            @endforeach

            <tr>
                <td colspan="3"></td>
                <td class="text-right">
                    <a href="{{ route('branches.create') }}" class="btn btn-success" id="create-branch">
                        <i class="fas fa-plus mr-1"></i> {{ __('branches.actions.create') ?? 'Crear' }}
                    </a>
                </td>
            </tr>
            <tr>
                <td colspan="4">
                    <div class="float-right">
                        {{ $branches->links('custom.pagination') }}
                    </div>
                </td>
            </tr>
        </x-adminlte-datatable>

        {{-- Formulario oculto para eliminación (reutilizable) --}}
        <form id="delete-branch-form" method="POST" style="display:none;">
            @csrf
            @method('DELETE')
        </form>
    </x-adminlte-card>

@stop

@push('js')
    {{-- SweetAlert2: usa tu vendor si lo tienes instalado, o CDN en su defecto --}}
    <script src="{{ asset('vendor/sweetalert2/sweetalert2.all.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Eliminar seccional con SweetAlert2
            document.querySelectorAll('.btn-delete-branch').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const name = this.getAttribute('data-name');

                    Swal.fire({
                        title: "{{ __('branches.actions.delete_confirm_title') ?? 'Eliminar seccional' }}",
                        html: `{{ __('branches.actions.delete_confirm_text') ?? 'La seccional' }} <b>${name}</b> {{ __('branches.actions.delete_confirm_suffix') ?? 'será eliminada permanentemente. Esta acción no se puede deshacer.' }}`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: "{{ __('branches.actions.delete_confirm_yes') ?? 'Sí, eliminar' }}",
                        cancelButtonText: "{{ __('branches.actions.delete_confirm_cancel') ?? 'Cancelar' }}",
                        confirmButtonColor: '#e3342f',
                        cancelButtonColor: '#6c757d',
                        reverseButtons: true,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const form = document.getElementById('delete-branch-form');
                            form.action = "{{ route('branches.destroy', '__ID__') }}"
                                .replace('__ID__', id);
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
@endpush
