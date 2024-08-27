<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();



// Command to delete distracted shifts
Artisan::command('shifts:clear-incomplete', function () {
    $res = \App\Models\Shift::query()
        // ->whereDate('created_at', now())
        ->whereIn('state', [
            \App\Enums\ShiftState::Distracted,
            \App\Enums\ShiftState::Pending,
            \App\Enums\ShiftState::PendingTransferred,
        ])
        ->delete();
    $this->info('Delete all incomplete shifts');
    $this->info("Total deleted: $res");
})->purpose('Delete all incomplete shifts')->daily();

Artisan::command('attendant:put-offline', function () {
    $res = \App\Models\Attendant::query()
        ->update(['status' => \App\Enums\AttendantStatus::Offline]);
    \App\Models\Module::query()
        ->update(['status' => \App\Enums\ModuleStatus::Offline]);
    $this->info('Put all busy attendants offline');
    $this->info("Total updated: $res");
})->purpose('Put all busy attendants offline')->daily();
