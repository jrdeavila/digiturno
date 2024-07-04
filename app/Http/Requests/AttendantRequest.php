<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendantRequest extends FormRequest
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
            'name' => 'required|string|max:100',
            'email' => 'required|email',
            'dni' => 'required|string|max:10|unique:attendants,dni|regex:/^\d{8}$/',
            'attention_profile_id' => 'required|exists:attention_profiles,id',
        ];
    }

    public function createAttendant(): \App\Models\Attendant
    {
        return \App\Models\Attendant::create([
            ...$this->validated(),
            'password' => bcrypt($this->dni)
        ]);
    }

    public function updateAttendant(\App\Models\Attendant $attendant): void
    {
        $attendant->update($this->validated());
    }
}
