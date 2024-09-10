<?php

namespace App\Http\Requests\Auth\Admin;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'email.exists' => 'email_not_found',
            'email.required' => 'email_required',
            'email.email' => 'email_invalid',
            'password.required' => 'password_required',
            'password.string' => 'password_invalid',
        ];
    }

    public function login(): string
    {
        $credentials = $this->only('email', 'password');
        $token = auth('admin')->attempt($credentials);
        throw_unless($token, \App\Exceptions\InvalidCredentialsException::class);
        return $token;
    }
}
