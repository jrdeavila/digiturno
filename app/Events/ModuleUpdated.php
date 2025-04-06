<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ModuleUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public \App\Models\Module $module;

    /**
     * Create a new event instance.
     */
    public function __construct(
        \App\Models\Module $module
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

    public function broadcastWith()
    {
        return [
            'module' => new \App\Http\Resources\ModuleResource($this->module)
        ];
    }
}
