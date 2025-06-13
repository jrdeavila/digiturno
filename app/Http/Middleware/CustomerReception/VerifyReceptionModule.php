<?php

namespace App\Http\Middleware\CustomerReception;

use App\Exceptions\CustomerReception\ModuleIsNotCustomerReceptionException;
use App\Exceptions\CustomerReception\UserDoesNotHaveModuleException;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class VerifyReceptionModule
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = User::find(Auth::id());
        if ($user->modules->count() == 0) {
            throw new UserDoesNotHaveModuleException();
        }
        $exists = $user->modules()->where('module_type_id', 3)->exists();
        if (!$exists) {
            throw new ModuleIsNotCustomerReceptionException();
        }
        return $next($request);
    }
}
