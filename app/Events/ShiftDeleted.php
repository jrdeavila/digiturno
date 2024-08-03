<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ShiftDeleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels, Queueable;

    public \App\Models\Shift $shift;

    public function __construct(
        \App\Models\Shift $shift
    ) {
        $this->shift = $shift;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('rooms.' . $this->shift->room->id . '.attention_profiles.' . $this->shift->attentionProfile->id . '.shifts'),
            new Channel('rooms.' . $this->shift->room->id . '.shifts'),
        ];
    }

    public function broadcastAs()
    {
        return "shift.deleted";
    }

    public function broadcastWith()
    {
        return [
            'shift' => new \App\Http\Resources\ShiftResource($this->shift)
        ];
    }
}
