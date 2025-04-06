<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ModuleRequest extends FormRequest
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
            'ip_address' => [
                'required',
                'ipv4',
                \Illuminate\Validation\Rule::unique('modules')->ignore($this->route('module')),
            ],
            'room_id' => 'required|exists:rooms,id',
            'client_type_id' => 'nullable|exists:client_types,id',
            'attention_profile_id' => 'nullable|exists:attention_profiles,id',
            'enabled' => 'sometimes|boolean',
            'module_type_id' => 'required|exists:module_types,id',

        ];
    }

    public function createModule(): \App\Models\Module
    {
        return \App\Models\Module::create($this->validated());
    }

    public function updateModule(\App\Models\Module $module): void
    {
        $module->update($this->validated());
    }
}
