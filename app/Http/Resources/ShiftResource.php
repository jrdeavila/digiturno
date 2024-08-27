<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShiftResource extends JsonResource
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
            'room' => $this->room->name,
            'attention_profile' => $this->attentionProfile->name,
            'client' => new \App\Http\Resources\ClientResource($this->client),
            'state' => $this->state,
            'module' => $this->module?->name,
            'module_id' => $this->module_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
