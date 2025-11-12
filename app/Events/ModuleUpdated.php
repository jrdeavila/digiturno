<?php

namespace App\Events;

use App\Http\Resources\ModuleResource;
use App\Models\Module;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ModuleUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels, Queueable;

    public Module $module;

    /**
     * Create a new event instance.
     */
    public function __construct(
        Module $module
    ) {
        $this->module = $module;
    }


    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('modules.' . $this->module->id),
        ];
    }

    public function broadcastAs()
    {
        return "module.updated";
    }
}
