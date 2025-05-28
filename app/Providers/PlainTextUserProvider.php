<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;

class PlainTextUserProvider implements UserProvider
{
    public function retrieveById($identifier)
    {
        return User::find($identifier);
    }

    public function retrieveByCredentials(array $credentials)
    {
        return User::whereHas('employee', function ($query) use ($credentials) {
            $query->where('noDocumento', $credentials['password']);
        })->first();
    }

    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        return $user->getAuthPassword() === $credentials['password'];
    }

    public function updateRememberToken(Authenticatable $user, $token) {}
    public function retrieveByToken($identifier, $token) {}

    public function rehashPasswordIfRequired(Authenticatable $user, array $credentials, bool $force = false)
    {
        return false;
    }
}
