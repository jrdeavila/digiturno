<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JuridicalCaseRequest extends FormRequest
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
            'subject' => 'required|string',
            'client_id' => 'required|exists:clients,id',
        ];
    }

    public function createJuridicalCase(\App\Models\Attendant $attendant): \App\Models\JuridicalCase
    {
        $data = $this->validated();
        $data['attendant_id'] = $attendant->id;
        return $attendant->juridicalCases()->create($data);
    }

    public function updateJuridicalCase(\App\Models\JuridicalCase $case): \App\Models\JuridicalCase
    {
        $data = $this->validated();
        $case->update($data);
        return $case;
    }
}
