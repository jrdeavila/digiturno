<?php

use App\Enums\ShiftSpanishLabel;
use App\Enums\ShiftState;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;

// Command to get all shift of a (branch_id) branch in a date range
// Limited by (limit) parameter
// Date format: MM/DD/YYYY
Artisan::command('shift:all {branch_id} {from} {to} {limit?}', function ($branch_id, $from, $to, $limit = 100) {
  $branch = \App\Models\Branch::find($branch_id);
  if (!$branch) {
    $this->error('Branch not found');
    return;
  }
  $shifts = \App\Models\Shift::query()
    ->whereHas('room', function ($query) use ($branch_id) {
      $query->where('branch_id', $branch_id);
    })
    ->whereBetween('created_at', [now()->parse($from), now()->parse($to)])
    ->limit($limit)
    ->get();
  $this->info('Branch: ' . $branch->name);
  $this->info('Shifts: ' . $shifts->count());
  $shifts->each(function ($shift) {
    $this->info($shift->created_at->setTimezone('America/Bogota')->format('m/d/Y h:i A')
      . ' - ' . $shift->client->name . ' - ' . $shift->state);
  });
})->purpose('Get all shift of a (branch_id) branch in a date range')
  ->dailyAt('00:00');

// Command to generate csv with shift data in spanish language and save it in the storage folder
// Filter by branch_id and range date optionally

function convertShiftStatusToSpanish($status)
{
  $status = ShiftState::from($status);
  $match =  match ($status) {
    ShiftState::Pending => ShiftSpanishLabel::Pending,
    ShiftState::PendingTransferred => ShiftSpanishLabel::PendingTransferred,
    ShiftState::Transferred => ShiftSpanishLabel::Transferred,
    ShiftState::InProgress => ShiftSpanishLabel::InProgress,
    ShiftState::Completed => ShiftSpanishLabel::Completed,
    ShiftState::Cancelled => ShiftSpanishLabel::Cancelled,
    ShiftState::Distracted => ShiftSpanishLabel::Distracted,
    ShiftState::Qualified => ShiftSpanishLabel::Qualified,
    ShiftState::Called => ShiftSpanishLabel::Called,
  };
  return $match->value;
}

function convertUtf8($string)
{
  $string = str_replace(
    ['á', 'é', 'í', 'ó', 'ú', 'ñ', 'Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ'],
    ['a', 'e', 'i', 'o', 'u', 'n', 'A', 'E', 'I', 'O', 'U', 'N'],
    $string
  );
  return $string;
}

// Example: php artisan shift:csv branch=1 from=2021-11-01 to=2021-11-30
Artisan::command('shift:csv {from?} {to?} {branch_id?}', function ($from = null, $to = null, $branch_id = null) {

  // Validate the from date
  if ($from && !Carbon::parse($from)) {
    $this->error('Invalid from date');
    return;
  }


  // Validate the to date
  if ($to && !Carbon::parse($to)) {
    $this->error('Invalid to date');
    return;
  }


  // Validate the branch
  if ($branch_id && !\App\Models\Branch::find($branch_id)) {
    $this->error('Branch not found');
    return;
  }

  $shifts = \App\Models\Shift::query()
    ->when($branch_id, function ($query, $branch_id) {
      return $query->whereHas('room', function ($query) use ($branch_id) {
        $query->where('branch_id', $branch_id);
      });
    })
    ->when($from, function ($query, $from) {
      $startDate = Carbon::parse($from)->startOfDay();
      return $query->where('created_at', '>=', $startDate);
    })
    ->when($to, function ($query, $to) {
      $endDate = Carbon::parse($to)->endOfDay();
      return $query->where('created_at', '<=', $endDate);
    })
    ->whereIn('state', [
      ShiftState::Qualified,
      ShiftState::Transferred,
    ])
    ->get();
  $csv = \League\Csv\Writer::createFromString('');
  $csv->insertOne([
    'ID',
    'Servicios',
    'Sala',
    'Seccional',
    'Modulo',
    'Cliente',
    'Documento de Identidad',
    'Tipo de Cliente',
    'Estado',
    'Calificación',
    'Funcionario',
    'Tiempo de Atención',
    'Creado En',
    'Actualizado En',
  ]);


  foreach ($shifts as $shift) {
    $attendant = $shift->module?->attendants()->whereDate('module_attendant_accesses.created_at', now())->first();
    $timeToAttend = $shift->created_at->diffInMinutes($shift->updated_at);
    $timeToAttend = intval(number_format($timeToAttend, 2));
    $servicesToString = $shift->services->map(function ($service) {
      return $service->name;
    })->implode('|');
    $data = [
      $shift->id,
      $servicesToString,
      $shift->room->name,
      $shift->room->branch->name,
      $shift->module?->name,
      $shift->client->name,
      $shift->client->dni,
      $shift->client->clientType->getTypeAttribute($shift->client->clientType->slug),
      convertShiftStatusToSpanish($shift->state),
      $shift->qualification?->qualification,
      $attendant?->name,
      $timeToAttend,
      $shift->created_at->setTimezone('America/Bogota')->format('Y-m-d h:i A'),
      $shift->updated_at->setTimezone('America/Bogota')->format('Y-m-d h:i A'),
    ];
    $data = array_map('convertUtf8', $data);
    $csv->insertOne($data);
  }
  // Save the file in the storage folder and public folder
  $filename = 'shifts-' . now()->setTimezone('America/Bogota')->format('Y-m-d-H-i-s') . '.csv';
  $path = public_path('storage/' . $filename);
  file_put_contents($path, $csv->getContent());
  // Generate url to download the file
  $url = url('storage/' . $filename);
  $this->info('File saved successfully: ' . $url);
})->purpose('Generate ccv with shift data in spanish language and save it in the storage folder')
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

