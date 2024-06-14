<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttentionProfileRequest extends FormRequest
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
                \Illuminate\Validation\Rule::unique('attention_profiles', 'name')->ignore($this->attention_profile),
            ],
            'attention_profile_id' => 'sometimes|exists:attention_profiles,id',
        ];
    }
}
