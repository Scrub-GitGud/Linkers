<?php

use App\Http\Controllers\AuthController;
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

Route::group(['prefix' => 'auth',], function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');
    Route::get('/user', [AuthController::class, 'getAuthUser'])->middleware('auth:api');
});

Route::group(['prefix' => 'admin', 'middleware' => ['auth:api']], function () {

    Route::post('/dashboard', [HomeController::class, 'dashboard']);

    Route::group(['prefix' => 'link'], function () {
        Route::get('/index', [LinkController::class, 'index']);
        Route::post('/add', [LinkController::class, 'add']);
        Route::post('/update', [LinkController::class, 'update']);
        Route::post('/delete', [LinkController::class, 'delete']);
        Route::post('/status-toggle', [LinkController::class, 'toggle']);
    });
});


Route::any('{path}', function() {
    return response()->json([
        'success' => false,
        'code' => 404,
        'message' => 'Url Not found, Check your URL first.',
        'data' => []
    ],404);
})->where('path', '.*');
