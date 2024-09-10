<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
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


// Command to delete all shifts replicated many times (> 5 times) in the current day.
// Having in mind that the shift has a client_id

Artisan::command('shifts:clear-replicated', function () {
    $res = \App\Models\Shift::query()
        ->whereDate('created_at', now())
        ->whereNotNull('client_id')
        ->select('client_id')
        ->selectRaw('count(client_id) as total')
        ->groupBy('client_id')
        ->having('total', '>', 5)
        ->delete();
    $this->info('Delete all replicated shifts');
    $this->info("Total deleted: $res");
})
    ->purpose('Delete all replicated shifts')
    ->everyMinute();

// Export the database to a file in the storage folder

Artisan::command('db:export', function () {
    $folder = "backups";
    $filename = 'database-' . now()->setTimezone('America/Bogota')->format('Y-m-d-H-i-s') . '.sql';
    // create the folder if it does not exist
    if (!is_dir(storage_path($folder))) {
        mkdir(storage_path($folder));
    }
    $path = storage_path($folder . DIRECTORY_SEPARATOR . $filename);
    // $path = storage_path($filename);
    // Get the database credentials
    $host = env('DB_HOST');
    $port = env('DB_PORT');
    $user = env('DB_USERNAME');
    $pass = env('DB_PASSWORD');
    $db = env('DB_DATABASE');

    // Dump the pgsql database
    $command = "PGPASSWORD=$pass pg_dump -h $host -p $port -U $user $db > $path";
    exec($command);

    $this->info('Database exported successfully');
})->purpose('Export the database to a file in the storage folder');
