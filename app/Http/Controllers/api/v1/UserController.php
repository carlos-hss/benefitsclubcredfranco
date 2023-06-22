<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    public function getAllUsers()
    {
        $users = User::select('id', 'name', 'email', 'type_user', 'points', 'status')->get();

        if (empty($users)) {
            return response()->json(['message' => 'No users registered'], 200);
        }
        return response()->json(['users' => $users], 200);
    }

    public function getAllClients()
    {
        $users = User::where('type_user', 'C')->select('id', 'name', 'email', 'type_user', 'points', 'status')->get();

        if (empty($users)) {
            return response()->json(['message' => 'No users registered'], 200);
        }
        
        return response()->json(['users' => $users], 200);
    }

    public function createUser(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|min:1',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'type_user' => 'required',
            'status' => 'required'
        ], [
            'name.required' => 'The name field is required.',
            'name.min' => 'The name field cannot be empty.',
            'email.required' => 'The email field is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already in use.',
            'password.required' => 'The password field is required.',
            'password.min' => 'The password must be at least 8 characters.',
            'type_user.required' => 'The type_user field is required.',
            'status.required' => 'The status field is required.',
        ]);

        if (empty($validatedData)) {
            return response()->json(['message' => 'Bad Request'], 400);
        }

        $validatedData['password'] = Hash::make($validatedData['password']);

        $user = User::create($validatedData);

        $user->makeHidden(['password']);

        $token = JWTAuth::fromUser($user);

        return response()->json(['token' => $token, 'user' => $user, 'message' => 'User successfully created!'], 201);
    }

    public function getUser($id)
    {
        $user = User::select('id', 'name', 'email', 'type_user', 'points', 'status')->find($id);

        if(!$user){
            response()->json(['message' => 'User not found'], 404);
        }

        return response()->json(['user' => $user], 200);
    }

    public function recoverUserInfo(Request $request)
    {
        $user = $request->user;

        if(!$user){
            response()->json(['message' => 'User not found'], 404);
        }

        $user->makeHidden('password');

        return response()->json(['user' => $user], 200);
    }

    public function updateUser(Request $request, string $id)
    {
        if ($id != $request->user->id) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validatedData = $request->validate([
            'name' => 'required|min:1',
            'email' => 'required|email|unique:users',
            'password' => 'nullable|min:8',
            'status' => 'nullable'
        ], [
            'name.required' => 'The name field is required.',
            'name.min' => 'The name field cannot be empty.',
            'email.required' => 'The email field is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already in use.',
        ]);

        $user = User::find($id);
        
        if(!$user){
            response()->json(['message' => 'User not found'], 404);
        }

        $user->update($validatedData);

        $user->makeHidden(['password']);

        $token = JWTAuth::fromUser($user);

        return response()->json(['user' => $user, 'token' => $token, 'message' => 'User successfully updated!'], 200);
    }

    public function addPoints(Request $request, string $id)
    {
        $user = User::find($id);

        $validatedData = $request->validate([
            'points' => 'required'
        ], [
            'points.required' => 'The points field is required.'
        ]);

        if(!$user){
            response()->json(['message' => 'User not found'], 404);
        }

        $user->points += $validatedData['points'];
        $user->save();

        return response()->json(['message' => 'Points added!'], 200);
    }

    public function deleteUser(string $id)
    {
        $user = User::find($id);

        if(!$user){
            response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'User successfully deleted!'], 200);
    }
}
