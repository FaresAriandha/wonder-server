<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\KredensialAdmin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\RegisterUserRequest;

class AuthController extends Controller {
    //

    public function authenticate(RegisterUserRequest $registerUserRequest) {
        $credentials = $registerUserRequest->validated();
        if (!Auth::attempt($credentials)) {
            return response()->json([
                'status' => 404,
                'message' => 'Login gagal!',
            ], 404);
        }

        $token = $registerUserRequest->user()->createToken(Auth::user()->username)->plainTextToken;
        return response()->json([
            'status' => 200,
            'message' => 'User ditemukan!',
            'token_id' => $token,
            'user' => [
                "id" => Auth::user()->id,
                "email" => Auth::user()->email,
                "username" => Auth::user()->username,
                "role" => Auth::user()->role,
                "foto" => Auth::user()->foto,
            ]
        ], 200);
    }

    public function userStore(RegisterUserRequest $registerUserRequest) {
        $validated = $registerUserRequest->validated();
        User::create($validated);
        return response()->json([
            'status' => 200,
            'message' => 'Berhasil registrasi!',
        ], 200);
    }

    public function revoke(Request $request) {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            "status" => 200,
            "message" => "Berhasil logout!"
        ], 200);
    }

    public function show($id) {
        $user = User::find($id);
        $user['like'] = $user->like;
        $user['comments'] = $user->comments;
        if (!$user) {
            return response()->json([
                "status" => 400,
                "messages" => "User tidak ditemukan!"
            ], 400);
        }
        return response()->json([
            "status" => 200,
            "data" => $user,
        ], 200);
    }

    public function update($id, RegisterUserRequest $registerUserRequest) {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                "status" => 400,
                "messages" => "User tidak ditemukan!"
            ], 400);
        }

        $validated = $registerUserRequest->validated();

        if (isset($validated['old_password']) && isset($validated['password'])) {
            if (!Hash::check($validated['old_password'], $user->password)) {
                return response()->json([
                    "status" => 400,
                    "messages" => "Password tidak ditemukan!",
                ], 400);
            }
        }

        if (isset($validated['foto'])) {
            if ($user['foto']) {
                Storage::disk('user_photo')->delete($user['foto']);
            }
            $validated['foto'] = Storage::disk('user_photo')->put('', $validated['foto']);
        }

        $updated = $user->update($validated);
        if (!$updated) {
            return response()->json([
                "status" => 400,
                "message" => "Ada masalah saat mengubah data",
            ], 400);
        }

        return response()->json([
            "status" => 200,
            "message" => "Profil berhasil diubah!",
        ], 200);
    }
}
