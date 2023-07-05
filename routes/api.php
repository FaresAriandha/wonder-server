<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardObjekWisata;
use App\Http\Controllers\KriteriaController;
use App\Http\Controllers\NilaiObjekWisataController;
use App\Http\Controllers\ObjekWisataController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::post('v1/login', [AuthController::class, 'authenticate']);
Route::post('v1/registration', [AuthController::class, 'userStore']);
Route::apiResource('v1/discover', ObjekWisataController::class)->only(['index', 'show']);

Route::middleware(['auth:sanctum'])->group(function () {
    // User relation
    Route::post('v1/logout', [AuthController::class, 'revoke']);
    Route::get('v1/profile/{user}', [AuthController::class, 'show']);
    Route::put('v1/profile/{user}', [AuthController::class, 'update']);
    Route::apiResource('v1/discover', ObjekWisataController::class)->only(['update']);
});

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    // Admin relation
    Route::apiResource('v1/admin', AdminController::class);

    // Wisata relation
    Route::apiResource('v1/discover-admin', DashboardObjekWisata::class);

    // Assesment relation
    Route::resource('v1/assesment', NilaiObjekWisataController::class);

    // Criteria relation
    Route::apiResource('v1/criteria', KriteriaController::class)->except(['destroy', 'store']);
});
