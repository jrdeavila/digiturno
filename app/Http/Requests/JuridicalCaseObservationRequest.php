<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JuridicalCaseObservationRequest extends FormRequest
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
            'content' => 'required|string',
            'attendant_id' => 'required|exists:attendants,id',
        ];
    }

    public function createObservation(\App\Models\JuridicalCase $case): \App\Models\JuridicalCaseObservation
    {
        return $case->observations()->create($this->validated());
    }
}
