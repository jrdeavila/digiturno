<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AttendantUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    public \App\Models\Attendant $attendant;
    /**
     * Create a new event instance.
     */
    public function __construct(
        \App\Models\Attendant $attendant
    ) {
        $this->attendant = $attendant;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('attendants.' . $this->attendant->id),
        ];
    }

    public function broadcastAs()
    {
        return "attendant.updated";
    }

    public function broadcastWith()
    {
        return [
            'attendant' => new \App\Http\Resources\AttendantResource($this->attendant)
        ];
    }
}
