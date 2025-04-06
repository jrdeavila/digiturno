<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ModuleOffline implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public \App\Models\Module $module;
    /**
     * Create a new job instance.
     */
    public function __construct(
        \App\Models\Module $module
    ) {
        $this->module = $module;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->module->status = \App\Enums\ModuleStatus::Offline;
        $this->module->save();
    }
}
