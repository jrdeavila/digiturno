<?php

use Illuminate\Support\Facades\Artisan;

// Command to get all branch
Artisan::command('branch:all', function () {
  $branches = \App\Models\Branch::all();
  $this->info('Branches: ' . $branches->count());
  $branches->each(function ($branch) {
    $this->info("$branch->id - $branch->name");
  });
})->purpose('Get all branch')
  ->dailyAt('00:00');
