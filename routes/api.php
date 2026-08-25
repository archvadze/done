<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ArtworkApiController;
use App\Http\Controllers\Api\EvaluationApiController;
use App\Http\Controllers\Api\UserApiController;
use App\Http\Controllers\Api\LanguageController;

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

// Artwork API routes
Route::prefix('artworks')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [ArtworkApiController::class, 'index']);
    Route::post('/', [ArtworkApiController::class, 'store']);
    Route::get('/{artwork}', [ArtworkApiController::class, 'show']);
    Route::put('/{artwork}', [ArtworkApiController::class, 'update']);
    Route::delete('/{artwork}', [ArtworkApiController::class, 'destroy']);
    Route::post('/{artwork}/like', [ArtworkApiController::class, 'like']);
});

// Evaluation API routes
Route::prefix('evaluations')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [EvaluationApiController::class, 'index']);
    Route::post('/', [EvaluationApiController::class, 'store']);
    Route::get('/{evaluation}', [EvaluationApiController::class, 'show']);
    Route::put('/{evaluation}', [EvaluationApiController::class, 'update']);
    Route::delete('/{evaluation}', [EvaluationApiController::class, 'destroy']);
});

// User API routes
Route::prefix('users')->group(function () {
    Route::get('/', [UserApiController::class, 'index']);
    Route::get('/{user}', [UserApiController::class, 'show']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::put('/{user}', [UserApiController::class, 'update']);
        Route::delete('/{user}', [UserApiController::class, 'destroy']);
    });
});

// Language API routes
Route::prefix('languages')->middleware('auth')->group(function () {
    Route::get('/', [LanguageController::class, 'index']);
    Route::post('/', [LanguageController::class, 'store']);
    Route::put('/{language}', [LanguageController::class, 'update']);
    Route::delete('/{language}', [LanguageController::class, 'destroy']);
});

// Public API routes (no authentication required)
Route::get('/artworks/public', [ArtworkApiController::class, 'publicIndex']);
Route::get('/artworks/{artwork}/public', [ArtworkApiController::class, 'publicShow']);
