<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'type_allowed' => 'required'
        ]);
    
        $user = User::where('email', $credentials['email'])->first();
    
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        if ($credentials['type_allowed'] != $user->type_user) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        $user->makeHidden(['password']);
    
        $token = JWTAuth::fromUser($user, ['exp' => null]);
    
        return response()->json(['token' => $token, 'user' => $user, 'message' => 'Login successful'], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out']);
    }
}
