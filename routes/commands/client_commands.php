<?php


use Illuminate\Support\Facades\Artisan;


// Command to find clients by dni or name
// Using the flag --dni or --name
Artisan::command('client:find {--dni= : Client dni} {--name= : Client name}', function () {
  $dni = $this->option('dni');
  $name = $this->option('name');

  $clients = \App\Models\Client::query();

  $this->info('Searching for client...');

  if ($dni) {
    $clients->where('dni',  $dni);
  }

  if ($name) {
    // In lowercase if the name is not case sensitive
    $name = strtolower($name);
    $clients->whereRaw('LOWER(name) LIKE ?', ["%{$name}%"]);
  }


  $client = $clients->first();
  if (!$client) {
    $this->error('Client not found');
    return;
  }
  $this->info($client);
})->describe('Find a client by dni or name');
