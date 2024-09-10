<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MontarSeccionalesConModulos extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $Sucursales = [
            "Manaure" => [
                'Principal' => [
                    'Receptor 1' => [
                        'ip' => '0.0.0.20',
                        'tipo' => 2,
                    ],
                ]
            ],
            "La Paz" => [
                'Principal' => [
                    'Receptor 1' => [
                        'ip' => '0.0.19',
                        'tipo' => 2,
                    ],
                ]
            ],
            "Becerril" => [
                'Principal' => [
                    'Receptor 1' => [
                        'ip' => '0.0.0.18',
                        'tipo' => 2,
                    ],

                ],
            ],
            "La Jagua de Ibírico" => [

                'Principal' => [
                    'Receptor 1' => [
                        'ip' => '0.0.0.17',
                        'tipo' => 2,
                    ],
                    'Receptor 2' => [
                        'ip' => '0.0.0.16',
                        'tipo' => 2,
                    ],
                ]

            ],
            "Chiriguana" => [
                'Principal'  => [
                    'Receptor 1' => [
                        'ip' => '0.0.0.15',
                        'tipo' => 2,
                    ],
                ]
            ],
            "Agustin Codazzi" => [
                'Principal' => [
                    'Receptor 1' => [
                        'ip' => '0.0.0.14',
                        'tipo' => 2,
                    ],
                    'Receptor 2' => [
                        'ip' => '0.0.0.13',
                        'tipo' => 2,
                    ],
                ],
            ],

            "Bosconia" => [
                'Principal' => [
                    'Receptor 1' => [
                        'ip' => '0.0.0.12',
                        'tipo' => 2,
                    ],
                    'Receptor 2' => [
                        'ip' => '0.0.0.11',
                        'tipo' => 2,
                    ],
                ]
            ],
            "El Copey" => [

                'Principal' => [
                    'Receptor 1' => [
                        'ip' => '0.0.0.10',
                        'tipo' => 2,
                    ],
                ]

            ],
            "Astrea" => [
                'Principal' => [
                    'Receptor 1' => [
                        'ip' => '0.0.0.9',
                        'tipo' => 2,
                    ],
                ],

            ],
            "Chimichagua" => [
                'Principal' => [
                    'Receptor 1' => [
                        'ip' => '0.0.0.8',
                        'tipo' => 2,
                    ],
                ]
            ],
            "Pueblo Bello" => [
                'Principal' => [
                    'Receptor 1' => [
                        'ip' => '0.0.0.7',
                        'tipo' => 2,
                    ],
                ]
            ],
            "Valledupar" => [
                'Principal' => [
                    '2' => [
                        'ip' => '0.0.1.1',
                        'tipo' => 1,
                    ],
                    '3' => [
                        'ip' => '0.0.1.2',
                        'tipo' => 1,
                    ],
                    '4' => [
                        'ip' => '0.0.1.3',
                        'tipo' => 1,
                    ],
                    '5' => [
                        'ip' => '0.0.1.4',
                        'tipo' => 1,
                    ],
                    '6' => [
                        'ip' => '0.0.1.5',
                        'tipo' => 1,
                    ],
                    '7' => [
                        'ip' => '0.0.1.6',
                        'tipo' => 1,
                    ],
                    '8' => [
                        'ip' => '0.0.1.7',
                        'tipo' => 1,
                    ],
                    '9' => [
                        'ip' => '0.0.1.8',
                        'tipo' => 1,
                    ],
                    '10' => [
                        'ip' => '0.0.1.9',
                        'tipo' => 1,
                    ],
                    '11' => [
                        'ip' => '0.0.1.10',
                        'tipo' => 1,
                    ],
                    '12' => [
                        'ip' => '0.0.1.11',
                        'tipo' => 1,
                    ],
                    '13' => [
                        'ip' => '0.0.1.12',
                        'tipo' => 1,
                    ],
                    'Receptor 1' => [
                        'ip' => '0.0.1.13',
                        'tipo' => 3,
                    ],
                    'Receptor 2' => [
                        'ip' => '0.0.1.14',
                        'tipo' => 3,
                    ],

                ],
                'Receptora la cuarta' => [
                    'Receptor 1' => [
                        'ip' => '0.0.2.1',
                        'tipo' => 3,
                    ],
                    '1' => [
                        'ip' => '0.0.2.2',
                        'tipo' => 1,
                    ],
                    '2' => [
                        'ip' => '0.0.2.3',
                        'tipo' => 1,
                    ],
                    '24' => [
                        'ip' => '0.0.2.4',
                        'tipo' => 1,
                    ],
                ]
            ]
        ];

        foreach ($Sucursales as $sucursal => $receptores) {
            $branch = \App\Models\Branch::create([
                'name' => $sucursal,
                'address' => 'Calle 0 # 0 - 0',
            ]);
            // if ($sucursal == 'Valledupar') {
            foreach ($receptores as $receptor => $data) {
                $room = $branch->rooms()->create([
                    'name' => $receptor,
                ]);
                foreach ($data as $receptor => $data) {
                    $module = $room->modules()->create([
                        'name' => $receptor,
                        'ip_address' => $data['ip'],
                        'client_type_id' => 1,
                        'module_type_id' => $data['tipo'],
                    ]);
                }
            }
            // }
            // $room = $branch->rooms()->create([
            //     'name' => $sucursal . ' 1',
            // ]);
            // foreach ($receptores as $receptor => $data) {
            //     $module = $room->modules()->create([
            //         'name' => $receptor,
            //         'ip_address' => $data['ip'],
            //         'client_type_id' => 1,
            //         'module_type_id' => $data['tipo'],
            //     ]);
            // }
        }
    }
}
