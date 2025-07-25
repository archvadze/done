<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ArtworkController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Api\LanguageController;
use App\Models\User;

// Locale switching routes
Route::get('/locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');
Route::get('/api/locale/current', [LocaleController::class, 'current'])->name('locale.current');

Route::get('/', function () {
    return view('welcome');
});

// Authentication routes
Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Registration routes
Route::get('/register', [RegisterController::class, 'show'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.store');

// Artwork routes
Route::resource('artworks', ArtworkController::class);
Route::post('artworks/{artwork}/like', [ArtworkController::class, 'toggleLike'])->name('artworks.like');
Route::post('artworks/{artwork}/publish', [ArtworkController::class, 'publish'])->name('artworks.publish');
Route::post('artworks/{artwork}/unpublish', [ArtworkController::class, 'unpublish'])->name('artworks.unpublish');
Route::get('upload-progress', [ArtworkController::class, 'uploadProgress'])->name('artworks.upload-progress');

// Comments routes (protected by auth middleware)
Route::middleware('auth')->group(function () {
    Route::post('comments', [CommentController::class, 'store'])->name('comments.store');
    Route::put('comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
});

// Public comments routes
Route::get('artworks/{artwork}/comments', [CommentController::class, 'getComments'])->name('comments.get');

// Evaluation routes
Route::middleware('auth')->group(function () {
    Route::get('artworks/{artwork}/evaluations/create', [EvaluationController::class, 'create'])->name('evaluations.create');
    Route::post('artworks/{artwork}/evaluations', [EvaluationController::class, 'store'])->name('evaluations.store');
    Route::get('artworks/{artwork}/evaluations', [EvaluationController::class, 'index'])->name('evaluations.index');
    Route::get('artworks/{artwork}/evaluations/{evaluation}/edit', [EvaluationController::class, 'edit'])->name('evaluations.edit');
    Route::put('artworks/{artwork}/evaluations/{evaluation}', [EvaluationController::class, 'update'])->name('evaluations.update');
    Route::delete('artworks/{artwork}/evaluations/{evaluation}', [EvaluationController::class, 'destroy'])->name('evaluations.destroy');
    Route::get('artworks/{artwork}/evaluations/{evaluation}', [EvaluationController::class, 'show'])->name('evaluations.show');
});

// Public evaluation routes
Route::get('leaderboard', [EvaluationController::class, 'leaderboard'])->name('leaderboard');

// User profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [UserController::class, 'profile'])->name('users.profile');
    Route::get('/profile/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/profile', [UserController::class, 'update'])->name('users.update');
});

// Public user profiles
Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
Route::get('/users/{user}/artworks', [UserController::class, 'artworks'])->name('users.artworks');
Route::get('/users/{user}/followers', [UserController::class, 'followers'])->name('users.followers');
Route::get('/users/{user}/following', [UserController::class, 'following'])->name('users.following');

// Follow/Unfollow (requires authentication)
Route::middleware('auth')->group(function () {
    Route::post('/users/{user}/follow', [UserController::class, 'toggleFollow'])->name('users.follow');
});

// Load development routes only in local environment
if (app()->environment('local')) {
    require __DIR__ . '/dev.php';
}

// Dashboard - redirect admins to admin panel, others to user dashboard
Route::get('/dashboard', function () {
    $user = Auth::user();

    // Redirect admins to admin panel
    if ($user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }

    // For regular users, show simple dashboard
    $twoFaStatus = $user->twofa_enabled ? 'enabled' : 'disabled';
    return "
    <div style='font-family: Arial, sans-serif; max-width: 800px; margin: 40px auto; padding: 20px;'>
        <h1>Dashboard - Welcome {$user->name}!</h1>
        <div style='background: #f8f9fa; border-radius: 8px; padding: 20px; margin: 20px 0;'>
            <h3>Account Security</h3>
            <p>Two-Factor Authentication: <strong style='color: " . ($user->twofa_enabled ? 'green' : 'orange') . ";'>" . ucfirst($twoFaStatus) . "</strong></p>
            <a href='/2fa' style='color: #007bff; text-decoration: none;'>→ Manage 2FA Settings</a>
        </div>
        <div style='margin-top: 30px;'>
            <a href='/logout' style='color: #dc3545; text-decoration: none;'>Logout</a>
        </div>
    </div>";
})->middleware('auth')->name('dashboard');

// Logout
Route::post('/logout', function () {
    Auth::logout();
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

// Two-Factor Authentication Routes
Route::middleware('auth')->prefix('2fa')->group(function () {
    Route::get('/', [TwoFactorController::class, 'show'])->name('two-factor.show');
    Route::post('/enable', [TwoFactorController::class, 'enable'])->name('two-factor.enable');
    Route::post('/disable', [TwoFactorController::class, 'disable'])->name('two-factor.disable');
    Route::get('/backup-codes', [TwoFactorController::class, 'generateBackupCodes'])->name('two-factor.backup-codes');
});

// Two-Factor Verification (for login)
Route::middleware('guest')->group(function () {
    Route::get('/2fa/verify', [TwoFactorController::class, 'verify'])->name('two-factor.verify');
    Route::post('/2fa/verify', [TwoFactorController::class, 'verifyCode'])->name('two-factor.verify.post');
});

// Admin routes (protected by admin middleware)
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/artworks', [AdminController::class, 'artworks'])->name('artworks');
    Route::get('/evaluations', [AdminController::class, 'evaluations'])->name('evaluations');
    Route::get('/languages', [AdminController::class, 'languages'])->name('languages');
    Route::patch('/languages/{language}/status', [AdminController::class, 'updateLanguageStatus'])->name('languages.status');
    Route::patch('/languages/{language}/default', [AdminController::class, 'setDefaultLanguage'])->name('languages.default');
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::get('/logs', [AdminController::class, 'logs'])->name('logs');
});
