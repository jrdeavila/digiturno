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
Schedule::command('shifts:clean')
    ->daily()
    ->at('00:00')
    ->name('delete-shifts-pending')
    ->withoutOverlapping();


Artisan::command('shifts:clean', function () {
    $shifts = \App\Models\Shift::whereIn('state', [
        \App\Enums\ShiftState::Pending,
        \App\Enums\ShiftState::PendingTransferred,
        \App\Enums\ShiftState::InProgress,
        \App\Enums\ShiftState::Completed,
    ])
        ->where('created_at', '<', now()->subDays(1))
        ->get();

    foreach ($shifts as $shift) {
        $shift->delete();
    }
})->purpose('Delete all shifts pending, pending-transferred, in-progress and completed older than 1 day')->daily()->at('00:00')->name('delete-shifts-pending')->withoutOverlapping();


Artisan::command('shifts:clean-now', function () {
    $shifts = \App\Models\Shift::whereIn('state', [\App\Enums\ShiftState::Pending, \App\Enums\ShiftState::PendingTransferred, \App\Enums\ShiftState::InProgress, \App\Enums\ShiftState::Completed,])->whereDate('created_at', now())->get();
    foreach ($shifts as $shift) {
        $shift->delete();
    }
})->purpose('Delete all shifts pending, pending-transferred, in-progress and completed older than now')->withoutOverlapping();
