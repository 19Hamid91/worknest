<?php

use App\Http\Controllers\ProjectController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('user')->group(function () {
    Route::get('/all', [UserController::class, 'all']);
    Route::get('/{id}', [UserController::class, 'show']);
    Route::post('/create', [UserController::class, 'store']);
    Route::put('/update/{id}', [UserController::class, 'update']);
});

Route::prefix('project')->group(function () {
    Route::get('/all', [ProjectController::class, 'all']);
    // Route::get('/{id}', [UserController::class, 'show']);
    // Route::post('/create', [UserController::class, 'store']);
    // Route::put('/update/{id}', [UserController::class, 'update']);
});
