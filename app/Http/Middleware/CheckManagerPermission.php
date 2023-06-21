<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;

class CheckManagerPermission
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $user = $request->user;

            if ($user && $user->type_user === 'M') {
                return $next($request);
            }
    
            return throw new AuthenticationException();
        } catch (AuthenticationException $e) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    }
}
