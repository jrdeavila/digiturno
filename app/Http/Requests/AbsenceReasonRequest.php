<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AbsenceReasonRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::unique('absence_reasons', 'name')->ignore($this->route('absence_reason')),
            ],
        ];
    }

    public function createAbsenceReason(): \App\Models\AbsenceReason
    {
        return \App\Models\AbsenceReason::create($this->only('name'));
    }

    public function updateAbsenceReason(\App\Models\AbsenceReason $absenceReason): \App\Models\AbsenceReason
    {
        $absenceReason->update($this->only('name'));
        return $absenceReason;
    }
}
