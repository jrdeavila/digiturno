<?php

use Illuminate\Support\Facades\Artisan;



Artisan::command('attendant:put-offline', function () {
  $res = \App\Models\Attendant::query()
    ->update(['status' => \App\Enums\AttendantStatus::Offline]);
  \App\Models\Module::query()
    ->update(['status' => \App\Enums\ModuleStatus::Offline]);
  $this->info('Put all busy attendants offline');
  $this->info("Total updated: $res");
})->purpose('Put all busy attendants offline')
  ->dailyAt('00:00');
