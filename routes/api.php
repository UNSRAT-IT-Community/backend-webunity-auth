<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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

Route::middleware('authorization')->group(function () {
    Route::get(
        '/test-api',
        function () {
            return response()->json([
                'status' => 200,
                'message' => 'Welcome to the API',
                'data' => null
            ]);
        }
    );
});

Route::post('/auth/refresh-token', [AuthController::class, 'refreshTokenLogin']);
Route::post('/auth/logout', [AuthController::class, 'logout']);
