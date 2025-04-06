<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
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
            'dni' => $this->dni,
            'client_type' => $this->clientType->slug,
            'is_deleted' => $this->deleted_at !== null,
            'created_at' => $this->created_at->setTimezone('America/Bogota')->format('Y-m-d h:i A'),
            'updated_at' => $this->updated_at->setTimezone('America/Bogota')->format('Y-m-d h:i A'),
        ];
    }
}
