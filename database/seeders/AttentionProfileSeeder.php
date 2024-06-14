<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;



class AttentionProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $attentionProfiles = require __DIR__ . '/data/attentionProfiles.php';

        foreach ($attentionProfiles as $key => $value) {
            $ap = \App\Models\AttentionProfile::create([
                'name' => $key,
            ]);
            for ($i = 0; $i < count($value); $i++) {
                $ap->attentionProfiles()->create([
                    'name' => $value[$i]
                ]);
            }
        }
    }
}
