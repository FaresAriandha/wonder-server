<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\KredensialAdmin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterUserRequest;

class AuthController extends Controller
{
    //

    public function authenticate(RegisterUserRequest $registerUserRequest)
    {
        $credentials = $registerUserRequest->validated();
        if (!Auth::attempt($credentials)) {
            return response()->json([
                'status' => 404,
                'message' => 'Login failed',
            ], 404);
        }

        $token = $registerUserRequest->user()->createToken(Auth::user()->username)->plainTextToken;
        return response()->json([
            'status' => 200,
            'message' => 'User found',
            'token_id' => $token,
            'user' => [
                "id" => Auth::user()->id,
                "email" => Auth::user()->email,
                "username" => Auth::user()->username,
                "role" => Auth::user()->role,
            ]
        ], 200);
    }

    public function userStore(RegisterUserRequest $registerUserRequest)
    {
        $validated = $registerUserRequest->validated();
        User::create($validated);
        return response()->json([
            'status' => 200,
            'message' => 'Add User Success',
        ], 200);
    }

    public function revoke(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            "status" => 200,
            "message" => "Logout success!"
        ], 200);
    }

    public function show($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                "status" => 400,
                "messages" => "User not found"
            ], 400);
        }
        return response()->json([
            "status" => 200,
            "data" => $user,
        ], 200);
    }

    public function update($id, RegisterUserRequest $registerUserRequest)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                "status" => 400,
                "messages" => "User not found"
            ], 400);
        }

        $validated = $registerUserRequest->validated();

        if (!Hash::check($validated['old_password'], $user->password)) {
            return response()->json([
                "status" => 400,
                "messages" => "Password not found!",
            ], 400);
        }

        $updated = $user->update($validated);
        if (!$updated) {
            return response()->json([
                "status" => 400,
                "message" => "There is problem for updating process",
            ], 400);
        }

        return response()->json([
            "status" => 200,
            "message" => "Yes, update successfully!",
        ], 200);
    }
}
