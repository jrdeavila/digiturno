<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShiftWithAttentionProfileRequest extends FormRequest
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
            'room_id' => 'required|exists:rooms,id',
            'client' => 'required|array',
            'client.name' => 'required|string',
            'client.dni' => [
                'required',
                'string',
                'regex:/^[0-9]+$/',
                function ($attribute, $value, $fail) {
                    $client = \App\Models\Client::firstWhere('dni', $value);
                    if (

                        $client && $this->route('shift')?->client->id != $client->id &&
                        $client->shifts()->where('state', 'pending')->exists()
                    ) {
                        $fail('client_has_pending_shift');
                    }
                },
            ],
            'client.client_type_id' => 'required|exists:client_types,id',
            'attention_profile_id' => 'required|exists:attention_profiles,id',
            'module_id' => 'sometimes|exists:modules,id',
        ];
    }

    public function createShift()
    {
        \Illuminate\Support\Facades\DB::beginTransaction();

        $client = \App\Models\Client::firstWhere('dni', $this->validated()['client']['dni']);
        if (!$client) {
            $client = \App\Models\Client::create($this->validated()['client']);
        } else {
            $client->update($this->validated()['client']);
        }


        $data = [
            'client_id' => $client->id,
            'room_id' => $this->validated()['room_id'],
            'attention_profile_id' => $this->validated()['attention_profile_id'],
            'state' => 'pending',
            'module_id' => null,
        ];

        $shift = \App\Models\Shift::create($data);

        \Illuminate\Support\Facades\DB::commit();

        return $shift;
    }

    public function updateShift(\App\Models\Shift $shift)
    {
        \Illuminate\Support\Facades\DB::beginTransaction();
        $client = \App\Models\Client::firstWhere('dni', $this->validated()['client']['dni']);
        $client->update($this->validated()['client']);

        $room = \App\Models\Room::find($this->validated()['room_id']);

        if ($room->id != $shift->room->id && !($room->attentionProfiles()->where('id', $this->validated()['attention_profile_id'])->exists())) {
            throw new \App\Exceptions\RoomNoContainAttentionProfileException();
        }

        $data = [
            'client_id' => $client->id,
            'room_id' => $this->validated()['room_id'],
            'attention_profile_id' => $this->validated()['attention_profile_id'],
            'state' => 'pending',
        ];
        // If module_id is not provided, don't update this field
        $data = array_merge($data, $this->module_id ? ['module_id' => $this->validated()['module_id']] : []);

        $shift->update($data);

        \Illuminate\Support\Facades\DB::commit();
        $shift->refresh();
        $client->refresh();
        return $shift;
    }
}
