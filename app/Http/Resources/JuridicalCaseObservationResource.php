<?php

namespace App\Http\Resources;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JuridicalCaseObservationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            'content' => $this->content,
            'attendant_id' => $this->attendant_id,
            "juridical_case_id" => $this->juridical_case_id,
            'attendant' => new AttendantResource($this->whenLoaded('attendant')),
            "juridical_case" => new JuridicalCaseResource($this->whenLoaded('juridicalCase')),
        ];
    }

    public function withResponse(Request $request, JsonResponse $response)
    {
        $response->setData($this->resource);
    }
}
