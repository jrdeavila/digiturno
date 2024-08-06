<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransferShiftRequest extends FormRequest
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
            'qualification' => 'required|integer|between:0,4',
            'attention_profile_id' => 'required|integer|exists:attention_profiles,id',
        ];
    }


    public function transferShift()
    {
        $shift = $this->route('shift');
        $shift->update([
            'state' => \App\Enums\ShiftState::Transferred,
        ]);

        $shift->qualification()->create([
            'qualification' => $this->getQualificationOption($this->qualification),
        ]);

        \App\Models\Shift::create([
            'client_id' => $shift->client_id,
            'attention_profile_id' => $this->attention_profile_id,
            'state' => \App\Enums\ShiftState::PendingTransferred,
            'room_id' => $shift->room_id,
        ]);


        \App\Jobs\ShiftTransferred::dispatch($shift);

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
}
