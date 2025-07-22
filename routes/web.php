<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SocialAuthController;

Route::get('/', function () {
    return view('welcome');
});

// Login page
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// Dashboard (placeholder)
Route::get('/dashboard', function () {
    return '<h1>Dashboard - Welcome ' . auth()->user()->name . '!</h1><a href="/logout">Logout</a>';
})->middleware('auth')->name('dashboard');

// Logout
Route::post('/logout', function () {
    auth()->logout();
    return redirect('/login');
})->name('logout');

// OAuth Social Authentication Routes
Route::prefix('auth')->group(function () {
    Route::get('/{provider}', [SocialAuthController::class, 'redirectToProvider'])
        ->where('provider', 'google|github|facebook|apple')
        ->name('auth.provider');

    Route::get('/{provider}/callback', [SocialAuthController::class, 'handleProviderCallback'])
        ->where('provider', 'google|github|facebook|apple')
        ->name('auth.callback');

    Route::delete('/unlink/{provider}', [SocialAuthController::class, 'unlinkProvider'])
        ->where('provider', 'google|github|facebook|apple')
        ->name('auth.unlink')
        ->middleware('auth');
});
