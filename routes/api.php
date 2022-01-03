<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\TagController;
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

Route::group(['middleware' => ['auth:api']], function () {

    Route::group(['prefix' => 'link'], function () {
        Route::get('/search', [LinkController::class, 'searchAll']);
        Route::get('/index', [LinkController::class, 'index']);
        Route::post('/add', [LinkController::class, 'add']);
        Route::post('/update', [LinkController::class, 'update']);
        Route::post('/delete', [LinkController::class, 'delete']);
        Route::post('/status-toggle', [LinkController::class, 'toggle']);
        Route::post('/click', [LinkController::class, 'click']);
        Route::post('/vote', [LinkController::class, 'vote']);
    });
    
    Route::group(['prefix' => 'folder'], function () {
        Route::get('/index', [FolderController::class, 'index']);
        Route::post('/add', [FolderController::class, 'add']);
        Route::get('/link-folders', [FolderController::class, 'linkFolders']);
        Route::get('/folder-links', [FolderController::class, 'folderLinks']);
        Route::post('/links/add', [FolderController::class, 'addLinkFolders']);
    });

    Route::group(['prefix' => 'tag'], function () {
        Route::get('/index', [TagController::class, 'index']);
        Route::post('/add', [TagController::class, 'add']);
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
