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
            'services' => 'required|array|min:1'
        ];
    }

    public function createAttentionProfile(): \App\Models\AttentionProfile
    {
        \Illuminate\Support\Facades\DB::beginTransaction();
        $attentionProfile = \App\Models\AttentionProfile::create($this->validated());
        $attentionProfile->services()->attach($this->services);
        \Illuminate\Support\Facades\DB::commit();
        return $attentionProfile;
    }

    public function updateAttentionProfile(\App\Models\AttentionProfile $attentionProfile): void
    {
        \Illuminate\Support\Facades\DB::beginTransaction();
        $attentionProfile->update($this->validated());
        $attentionProfile->services()->sync($this->services);
        \Illuminate\Support\Facades\DB::commit();
    }
}
