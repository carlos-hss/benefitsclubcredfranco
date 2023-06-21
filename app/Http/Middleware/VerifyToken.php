<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class VerifyToken
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $token = $request->bearerToken();
    
            if (!$token) {
                throw new AuthenticationException();
            }
    
            JWTAuth::setToken($token);
            $user = JWTAuth::toUser();
    
            if (!$user) {
                throw new AuthenticationException();
            }

            $request->merge(['user' => $user]);
    
            return $next($request);
        } catch (AuthenticationException $e) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    }
}
