<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoomAttentionProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'rooms' => 'required|array',
        ];
    }


    public function roomAttentionProfile(
        \App\Models\AttentionProfile $attentionProfile
    ) {
        $rooms = $this->rooms;
        $attentionProfile->rooms()->sync($rooms);
        return $attentionProfile;
    }

    public function updateRoomAttentionProfile(
        \App\Models\AttentionProfile $attentionProfile
    ) {
        $rooms = $this->rooms;
        $attentionProfile->rooms()->sync($rooms);
        return $attentionProfile;
    }
}
