<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ArtworkApiController;
use App\Http\Controllers\Api\EvaluationApiController;
use App\Http\Controllers\Api\UserApiController;

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

// Public API routes
Route::prefix('v1')->group(function () {
    // Artworks API
    Route::get('artworks', [ArtworkApiController::class, 'index']);
    Route::get('artworks/{artwork}', [ArtworkApiController::class, 'show']);
    Route::get('artworks/{artwork}/evaluations', [EvaluationApiController::class, 'index']);
    Route::get('leaderboard', [EvaluationApiController::class, 'leaderboard']);

    // User endpoints (public)
    Route::get('users/{user}/artworks', [UserApiController::class, 'artworks']);

    // Protected API routes (require authentication)
    Route::middleware('auth:sanctum')->group(function () {
        // User profile
        Route::get('me', [UserApiController::class, 'me']);
        Route::put('me', [UserApiController::class, 'updateProfile']);

        // Artwork management
        Route::post('artworks', [ArtworkApiController::class, 'store']);
        Route::put('artworks/{artwork}', [ArtworkApiController::class, 'update']);
        Route::delete('artworks/{artwork}', [ArtworkApiController::class, 'destroy']);
        Route::post('artworks/{artwork}/like', [ArtworkApiController::class, 'toggleLike']);
        Route::post('artworks/{artwork}/publish', [ArtworkApiController::class, 'publish']);
        Route::post('artworks/{artwork}/unpublish', [ArtworkApiController::class, 'unpublish']);

        // Evaluations
        Route::post('artworks/{artwork}/evaluations', [EvaluationApiController::class, 'store']);
        Route::put('evaluations/{evaluation}', [EvaluationApiController::class, 'update']);
        Route::delete('evaluations/{evaluation}', [EvaluationApiController::class, 'destroy']);
        Route::get('evaluations/{evaluation}', [EvaluationApiController::class, 'show']);
    });
});
