<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ModuleTypeRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'use_qualification_module' => 'required|boolean',
        ];
    }

    public function createModuleType(): \App\Models\ModuleType
    {
        return \App\Models\ModuleType::create($this->validated());
    }

    public function updateModuleType(\App\Models\ModuleType $moduleType): \App\Models\ModuleType
    {
        $moduleType->update($this->validated());
        return $moduleType;
    }
}
