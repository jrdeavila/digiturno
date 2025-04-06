<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyModuleIp
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $moduleIp = $request->header('X-Module-Ip');
        throw_unless($moduleIp, \App\Exceptions\ModuleIpNotProvidedException::class);
        $validator = \Illuminate\Support\Facades\Validator::make(['ip_address' => $moduleIp], ['ip_address' => 'ipv4']);
        throw_unless($validator->passes(), \App\Exceptions\InvalidModuleIpException::class);
        $module = \App\Models\Module::where('ip_address', $moduleIp)->first();
        throw_unless($module, \App\Exceptions\ModuleNotFoundException::class);
        throw_unless($module->enabled, \App\Exceptions\ModuleNotActiveException::class);
        $request->merge(['module' => $module]);
        return $next($request);
    }
}
