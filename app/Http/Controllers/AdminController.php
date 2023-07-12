<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\KredensialAdmin;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\StoreDataAdminRequest;

class AdminController extends Controller {
    /**
     * Display a listing of the resource.
     */

    public function index() {
        //
        $data = KredensialAdmin::all();

        if (!$data) {
            return response()->json([
                "status" => 200,
                "data" => "There is no data"
            ], 200);
        }
        return response()->json([
            "status" => 200,
            "data" => $data
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RegisterUserRequest $registerUserRequest, StoreDataAdminRequest $storeDataAdminRequest) {
        //Add Admin Account
        $validatedUser = $registerUserRequest->validated();
        // Add Credential Admin
        $validatedCredentials = $storeDataAdminRequest->validated();

        // Storing data
        $validatedUser['foto'] = Storage::disk('user_photo')->put('', $validatedUser['foto']);
        $userCreated = User::create($validatedUser);
        $validatedCredentials['id_user'] = $userCreated->id;


        KredensialAdmin::create($validatedCredentials);
        return response()->json([
            'status' => 200,
            'message' => 'Akun berhasil ditambahkan!',
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($id) {
        $credentials_user = KredensialAdmin::where('id', $id)->first();
        if (!$credentials_user) {
            return response()->json([
                "message" => "User Admin tidak ditemukan!",
            ], 404);
        }
        $credentials_user['user'] = $credentials_user->user;
        unset($credentials_user['id_user']);
        return response()->json([
            "message" => "Credentials user",
            "credentials" => $credentials_user,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id, RegisterUserRequest $registerUserRequest, StoreDataAdminRequest $storeDataAdminRequest) {
        $validatedUser = $registerUserRequest->validated();
        $validated = $storeDataAdminRequest->validated();
        $admin = KredensialAdmin::where('id', $id)->first();
        if (!$admin) {
            return response()->json([
                "status" => 400,
                "message" => "User admin tidak ditemukan!"
            ], 400);
        }

        $user = $admin->user;
        if (isset($validatedUser['foto'])) {
            if ($user['foto']) {
                Storage::disk('user_photo')->delete($user['foto']);
            }
            $validatedUser['foto'] = Storage::disk('user_photo')->put('', $validatedUser['foto']);
        }

        $user->update($validatedUser);
        $admin->update($validated);
        return response()->json([
            "status" => 200,
            "message" => $validated['nama_lengkap'] . " berhasil diubah datanya!"
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) {
        $admin = KredensialAdmin::where('id', $id)->first();
        if (!$admin) {
            return response()->json([
                "status" => 400,
                "message" => "User admin tidak ditemukan!"
            ], 400);
        }
        if ($admin['foto']) {
            Storage::disk('user_photo')->delete($admin['foto']);
        }

        $deleted = User::where('id', $admin->id_user)->delete();
        if (!$deleted) {
            return response()->json([
                "status" => 400,
                "message" => "Terdapat masalah!"
            ], 400);
        }

        return response()->json([
            "status" => 200,
            "message" => "Akun " . $admin->nama_lengkap . " berhasil dihapus!"
        ], 200);
    }
}
