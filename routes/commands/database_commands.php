<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();



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
