<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();



Artisan::command('attendant:put-offline', function () {
    $res = \App\Models\Attendant::query()
        ->update(['status' => \App\Enums\AttendantStatus::Offline]);
    \App\Models\Module::query()
        ->update(['status' => \App\Enums\ModuleStatus::Offline]);
    $this->info('Put all busy attendants offline');
    $this->info("Total updated: $res");
})->purpose('Put all busy attendants offline')
    ->dailyAt('00:00');


// Command to get all shift of a (from_module_id) module in a specific (date) date and assign them to another (to_module_id) module
// Limited by (limit) parameter
Artisan::command('shift:reassign {from_module_id} {to_module_id} {date} {limit?}', function ($from_module_id, $to_module_id, $date, $limit = 100) {
    $from_module = \App\Models\Module::find($from_module_id);
    if (!$from_module) {
        $this->error('Module not found');
        return;
    }
    $to_module = \App\Models\Module::find($to_module_id);
    if (!$to_module) {
        $this->error('Module not found');
        return;
    }
    $shifts = \App\Models\Shift::query()
        ->where('module_id', $from_module_id)
        ->whereDate('created_at', $date)
        ->limit($limit)
        ->get();
    $this->info('Shifts to replicate: ' . $shifts->count());
    $shifts->each(function ($shift) use ($to_module) {
        $shift->module_id = $to_module->id;
        $shift->save();
    });
    $this->info('Shifts replicated successfully');
})->purpose('Get all shift of a (from_module_id) module in a specific (date) date and assign them to another (to_module_id) module')
    ->dailyAt('00:00');




// Command to delete all shifts replicated today assigned to a (dni) client registered in the system by an error

Artisan::command('shift:delete-replicated {dni}', function ($dni) {
    $client = \App\Models\Client::firstWhere('dni', $dni);
    if (!$client) {
        $this->error('Client not found');
        return;
    }
    $shifts = \App\Models\Shift::query()
        ->where('client_id', $client->id)
        ->whereDate('created_at', now()->toDateString())
        ->get();
    $this->info('Shifts to delete: ' . $shifts->count());
    // Delete all shifts, except the first one
    $shifts->slice(1)->each->deleted();
    $this->info('Shifts deleted successfully');
})->purpose('Delete all shifts replicated today assigned to a (dni) client registered in the system by an error');



// Export the database to a file in the storage folder

Artisan::command('db:export', function () {
    $folder = "backups";
    // Delete yesterday's backup
    $yesterday = now()->subDay()->format('Y-m-d');

    $files = glob(storage_path($folder . DIRECTORY_SEPARATOR . "database-$yesterday-*.sql"));
    foreach ($files as $file) {
        unlink($file);
    }

    // Create the filename

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
})->purpose('Export the database to a file in the storage folder')
    ->hourly();

// Import the database from a file in the storage folder
Artisan::command('db:import {filename}', function ($filename) {
    $folder = "backups";
    $path = storage_path($folder . DIRECTORY_SEPARATOR . $filename);
    // $path = storage_path($filename);
    // Get the database credentials
    $host = env('DB_HOST');
    $port = env('DB_PORT');
    $user = env('DB_USERNAME');
    $pass = env('DB_PASSWORD');
    $db = env('DB_DATABASE');

    // Dump the pgsql database
    $command = "PGPASSWORD=$pass psql -h $host -p $port -U $user $db < $path";
    exec($command);

    $this->info('Database imported successfully');
})->purpose('Import the database from a file in the storage folder');
