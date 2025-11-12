<?php

namespace App\Listeners;

use App\Enums\ModuleStatus;
use App\Events\ModuleUpdated;
use Illuminate\Bus\Queueable;

class ModuleUpdatedListener
{
    use Queueable;

    public function __construct() {}

    public function handle(ModuleUpdated $event): void
    {
        if ($event->module->state === ModuleStatus::Offline->value) {
            $event->module->currentShifts()->delete();
        }
    }
}
