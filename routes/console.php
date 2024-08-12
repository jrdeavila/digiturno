<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::job(function () {
    \App\Jobs\AssignShifts::dispatch();
})
    ->everySecond()
    ->name('assign-shifts');


// Run delete shifts pending every day at 00:00
Schedule::command('shifts:delete-pending')
    ->daily()
    ->at('00:00')
    ->name('delete-shifts-pending')
    ->withoutOverlapping();


Artisan::command('shifts:delete-pending', function () {
    $shifts = \App\Models\Shift::where('state', \App\Enums\ShiftState::Pending)
        ->where('created_at', '<', now()->subDays(1))
        ->get();

    foreach ($shifts as $shift) {
        $shift->delete();
    }
})->purpose('Delete shifts pending older than 1 day')->daily()->at('00:00')->name('delete-shifts-pending')->withoutOverlapping();
