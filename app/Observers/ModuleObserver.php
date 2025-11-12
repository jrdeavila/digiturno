<?php

namespace App\Observers;

use App\Events\ModuleUpdated;

class ModuleObserver
{
    public function updated(\App\Models\Module $module)
    {
        ModuleUpdated::dispatch($module);
    }
}
