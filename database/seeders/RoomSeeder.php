<?php

namespace Database\Seeders;

use App\Models\AttentionProfile;
use App\Models\Branch;
use App\Models\Module;
use App\Models\Room;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sectionals = [
            'Astrea' => 1,
            'La Jagua de Ibirico' => 2,
            'El Paso - La Loma' => 1,
            'Becerril' => 1,
            'Manaure' => 1,
            'Chimichagua' => 1,
            'Chiriguana' => 1,
            'La Paz' => 1,
            'El Copey' => 1,
            'San Diego' => 1,
            'Pueblo Bello' => 1,
            'Codazzi' => 2,
        ];

        $branchs = [];
        foreach ($sectionals as $sectional => $count) {
            $modules = [];
            for ($i = 1; $i <= $count; $i++) {
                $modules[] = [
                    'name' => $i,
                    'module_type_id' => 2,
                    'client_type_id' => 4,
                    "attention_profiles" => [
                        'CAJA',
                        'ASESORIA JURIDICA',
                        'CAE',
                    ],
                ];
            }
            $rooms = [
                [
                    'name' => 'Principal',
                    'modules' => $modules
                ]
            ];


            $branchs[] = [
                'name' => $sectional,
                'rooms' => $rooms,
            ];
        }

        $branchs = [
            ...$branchs,
            [
                'name' => 'Principal',
                'rooms' => [
                    [
                        'name' => 'Principal',
                        'modules' => [
                            [
                                'name' => '1',
                                'module_type_id' => 1,
                                'client_type_id' => 4,
                                'responsable_id' => 639,
                                'attention_profiles' => [
                                    'CAJA',
                                ]
                            ],
                            [
                                "name" => "2",
                                "module_type_id" => 1,
                                "client_type_id" => 4,
                                "attention_profiles" => [
                                    'CAJA',
                                ]
                            ],
                            [
                                "name" => "3",
                                "module_type_id" => 1,
                                "client_type_id" => 4,
                                "attention_profiles" => [
                                    'CAJA',
                                ]
                            ],
                            [
                                "name" => "4",
                                "module_type_id" => 1,
                                "client_type_id" => 4,
                                "attention_profiles" => [
                                    'CAJA',
                                ]
                            ],
                            [
                                "name" => "5",
                                "module_type_id" => 1,
                                "client_type_id" => 4,
                                "attention_profiles" => [
                                    'CAJA',
                                ]
                            ],
                            [
                                "name" => "6",
                                "module_type_id" => 1,
                                "client_type_id" => 4,
                                "attention_profiles" => [
                                    'CAJA',
                                ]
                            ],
                            [
                                "name" => "7",
                                "module_type_id" => 1,
                                "client_type_id" => 4,
                                "attention_profiles" => [
                                    'CAE',
                                ]
                            ],
                            [
                                "name" => "8",
                                "module_type_id" => 1,
                                "client_type_id" => 4,
                                "attention_profiles" => [
                                    'CAE',
                                ]
                            ],
                            [
                                "name" => "9",
                                "module_type_id" => 1,
                                "client_type_id" => 4,
                                "attention_profiles" => [
                                    'CAE',
                                ]
                            ],
                            [
                                "name" => "10",
                                "module_type_id" => 1,
                                "client_type_id" => 4,
                                "attention_profiles" => [
                                    'ASESORIA JURIDICA',
                                ]
                            ],
                            [
                                "name" => "11",
                                "module_type_id" => 1,
                                "client_type_id" => 4,
                                "attention_profiles" => [
                                    'ASESORIA JURIDICA',
                                ]
                            ],
                            [
                                "name" => "12",
                                "module_type_id" => 1,
                                "client_type_id" => 4,
                                "attention_profiles" => [
                                    'ASESORIA JURIDICA',
                                ]
                            ],
                            [
                                "name" => "13",
                                "module_type_id" => 1,
                                "client_type_id" => 4,
                                "attention_profiles" => [
                                    "CAE",
                                ]
                            ],
                            [
                                "name" => "Receptor 1",
                                "module_type_id" => 3,
                                "responsable_id" => 639

                            ],
                            [
                                "name" => "Receptor 2",
                                "module_type_id" => 3,
                            ],
                            [
                                "name" => "Pantalla 1",
                                "module_type_id" => 4,
                            ],
                            [
                                "name" => "Pantalla 2",
                                "module_type_id" => 4,
                            ]
                        ]
                    ],
                    [
                        'name' => 'Receptora la cuarta',
                        'modules' => [
                            [
                                'name' => '1',
                                'module_type_id' => 1,
                                'client_type_id' => 4,
                                'attention_profiles' => [
                                    'CAJA',
                                ]
                            ],
                            [
                                "name" => "2",
                                "module_type_id" => 1,
                                "client_type_id" => 4,
                                "attention_profiles" => [
                                    'CAJA',
                                ]
                            ],
                            [
                                "name" => "3",
                                "module_type_id" => 1,
                                "client_type_id" => 4,
                                "attention_profiles" => [
                                    'CAE',
                                ]
                            ],
                            [
                                "name" => "Receptor 1",
                                "module_type_id" => 3,
                            ],
                            [
                                "name" => "Pantalla 1",
                                "module_type_id" => 4,
                            ]
                        ]
                    ],
                ],
            ],
        ];
        try {

            DB::beginTransaction();
            foreach ($branchs as $branch) {
                $branchId = Branch::firstOrCreate(['name' => $branch['name']])->id;
                foreach ($branch['rooms'] as $room) {
                    $r =  Room::firstOrCreate([
                        'name' => $room['name'],
                        'branch_id' => $branchId,
                    ]);
                    if (array_key_exists('modules', $room)) {
                        $modules = $room['modules'];
                        foreach ($modules as $module) {
                            $m = Module::firstOrCreate([
                                'name' => $module['name'],
                                'room_id' => $r->id,
                                'module_type_id' => $module['module_type_id'],
                                'client_type_id' => array_key_exists('client_type_id', $module) ? $module['client_type_id'] : null,
                                'responsable_id' => array_key_exists('responsable_id', $module) ? $module['responsable_id'] : null
                            ]);
                            if (array_key_exists('attention_profiles', $module)) {
                                $attention_profiles = $module['attention_profiles'];
                                foreach ($attention_profiles as $attention_profile) {
                                    $ap = AttentionProfile::where('name', $attention_profile)->firstOrFail();
                                    $m->attentionProfiles()->attach($ap->id);
                                    $r->attentionProfiles()->attach($ap->id);
                                }
                            }
                        }
                    }
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
