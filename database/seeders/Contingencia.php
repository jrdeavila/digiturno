<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class Contingencia extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Module::find(41)->update(['attention_profile_id' => 2, 'client_type_id' => 3,]);
        Module::find(42)->update(['attention_profile_id' => 2, 'client_type_id' => 3,]);
        Module::find(43)->update(['attention_profile_id' => 2, 'client_type_id' => 3,]);
        Module::find(44)->update(['attention_profile_id' => 6, 'client_type_id' => 3,]);
    }
}
