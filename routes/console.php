<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();


// Command to delete all incomplete shifts
Artisan::command('shifts:clear-incomplete', function () {
    $res = \App\Models\Shift::query()
        ->whereIn('state', [
            \App\Enums\ShiftState::Pending,
            \App\Enums\ShiftState::PendingTransferred,
        ])
        ->whereDate(
            'created_at',
            '<',
            now()->subHours(2)
        )
        ->delete();
    $this->info('Delete all incomplete shifts');
    $this->info("Total deleted: $res");
})->purpose('Delete all incomplete shifts')
    ->everyMinute();

// Command to delete all distracted shifts
Artisan::command('shifts:clear-distracted', function () {
    $res = \App\Models\Shift::query()
        ->whereDate(
            'created_at',
            '<',
            now()->subMinutes(30)
        )
        ->where('state', \App\Enums\ShiftState::Distracted)
        ->delete();
    $this->info('Delete all distracted shifts');
    $this->info("Total deleted: $res");
})->purpose('Delete all distracted shifts')
    ->everyMinute();

Artisan::command('attendant:put-offline', function () {
    $res = \App\Models\Attendant::query()
        ->update(['status' => \App\Enums\AttendantStatus::Offline]);
    \App\Models\Module::query()
        ->update(['status' => \App\Enums\ModuleStatus::Offline]);
    $this->info('Put all busy attendants offline');
    $this->info("Total updated: $res");
})->purpose('Put all busy attendants offline')
    ->dailyAt('00:00');


// Command to delete all in_progress shifts
Artisan::command('shifts:clear-in-progress', function () {
    $res = \App\Models\Shift::query()
        ->whereDate(
            'created_at',
            '<',
            now()->subHours(2)
        )
        ->where('state', \App\Enums\ShiftState::InProgress)
        ->delete();
    $this->info('Delete all in_progress shifts');
    $this->info("Total deleted: $res");
})
    ->purpose('Delete all in_progress shifts')
    ->everyMinute();
