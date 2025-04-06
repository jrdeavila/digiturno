<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServiceRequest extends FormRequest
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
                \Illuminate\Validation\Rule::unique('services', 'name')->ignore($this->service),
            ],
            'service_id' => 'sometimes|exists:services,id',
        ];
    }

    public function createService(): \App\Models\Service
    {
        return \App\Models\Service::create($this->validated());
    }

    public function updateService(\App\Models\Service $service): void
    {
        $service->update($this->validated());
    }
}
