<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientRequest extends FormRequest
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
            'name' => ['required', 'string'],
            'dni' => [
                'required', 'string', 'min:8', 'max:10',
                \Illuminate\Validation\Rule::unique('clients', 'dni')->ignore($this->client)
            ],
            'client_type_id' => ['required', 'integer', 'exists:client_types,id'],
        ];
    }

    public function createClient(): \App\Models\Client
    {
        return \App\Models\Client::create($this->validated());
    }

    public function updateClient(\App\Models\Client $client): void
    {
        $client->update($this->validated());
    }
}
