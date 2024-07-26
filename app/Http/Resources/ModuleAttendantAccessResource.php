<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ModuleAttendantAccessResource extends JsonResource
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
            'module' => new ModuleResource($this->module),
            'attendant' => new AttendantResource($this->attendant),
            'access_date' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
