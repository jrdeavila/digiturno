@extends('adminlte::page')

@section('title', __('Dashboard'))

@section('content_header')
    <div class="d-flex align-items-center justify-content-between">
        <h1 class="mb-0">{{ __('Dashboard') }}</h1>
        <span class="badge badge-primary px-3 py-2" title="{{ now()->format('Y-m-d H:i') }}">
            {{ __('Hoy') }} · {{ now()->translatedFormat('d M Y') }}
        </span>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                @foreach (['error', 'success'] as $type)
                    @if (session()->has($type))
                        <div class="alert alert-{{ $type === 'error' ? 'danger' : $type }} alert-dismissible fade show"
                            role="alert">
                            {{ session($type) }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="{{ __('Close') }}">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                @endforeach

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert" aria-label="{{ __('Close') }}">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
            </div>

            {{-- KPIs fila 1 --}}
            <div class="col-xl-3 col-md-6 mb-4">
                <x-adminlte-card theme="light" title="{{ __('Clientes registrados') }}" icon="far fa-user"
                    header-class="bg-gradient-light" class="elevation-2">
                    <div class="d-flex align-items-center">
                        <div class="mr-3 p-3 rounded-circle bg-light border">
                            <i class="far fa-user text-primary fa-lg"></i>
                        </div>
                        <div>
                            <div class="h2 mb-0">{{ $clientCount }}</div>
                            <small class="text-muted">{{ __('Total general') }}</small>
                        </div>
                    </div>
                </x-adminlte-card>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <x-adminlte-card theme="success" title="{{ __('Turnos completados (Hoy)') }}" icon="far fa-check-circle"
                    header-class="bg-gradient-success" class="elevation-2">
                    <div class="d-flex align-items-center">
                        <div class="mr-3 p-3 rounded-circle bg-white-50">
                            <i class="far fa-check-circle text-white fa-lg"></i>
                        </div>
                        <div>
                            <div class="h2 mb-0">{{ $completedShiftCountToDay }}</div>
                            <span class="badge badge-light text-success">{{ __('Eficiencia') }}</span>
                        </div>
                    </div>
                </x-adminlte-card>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <x-adminlte-card theme="primary" title="{{ __('Turnos pendientes (Hoy)') }}" icon="far fa-clock"
                    header-class="bg-gradient-primary" class="elevation-2">
                    <div class="d-flex align-items-center">
                        <div class="mr-3 p-3 rounded-circle bg-white-50">
                            <i class="far fa-clock text-white fa-lg"></i>
                        </div>
                        <div>
                            <div class="h2 mb-0">{{ $pendingShiftCountToDay }}</div>
                            <span class="badge badge-light text-primary">{{ __('En curso') }}</span>
                        </div>
                    </div>
                </x-adminlte-card>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <x-adminlte-card theme="warning" title="{{ __('Turnos cancelados (Hoy)') }}"
                    icon="fa fa-exclamation-triangle" header-class="bg-gradient-warning" class="elevation-2">
                    <div class="d-flex align-items-center">
                        <div class="mr-3 p-3 rounded-circle bg-white-50">
                            <i class="fa fa-exclamation-triangle text-white fa-lg"></i>
                        </div>
                        <div>
                            <div class="h2 mb-0">{{ $cancelledShiftCountToDay }}</div>
                            <span class="badge badge-light text-warning">{{ __('Atención') }}</span>
                        </div>
                    </div>
                </x-adminlte-card>
            </div>

            {{-- KPIs fila 2 --}}
            <div class="col-xl-3 col-md-6 mb-4">
                <x-adminlte-card theme="danger" title="{{ __('Turnos distraídos (Hoy)') }}" icon="far fa-times-circle"
                    header-class="bg-gradient-danger" class="elevation-2">
                    <div class="d-flex align-items-center">
                        <div class="mr-3 p-3 rounded-circle bg-white-50">
                            <i class="far fa-times-circle text-white fa-lg"></i>
                        </div>
                        <div>
                            <div class="h2 mb-0">{{ $distractedShiftCountToDay }}</div>
                            <span class="badge badge-light text-danger">{{ __('Incidencias') }}</span>
                        </div>
                    </div>
                </x-adminlte-card>
            </div>

            <div class="col-xl-9 col-md-12 mb-4">
                <x-adminlte-card title="{{ __('Resumen de hoy') }}" theme="info" icon="far fa-chart-bar"
                    header-class="bg-gradient-info" class="elevation-2">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="chart-container" style="position: relative; min-height: 260px;">
                                <canvas id="chartSummary" aria-label="{{ __('Resumen de estados de turnos (Hoy)') }}"
                                    role="img"></canvas>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-group">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="far fa-check-circle text-success mr-2"></i>
                                        {{ __('Completados') }}</span>
                                    <span class="badge badge-success badge-pill">{{ $completedShiftCountToDay }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="far fa-clock text-primary mr-2"></i> {{ __('Pendientes') }}</span>
                                    <span class="badge badge-primary badge-pill">{{ $pendingShiftCountToDay }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="fa fa-exclamation-triangle text-warning mr-2"></i>
                                        {{ __('Cancelados') }}</span>
                                    <span class="badge badge-warning badge-pill">{{ $cancelledShiftCountToDay }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="far fa-times-circle text-danger mr-2"></i>
                                        {{ __('Distraídos') }}</span>
                                    <span class="badge badge-danger badge-pill">{{ $distractedShiftCountToDay }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </x-adminlte-card>
            </div>

            <div class="col-md-12">
                <x-adminlte-card title="{{ __('Turnos completados por sede (Hoy)') }}" theme="primary"
                    icon="far fa-building" header-class="bg-gradient-primary" class="elevation-2">
                    @php $isEmptyBranch = empty($groupedShiftByBranch) || collect($groupedShiftByBranch)->sum() === 0; @endphp
                    @if ($isEmptyBranch)
                        <div class="text-center text-muted py-4">
                            <i class="far fa-frown fa-2x mb-2"></i>
                            <div>{{ __('Sin datos para mostrar hoy') }}</div>
                        </div>
                    @else
                        <div class="chart-container" style="position: relative; min-height: 340px;">
                            <canvas id="chartByBranch" aria-label="{{ __('Turnos completados por sede') }}"
                                role="img"></canvas>
                        </div>
                    @endif
                </x-adminlte-card>
            </div>

            <div class="col-md-12">
                <x-adminlte-card title="{{ __('Distribución de turnos en la sede Principal') }}" theme="secondary"
                    icon="far fa-layer-group" header-class="bg-gradient-secondary" class="elevation-2">
                    @php $isEmptyModules = empty($principalShiftByModule); @endphp
                    @if ($isEmptyModules)
                        <div class="text-center text-muted py-4">
                            <i class="far fa-frown fa-2x mb-2"></i>
                            <div>{{ __('No hay datos por módulo para hoy') }}</div>
                        </div>
                    @else
                        <div class="chart-container" style="position: relative; min-height: 360px;">
                            <canvas id="chartByModule" aria-label="{{ __('Turnos por Módulo y Sede') }}"
                                role="img"></canvas>
                        </div>
                    @endif
                </x-adminlte-card>
            </div>
        </div>
    </div>
@endsection

@push('js')
    {{-- Chart.js con SRI --}}
    <script src="{{ asset('vendor/chart.js/Chart.js') }}"></script>

    <script defer>
        const groupedShiftByBranch = {!! json_encode($groupedShiftByBranch, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!};
        const principalShiftByModule = {!! json_encode($principalShiftByModule, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!};
        const completedShiftCountToDay = {{ (int) $completedShiftCountToDay }};
        const pendingShiftCountToDay = {{ (int) $pendingShiftCountToDay }};
        const cancelledShiftCountToDay = {{ (int) $cancelledShiftCountToDay }};
        const distractedShiftCountToDay = {{ (int) $distractedShiftCountToDay }};

        function hexToRgba(hex, alpha = 1) {
            const h = hex.replace('#', '');
            const bigint = parseInt(h, 16);
            const r = (bigint >> 16) & 255;
            const g = (bigint >> 8) & 255;
            const b = bigint & 255;
            return `rgba(${r}, ${g}, ${b}, ${alpha})`;
        }

        const basePalette = [
            '#4F46E5', '#16A34A', '#DC2626', '#F59E0B', '#0EA5E9',
            '#8B5CF6', '#EF4444', '#10B981', '#F97316', '#06B6D4',
            '#84CC16', '#A855F7', '#EAB308', '#374151', '#FB7185'
        ];

        // Chart 0: Resumen Doughnut
        const ctxSummary = document.getElementById('chartSummary');
        if (ctxSummary) {
            new Chart(ctxSummary, {
                type: 'doughnut',
                data: {
                    labels: [
                        '{{ __('Completados') }}',
                        '{{ __('Pendientes') }}',
                        '{{ __('Cancelados') }}',
                        '{{ __('Distraídos') }}'
                    ],
                    datasets: [{
                        data: [
                            completedShiftCountToDay,
                            pendingShiftCountToDay,
                            cancelledShiftCountToDay,
                            distractedShiftCountToDay
                        ],
                        backgroundColor: [
                            hexToRgba('#16A34A', 0.8),
                            hexToRgba('#4F46E5', 0.8),
                            hexToRgba('#F59E0B', 0.8),
                            hexToRgba('#DC2626', 0.8),
                        ],
                        borderColor: [
                            '#16A34A', '#4F46E5', '#F59E0B', '#DC2626'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '60%',
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        title: {
                            display: true,
                            text: '{{ __('Distribución de estados (Hoy)') }}'
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        }
                    },
                    animation: {
                        animateRotate: true,
                        animateScale: true
                    }
                }
            });
        }

        // Chart 1: Completados por sede
        const ctxBranch = document.getElementById('chartByBranch');
        if (ctxBranch) {
            let labelsBranch = Object.keys(groupedShiftByBranch);
            let valuesBranch = Object.values(groupedShiftByBranch).map(Number);

            const bgColorsBranch = labelsBranch.map((_, i) => hexToRgba(basePalette[i % basePalette.length], 0.25));
            const bdColorsBranch = labelsBranch.map((_, i) => hexToRgba(basePalette[i % basePalette.length], 1));

            new Chart(ctxBranch, {
                type: 'bar',
                data: {
                    labels: labelsBranch,
                    datasets: [{
                        label: '{{ __('Turnos completados') }}',
                        data: valuesBranch,
                        backgroundColor: bgColorsBranch,
                        borderColor: bdColorsBranch,
                        borderWidth: 1,
                        borderRadius: 6,
                        maxBarThickness: 48
                    }]
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
                            text: '{{ __('Turnos completados por sede') }}'
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
                                text: '{{ __('Sede') }}'
                            },
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: '{{ __('Cantidad') }}'
                            },
                            ticks: {
                                stepSize: 1
                            },
                            grid: {
                                color: 'rgba(0,0,0,0.05)'
                            }
                        }
                    }
                }
            });
        }

        // Chart 2: Por módulo y sede (agrupadas)
        const ctxModule = document.getElementById('chartByModule');
        if (ctxModule) {
            const sedes = Object.keys(principalShiftByModule);
            const moduloSet = new Set();
            sedes.forEach(sede => {
                Object.keys(principalShiftByModule[sede] || {}).forEach(mod => moduloSet.add(mod));
            });
            const modulos = Array.from(moduloSet).sort((a, b) => Number(a) - Number(b));
            const datasets = modulos.map((mod, idx) => {
                const dataValues = sedes.map(sede => {
                    const valor = principalShiftByModule[sede] && principalShiftByModule[sede][mod] !=
                        null ?
                        principalShiftByModule[sede][mod] : 0;
                    return Number(valor);
                });
                const color = basePalette[idx % basePalette.length];
                return {
                    label: `{{ __('Módulo') }} ${mod}`,
                    data: dataValues,
                    backgroundColor: hexToRgba(color, 0.5),
                    borderColor: hexToRgba(color, 1),
                    borderWidth: 1
                };
            });

            new Chart(ctxModule, {
                type: 'bar',
                data: {
                    labels: sedes,
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top'
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        },
                        title: {
                            display: true,
                            text: '{{ __('Turnos por Módulo y Sede') }}'
                        }
                    },
                    scales: {
                        x: {
                            stacked: false,
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: '{{ __('Cantidad') }}'
                            },
                            ticks: {
                                stepSize: 1
                            },
                            grid: {
                                color: 'rgba(0,0,0,0.05)'
                            }
                        }
                    }
                }
            });
        }
    </script>
@endpush
