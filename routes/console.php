<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();


// Command to delete distracted shifts
Artisan::command('delete:distracted-shifts', function () {
    \App\Models\Shift::query()
        ->whereDate('created_at', now())
        ->where('state', \App\Enums\ShiftState::Distracted)
        ->delete();
    $this->info('Distracted shifts deleted successfully');
})->purpose('Delete all distracted shifts')->daily();