// Command to show all shifts of a (module_id) module in a specific (date) date or today

Artisan::command('shift:show {module_id} {date?}', function ($module_id, $date = null) {
  $module = \App\Models\Module::find($module_id);
  if (!$module) {
    $this->error('Module not found');
    return;
  }
  $date = $date ? now()->parse($date) : now();
  $shifts = \App\Models\Shift::query()
    ->where('module_id', $module_id)
    ->whereDate('created_at', $date)
    ->get();
  $this->info('Module: ' . $module->name);
  $this->info('Shifts: ' . $shifts->count());
  $shifts->each(function ($shift) {
    $this->info($shift->created_at->setTimezone('America/Bogota')->format('m/d/Y h:i A')
      . ' - ' . $shift->client->name . ' - ' . $shift->state);
  });
})->purpose('Show all shifts of a (module_id) module in a specific (date) date or today')
  ->dailyAt('00:00');


// Command to delete one shift assigned to a (dni) client registered in the system by an error
Artisan::command('shift:delete {dni}', function ($dni) {
  $client = \App\Models\Client::firstWhere('dni', $dni);
  if (!$client) {
    $this->error('Client not found');
    return;
  }
  $shift = \App\Models\Shift::where('client_id', $client->id)
    ->latest()
    ->first();
  if (!$shift) {
    $this->error('Shift not found');
    return;
  }
  // Show the shift created at and ask for confirmation (MM/DD/YYYY HH:mm AM/PM)
  $this->info('Shift created at: ' . $shift->created_at->setTimezone('America/Bogota')
    ->format('m/d/Y h:i A'));
  if (!$this->confirm('Do you want to delete this shift?')) {
    return;
  }
  $shift->delete();
  $this->info('Shift deleted successfully');
})->purpose('Delete one shift assigned to a (dni) client registered in the system by an error');



// Command to delete all shifts replicated today assigned to a (dni) client registered in the system by an error

Artisan::command('shift:delete-replicated {dni}', function ($dni) {
  $client = \App\Models\Client::firstWhere('dni', $dni);
  if (!$client) {
    $this->error('Client not found');
    return;
  }
  $shifts = \App\Models\Shift::where('client_id', $client->id)
    // ->whereDate('created_at', now()->toDateString())
    ->get();
  if ($shifts->count() === 0) {
    $this->error('Shifts not found');
    return;
  }
  // Show the number of shifts to delete
  $this->info('Client: ' . $client->name . ' - ' . $client->dni);
  $this->info('Shifts to delete: ' . $shifts->count() . ' (except the first one)');
  for ($i = 0; $i < $shifts->count(); $i++) {
    $shift = $shifts[$i];
    $this->info($i + 1 .  '. Shift created at: ' . $shift->created_at->setTimezone('America/Bogota')
      ->format('m/d/Y h:i A') . ' - ' . $shift->room->name . ' - ' . $shift->state);
  }
  // Ask many indexes to delete separated by commas or ranges (1, 3, 5-7)
  $indexes = $this->ask('Enter the indexes of the shifts to delete separated by commas or ranges (1, 3, 5-7)');
  $indexes = collect(explode(',', $indexes))
    ->map(function ($index) {
      if (str_contains($index, '-')) {
        $range = explode('-', $index);
        return range($range[0], $range[1]);
      }
      return $index;
    })
    ->flatten()
    ->map(function ($index) {
      return (int) $index;
    });

  // Filter the shifts to delete
  $shifts = $shifts->filter(function ($shift, $index) use ($indexes) {
    return $indexes->contains($index + 1);
  });



  // Ask for confirmation
  if (!$this->confirm('Do you want to delete these shifts?')) {
    return;
  }
  // Delete all shifts, except the first one
  $shifts->each(function ($shift) {
    $shift->delete();
  });

  $this->info('Shifts deleted successfully');
})->purpose('Delete all shifts replicated today assigned to a (dni) client registered in the system by an error');
