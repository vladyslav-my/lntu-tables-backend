<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Authenticate extends Middleware
{
    public function handle($request, Closure $next, ...$guards)
    {

        if (!Auth::guard($guards[0] ?? null)->check()) {
            return response()->json(['message' => 'Invalid token or token expired'], 401);
        }
        
        $this->authenticate($request, $guards);
        return $next($request);
    }
}
