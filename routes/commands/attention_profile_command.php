<?php

use Illuminate\Support\Facades\Artisan;


// Command to show all Attention Profiles in a table format
Artisan::command('attention_profile:show', function () {
    $attention_profiles = \App\Models\AttentionProfile::all();

    $this->table(
        ['ID', 'Name'],
        $attention_profiles->map(function ($attention_profile) {
            return [
                $attention_profile->id,
                $attention_profile->name,
            ];
        })
    );
})->describe('Show all Attention Profiles');
