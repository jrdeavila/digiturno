<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();



// Command to delete distracted shifts
Artisan::command('delete:distracted-shifts', function () {
    $res = \App\Models\Shift::query()
        ->whereDate('created_at', now())
        ->whereIn('state', [
            \App\Enums\ShiftState::Distracted,
            \App\Enums\ShiftState::Pending,
            \App\Enums\ShiftState::PendingTransferred,
        ])
        ->delete();
    $this->info('Distracted shifts deleted successfully');
    $this->info("Total deleted: $res");
})->purpose('Delete all distracted shifts')->daily();
