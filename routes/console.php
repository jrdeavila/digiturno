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

// Schedule::job(function () {
//     \App\Jobs\VerifyRepeatedShifts::dispatch();
// })
//     ->everyFiveSeconds()
//     ->name('verify-repeated-shifts');

// Put all modules in offline status on 8:00AM, 12:00 PM and 6:00 PM
Schedule::call(function () {
    \App\Models\Module::query()->update(['status' => 'offline']);
})->at('08:00', '12:00', '18:00');


// Put all attendances in free status on 8:00AM, 12:00 PM and 6:00 PM
Schedule::call(function () {
    \App\Models\Attendant::query()->update(['status' => 'free']);
})->at('08:00', '12:00', '18:00')
    ->name('reset-attendants-status');


// Check if attendant has a shift in progress and put it in free if not
Schedule::call(function () {
    $attendants =  \App\Models\Attendant::query()
        ->where('status',  \App\Enums\AttendantStatus::Busy)
        ->get();
    foreach ($attendants as $attendant) {
        if (!$attendant->haveShiftInProgress() && !$attendant->haveShiftCompleted()) {
            $attendant->update(['status' => \App\Enums\AttendantStatus::Free]);
        }
    }
})->everySecond()
    ->name('check-attendants-status');


// Command to delete distracted shifts
Artisan::command('delete:distracted-shifts', function () {
    \App\Models\Shift::query()
        ->whereDate('created_at', now())
        ->where('state', \App\Enums\ShiftState::Distracted)
        ->delete();
    $this->info('Distracted shifts deleted successfully');
})->purpose('Delete all distracted shifts')->daily();
