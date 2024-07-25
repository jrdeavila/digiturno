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
        if ($this->shift->state === 'in_progress' || $this->shift->state === 'completed' || $this->shift->state === 'qualified' || $this->shift->state === 'transferred') {
            return [
                new Channel('rooms.' . $this->shift->room->id . '.attention_profiles.' . $this->shift->attentionProfile->id . '.shifts'),
                new Channel('modules.' . $this->shift->module_id . '.current-shift'),
                new Channel('rooms.' . $this->shift->room->id . '.shifts'),
            ];
        }
        return [
            new Channel('rooms.' . $this->shift->room->id . '.attention_profiles.' . $this->shift->attentionProfile->id . '.shifts'),
            new Channel('rooms.' . $this->shift->room->id . '.shifts'),
        ];
    }

    public function broadcastAs()
    {
        $as = [
            "pending" => 'shift.pending',
            "pending-transferred" => 'shift.created',
            "distracted" => 'shift.distracted',
            "in_progress" => 'shift.in-progress',
            "completed" => 'shift.completed',
            "qualified" => 'shift.qualified',
            "transferred" => 'shift.transferred',
            "cancelled" => 'shift.cancelled',
        ];
        return $as[$this->shift->state];
    }

    public function broadcastWith()
    {
        return [
            'shift' => new \App\Http\Resources\ShiftResource($this->shift)
        ];
    }
}
