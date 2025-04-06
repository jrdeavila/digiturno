<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class VerifyAdminToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $token = $request->bearerToken();
            JWTAuth::setToken($token)->toUser();
            return $next($request);
        } catch (TokenInvalidException $e) {
            throw new \App\Exceptions\InvalidTokenException();
        } catch (TokenExpiredException $e) {
            throw new \App\Exceptions\ExpiredTokenException();
        }
    }
}
