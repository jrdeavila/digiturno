<?php

use Illuminate\Support\Facades\Artisan;

// Command to show all modules in a table format
Artisan::command('module:show', function () {
    $modules = \App\Models\Module::query()
        ->orderBy(function ($query) {
            $query->select('name')
                ->from('attention_profiles')
                ->whereColumn('attention_profiles.id', 'modules.attention_profile_id');
        })->get();

    $this->table(
        ['ID', 'Name', 'Attention Profile', 'Funcionario'],
        $modules->map(function ($module) {
            $attendant = $module->attendants()->latest()->first();
            return [
                $module->id,
                $module->name,
                $module->attentionProfile?->name ?? "SECCIONAL",
                $attendant?->name ?? "No asignado",
            ];
        })
    );
})->describe('Show all modules');

// Command to change module attention profile
// Using the flag --module_id and --attention_profile_id
Artisan::command('module:change_attention_profile {--module_id= : Module id} {--attention_profile_id= : Attention Profile id}', function () {
    $module_id = $this->option('module_id');
    $attention_profile_id = $this->option('attention_profile_id');

    $module = \App\Models\Module::find($module_id);
    if (!$module) {
        $this->error('Module not found');
        return;
    }

    $attention_profile = \App\Models\AttentionProfile::find($attention_profile_id);
    if (!$attention_profile) {
        $this->error('Attention Profile not found');
        return;
    }

    $module->attentionProfile()->associate($attention_profile);
    $module->save();

    $this->info('Attention Profile changed successfully');
})->describe('Change module attention profile');


// Command to show module connections in a table format
Artisan::command('module:connections {date? : datetime}', function ($date = null) {
    $modules = \App\Models\Module::query()
        ->whereNotNull('attention_profile_id')
        ->get();

    $this->table(
        ['Id', 'Sala', 'Modulo', 'Funcionarios'],
        $modules->map(function ($module) use ($date) {
            return [
                $module->id,
                $module->room->name,
                $module->name,
                // Pluck the name of the attendants from the attendants relationship
                $module->attendants()->whereDate('module_attendant_accesses.created_at',  $date)->pluck('attendant_id')
                    ->unique()
                    ->map(function ($attendant_id) {
                        return \App\Models\Attendant::find($attendant_id)->name;
                    })->join(', '),
            ];
        })
    );
})->describe('Show module connections');
