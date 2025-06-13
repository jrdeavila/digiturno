@props(['edit' => false, 'root' => true, 'selectedServices' => []])

@push('css')
    <style>
        * {
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .tree li {
            margin-left: 15px;
            position: relative;
            padding-left: 5px;
        }

        .tree li::before {
            content: " ";
            position: absolute;
            width: 1px;
            background-color: #c3c3c3;
            top: 5px;
            bottom: -12px;
            left: -10px;
        }

        body>.tree>li:first-child::before {
            top: 12px;
        }

        .tree li:not(:first-child):last-child::before {
            display: none;
        }

        .tree li:only-child::before {
            display: list-item;
            content: " ";
            position: absolute;
            width: 1px;
            background-color: #c3c3c3;
            top: 5px;
            bottom: 7px;
            height: 7px;
            left: -10px;
        }

        .tree li::after {
            content: " ";
            position: absolute;
            left: -10px;
            width: 10px;
            height: 1px;
            background-color: #c3c3c3;
            top: 12px;
        }
    </style>
@endpush

<ul class="{{ isset($root) && $root ? 'tree' : '' }}">
    @foreach ($services as $item)
        <li>
            @if ($edit)
                <input name="services[]" value="{{ $item->id }}"
                    {{ in_array(old('services.' . $item->id, $item->id), $selectedServices) ? 'checked' : '' }}
                    type="checkbox" class="service-checkbox" data-service-id="{{ $item->id }}"
                    data-parent-id="{{ $item->service_id ?? '' }}" id="service-{{ $item->id }}">
                <label for="service-{{ $item->id }}">{{ $item->name }}</label>
            @else
                <a href="{{ route('services.show', $item) }}">{{ $item->name }}</a>
            @endif

            @if ($item->services->count() > 0)
                @include('admin.services.partials.service_tree', ['services' => $item->services])
            @endif
        </li>
    @endforeach
</ul>


@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.service-checkbox').forEach(function(checkbox) {
                checkbox.addEventListener('change', function() {
                    toggleChildren(this);
                    toggleParents(this);
                });
            });

            // Selecciona/deselecciona todos los hijos recursivamente
            function toggleChildren(parentCheckbox) {
                let parentId = parentCheckbox.dataset.serviceId;
                let checked = parentCheckbox.checked;
                document.querySelectorAll('.service-checkbox[data-parent-id="' + parentId + '"]').forEach(function(
                    child) {
                    child.checked = checked;
                    toggleChildren(child);
                });
            }

            function toggleParents(childCheckbox) {
                let parentId = childCheckbox.dataset.parentId;
                if (!parentId) return;
                let parent = document.querySelector('.service-checkbox[data-service-id="' + parentId + '"]');
                if (parent) {
                    let siblings = document.querySelectorAll('.service-checkbox[data-parent-id="' + parentId +
                        '"]');
                    let anyChecked = Array.from(siblings).some(cb => cb.checked);
                    parent.checked = anyChecked;
                    toggleParents(parent);
                }
            }
        });
    </script>
@endpush
