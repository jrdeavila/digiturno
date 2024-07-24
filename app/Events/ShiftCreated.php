<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ShiftCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels, Queueable;

    /**
     * Create a new event instance.
     */
    public \App\Models\Shift $shift;

    public function __construct(
        \App\Models\Shift $shift
    ) {
        $this->shift = $shift;
    }

    public function broadcastOn()
    {
        return [
            new Channel('rooms.' . $this->shift->room_id . '.attention_profiles.' . $this->shift->attention_profile_id . '.shifts'),
        ];
    }

    public function broadcastAs()
    {
        return 'shift.created';
    }

    public function broadcastWith()
    {
        return [
            'shift' => new \App\Http\Resources\ShiftResource($this->shift)
        ];
    }
}
