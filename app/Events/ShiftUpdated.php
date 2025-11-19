<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ShiftUpdated implements ShouldBroadcast
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
            new Channel('modules.' . $this->shift->module_id . '.shifts'),
            new Channel('rooms.' . $this->shift->room->id . '.shifts'),
            new Channel('modules.' . $this->shift->module_id),
        ];
    }

    public function broadcastAs()
    {
        return 'shift.updated';
    }
}
