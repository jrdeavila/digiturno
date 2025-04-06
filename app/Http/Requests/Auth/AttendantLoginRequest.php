<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class AttendantLoginRequest extends FormRequest
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
            'email' => 'required|email|exists:attendants,email',
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
        $token = auth('attendant')->attempt($credentials);
        throw_unless($token, \App\Exceptions\InvalidCredentialsException::class);
        $attendant = auth('attendant')->user();
        throw_unless($attendant->enabled, \App\Exceptions\AttendantDisabledException::class);
        \App\Jobs\AttendantLogin::dispatch($attendant, $this->module);
        return $token;
    }
}
