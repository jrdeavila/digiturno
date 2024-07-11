<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShiftRequest extends FormRequest
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
                'min:8',
                'max:10',
                'regex:/^[0-9]+$/',
                function ($attribute, $value, $fail) {
                    $client = \App\Models\Client::firstWhere('dni', $value);
                    if ($client && $client->shifts()->where('state', 'pending')->exists()) {
                        $fail('client_has_pending_shift');
                    }
                },
            ],
            'client.client_type_id' => 'required|exists:client_types,id',
            'attention_profile_id' => 'required|exists:attention_profiles,id',
            'state' => 'required|string|in:pending,completed,canceled,distracted,in_process',

        ];
    }


    public function createShift()
    {
        \Illuminate\Support\Facades\DB::beginTransaction();
        $client = \App\Models\Client::firstWhere('dni', $this->validated()['client']['dni']);
        if (!$client) {
            $client = \App\Models\Client::create($this->validated()['client']);
        }
        $data = [
            ...$this->validated(),
            'client_id' => $client->id
        ];

        $shift = \App\Models\Shift::create($data);
        \Illuminate\Support\Facades\DB::commit();

        return $shift;
    }

    public function updateShift(\App\Models\Shift $shift)
    {
        \Illuminate\Support\Facades\DB::beginTransaction();
        $client = \App\Models\Client::findOrCreate($this->validated()['client']);
        $data = [
            ...$this->validated(),
            'client_id' => $client->id
        ];
        $shift->update($data);
        \Illuminate\Support\Facades\DB::commit();

        return $shift;
    }
}
