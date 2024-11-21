<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

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
                'max:10',
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
            // 'attention_profile_id' => 'required|exists:attention_profiles,id',
            'module_id' => 'required|exists:modules,id',
            'services' => [
                'required',
                'array',
                'min:1',
            ],
            'state' => 'required|string|in:pending,completed,canceled,distracted,in_process',
            'qualification' => 'nullable|integer|between:0,4',
        ];
    }


    public function createShift()
    {

        DB::beginTransaction();

        $attentionProfile = \App\Models\AttentionProfile::whereDoesntHave('rooms')->first();

        \throw_unless($attentionProfile, \App\Exceptions\AttentionProfileNotFoundException::class);
        $client = \App\Models\Client::firstWhere('dni', $this->validated()['client']['dni']);
        if (!$client) {
            $client = \App\Models\Client::create($this->validated()['client']);
        } else {
            $client->client_type_id = $this->validated()['client']['client_type_id'];
            $client->name = $this->validated()['client']['name'];
            $client->save();
        }
        $data = [
            ...$this->validated(),
            'client_id' => $client->id,
            'attention_profile_id' => $attentionProfile->id,
            'module_id' => $this->validated()['module_id'],
        ];

        $shift = \App\Models\Shift::create($data);

        $shift->services()->attach($this->input('services'));

        $qualification = $this->getQualificationOption($this->input('qualification'));
        $shift->qualification()->create([
            'qualification' => $qualification->value,
        ]);

        $shift->update([
            'state' => "qualified",
        ]);

        DB::commit();


        return $shift;
    }


    private function getQualificationOption(int $qualification): \App\Enums\QualificationOption
    {
        switch ($qualification) {
            case 0:
                return \App\Enums\QualificationOption::NotQualified;
            case 1:
                return \App\Enums\QualificationOption::Bad;
            case 2:
                return \App\Enums\QualificationOption::Regular;
            case 3:
                return \App\Enums\QualificationOption::Good;
            case 4:
                return \App\Enums\QualificationOption::Excellent;
            default:
                return \App\Enums\QualificationOption::NotQualified;
        }
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
