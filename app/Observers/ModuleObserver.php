<?php

namespace App\Observers;

class ModuleObserver
{
    public function updated(\App\Models\Module $module)
    {
        \App\Events\ModuleUpdated::dispatch($module);
    }
}
