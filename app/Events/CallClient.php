<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CallClient implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels, Queueable;
    public \App\Models\Shift $shift;
    /**
     * Create a new event instance.
     */
    public function __construct(
        \App\Models\Shift $shift,
    ) {
        $this->shift = $shift;
    }


    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('rooms.' . $this->shift->room_id . '.clients'),
        ];
    }

    public function broadcastAs()
    {
        return 'client.call';
    }

    public function broadcastWith()
    {
        return [
            'client' => new \App\Http\Resources\ClientResource($this->shift->client),
            'module' => new \App\Http\Resources\ModuleResource($this->shift->module)
        ];
    }
}
