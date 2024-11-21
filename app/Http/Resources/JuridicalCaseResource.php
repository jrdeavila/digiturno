<?php

namespace App\Http\Resources;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JuridicalCaseResource extends JsonResource
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
            'status' => $this->status,
            'subject' => $this->subject,
            'client_id' => $this->client_id,
            'attendant_id' => $this->attendant_id,
            'client' => new ClientResource($this->client),
            'attendant' => new AttendantResource($this->attendant),
            'observations' => JuridicalCaseObservationResource::collection($this->observations),
        ];
    }

    public function withResponse(Request $request, JsonResponse $response)
    {
        $response->setData($this->resource);
    }
}
