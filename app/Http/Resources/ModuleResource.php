<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ModuleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'ip_address' => $this->ip_address,
            'room' => new RoomResource($this->room),
            'type' => $this->clientType?->name,
            'status' => $this->status,
            'enabled' => $this->enabled,
            'attention_profile_id' => $this->attentionProfile?->id,
            'module_type_id' => $this->module_type_id,
            'current_attendant_id' => $this->currentAttendant()?->id,
        ];
    }
}
