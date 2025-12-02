@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                @foreach (['error', 'success'] as $type)
                    @if (session()->has($type))
                        <div class="alert alert-{{ $type === 'error' ? 'danger' : $type }} alert-dismissible fade show"
                            role="alert">
                            {{ session($type) }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                @endforeach

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
            </div>

            <div class="col-md-4">
                <x-adminlte-small-box icon="far fa-user" text="Clientes registrados" title="{{ $clientCount }}"
                    theme="light" />
            </div>

            <div class="col-md-4">
                <x-adminlte-small-box text="Turnos completados (Hoy)" title="{{ $completedShiftCountToDay }}"
                    theme="success" icon="far fa-check-circle" />
            </div>

            <div class="col-md-4">
                <x-adminlte-small-box text="Turnos pendientes (Hoy)" title="{{ $pendingShiftCountToDay }}" theme="primary"
                    icon="far fa-clock" />
            </div>

            <div class="col-md-4">
                <x-adminlte-small-box text="Turnos cancelados (Hoy)" title="{{ $cancelledShiftCountToDay }}" theme="warning"
                    icon="fa fa-exclamation-triangle" />
            </div>

            <div class="col-md-4">
                <x-adminlte-small-box title="{{ $distractedShiftCountToDay }}" text="Turnos distraidos (Hoy)" theme="danger"
                    icon="far fa-times-circle" />
            </div>
            <div class="col-md-12">
                <hr>
            </div>
            <div class="col-md-12">
                <x-adminlte-card title="Turnos completados por sala (Hoy)" theme="primary" icon="far fa-clock">
                    <canvas id="myChart" width="400" height="200"></canvas>
                </x-adminlte-card>
            </div>
            <div class="col-md-12">
                <x-adminlte-card title="Distribucción de turnos en la sede Principal" theme="primary" icon="far fa-clock">
                    <canvas id="myChart2" width="400" height="200"></canvas>
                </x-adminlte-card>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="{{ asset('vendor/chart.js/Chart.min.js') }}"></script>
    <script>
        const ctx2 = document.getElementById('myChart2');

        let data2 = {!! json_encode($principalShiftByModule) !!};

        const sedes = Object.keys(data2);

        const moduloSet = new Set();
        sedes.forEach(sede => {
            Object.keys(data2[sede] || {}).forEach(mod => moduloSet.add(mod));
        });
        // Ordenar módulos numéricamente
        const modulos = Array.from(moduloSet).sort((a, b) => Number(a) - Number(b));

        // 3) Crear un color distinto por módulo (puedes personalizar esta paleta)
        const palette = [
            '#4F46E5', '#16A34A', '#DC2626', '#F59E0B', '#0EA5E9',
            '#8B5CF6', '#EF4444', '#10B981', '#F97316', '#06B6D4',
            '#84CC16', '#A855F7', '#EAB308'
        ];
        const colorForModulo = (idx) => palette[idx % palette.length];

        // 4) Armar datasets: un dataset por módulo con valores por sede
        const datasets = modulos.map((mod, idx) => {
            const dataValues = sedes.map(sede => {
                const valor = data2[sede] && data2[sede][mod] != null ? data2[sede][mod] : 0;
                return Number(valor);
            });
            return {
                label: `Módulo ${mod}`,
                data: dataValues,
                backgroundColor: colorForModulo(idx),
                borderColor: colorForModulo(idx),
                borderWidth: 1
            };
        });

        // 5) Construir el chart de barras agrupadas
        const myChart2 = new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: sedes, // eje X: sedes
                datasets: datasets // barras agrupadas por módulo
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    },
                    title: {
                        display: true,
                        text: 'Turnos por Módulo y Sede'
                    }
                },
                scales: {
                    x: {
                        stacked: false
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Cantidad'
                        }
                    }
                }
            }
        });
    </script>

    <script defer>
        const ctx = document.getElementById('myChart');
        let data = {!! json_encode($groupedShiftByBranch) !!};
        // Ejemplo:
        // {
        //   "Astrea": 0, "Becerril": 0, ..., "Principal": 2, "San Diego": 0
        // }

        // Opcional: ordenar sedes por valor descendente (pon a false si no lo quieres)
        const ordenarPorValor = false;

        // Extraer etiquetas y valores
        let labels = Object.keys(data);
        let values = Object.values(data).map(Number);

        if (ordenarPorValor) {
            const pares = labels.map((l, i) => ({
                label: l,
                value: values[i]
            }));
            pares.sort((a, b) => b.value - a.value);
            labels = pares.map(p => p.label);
            values = pares.map(p => p.value);
        }

        // Generar paleta de colores dinámica
        const basePalette = [
            '#4F46E5', '#16A34A', '#DC2626', '#F59E0B', '#0EA5E9',
            '#8B5CF6', '#EF4444', '#10B981', '#F97316', '#06B6D4',
            '#84CC16', '#A855F7', '#EAB308', '#374151', '#FB7185'
        ];
        const bgColors = labels.map((_, i) => hexToRgba(basePalette[i % basePalette.length], 0.2));
        const bdColors = labels.map((_, i) => hexToRgba(basePalette[i % basePalette.length], 1));

        // Utilidad: convertir HEX a RGBA con opacidad
        function hexToRgba(hex, alpha = 1) {
            const h = hex.replace('#', '');
            const bigint = parseInt(h, 16);
            const r = (bigint >> 16) & 255;
            const g = (bigint >> 8) & 255;
            const b = bigint & 255;
            return `rgba(${r}, ${g}, ${b}, ${alpha})`;
        }

        const myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: '# de turnos completados',
                    data: values,
                    backgroundColor: bgColors,
                    borderColor: bdColors,
                    borderWidth: 1
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    title: {
                        display: true,
                        text: 'Turnos completados por sede'
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: (ctx) => ` ${ctx.dataset.label}: ${ctx.parsed.y}`
                        }
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Sede'
                        },
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Cantidad'
                        },
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    </script>
@endpush
