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
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\NftController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\CommunityPostController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\SupportTicketController;
use App\Http\Controllers\HelpArticleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ModerationController;
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

// OAuth routes
Route::prefix('auth')->group(function () {
    Route::get('/{provider}', [SocialAuthController::class, 'redirectToProvider'])->name('auth.redirect');
    Route::get('/{provider}/callback', [SocialAuthController::class, 'handleProviderCallback'])->name('auth.callback');
});

// Registration routes
Route::get('/register', [RegisterController::class, 'show'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.store');

// Artwork routes
Route::resource('artworks', ArtworkController::class);
Route::post('artworks/{artwork}/like', [ArtworkController::class, 'toggleLike'])->name('artworks.like');
Route::post('artworks/{artwork}/publish', [ArtworkController::class, 'publish'])->name('artworks.publish');
Route::post('artworks/{artwork}/unpublish', [ArtworkController::class, 'unpublish'])->name('artworks.unpublish');
Route::get('upload-progress', [ArtworkController::class, 'uploadProgress'])->name('artworks.upload-progress');

// Test route
Route::get('/test-edit', [\App\Http\Controllers\TestController::class, 'testEdit'])->name('test.edit');

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

// Dashboard route
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth')->name('dashboard');

// Logout
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/login');
})->name('logout');

// Handle GET requests to logout by redirecting to login
Route::get('/logout', function () {
    return redirect('/login')->with('message', 'Please use the logout button to sign out.');
});

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
        ->name('social.unlink')
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

// Payment routes
Route::middleware('auth')->prefix('payments')->name('payments.')->group(function () {
    Route::get('/', [PaymentController::class, 'show'])->name('show');
    Route::post('/checkout', [PaymentController::class, 'createCheckout'])->name('checkout');
    Route::get('/success', [PaymentController::class, 'success'])->name('success');
    Route::get('/history', [PaymentController::class, 'history'])->name('history');
});

// Stripe webhook (no auth needed)
Route::post('/webhook/stripe', [PaymentController::class, 'webhook'])->name('webhook.stripe');

// NFT routes
Route::middleware('auth')->prefix('nft')->name('nft.')->group(function () {
    Route::get('/mint/{artwork}', [NftController::class, 'mint'])->name('mint');
    Route::post('/mint/{artwork}', [NftController::class, 'processMint'])->name('process-mint');
    Route::get('/show/{nft}', [NftController::class, 'show'])->name('show');
    Route::get('/collection/{user?}', [NftController::class, 'collection'])->name('collection');
    Route::post('/connect-wallet', [NftController::class, 'connectWallet'])->name('connect-wallet');
    Route::post('/disconnect-wallet', [NftController::class, 'disconnectWallet'])->name('disconnect-wallet');
});

// NFT API routes (for AJAX calls)
Route::middleware('auth')->prefix('api/nft')->name('api.nft.')->group(function () {
    Route::get('/ownership/{artwork}', [NftController::class, 'ownership'])->name('ownership');
});

// Community routes
Route::prefix('communities')->name('communities.')->group(function () {
    Route::get('/', [CommunityController::class, 'index'])->name('index');
    Route::middleware('auth')->group(function () {
        Route::get('/create', [CommunityController::class, 'create'])->name('create');
        Route::post('/', [CommunityController::class, 'store'])->name('store');
    });

    Route::get('/{community}', [CommunityController::class, 'show'])->name('show');
    Route::get('/{community}/members', [CommunityController::class, 'members'])->name('members');

    Route::middleware('auth')->group(function () {
        Route::get('/{community}/edit', [CommunityController::class, 'edit'])->name('edit');
        Route::patch('/{community}', [CommunityController::class, 'update'])->name('update');
        Route::delete('/{community}', [CommunityController::class, 'destroy'])->name('destroy');
        Route::post('/{community}/join', [CommunityController::class, 'join'])->name('join');
        Route::post('/{community}/leave', [CommunityController::class, 'leave'])->name('leave');

        // Community posts
        Route::get('/{community}/posts/create', [CommunityPostController::class, 'create'])->name('posts.create');
        Route::post('/{community}/posts', [CommunityPostController::class, 'store'])->name('posts.store');
        Route::get('/{community}/posts/{post}', [CommunityPostController::class, 'show'])->name('posts.show');
        Route::get('/{community}/posts/{post}/edit', [CommunityPostController::class, 'edit'])->name('posts.edit');
        Route::patch('/{community}/posts/{post}', [CommunityPostController::class, 'update'])->name('posts.update');
        Route::delete('/{community}/posts/{post}', [CommunityPostController::class, 'destroy'])->name('posts.destroy');
        Route::post('/{community}/posts/{post}/pin', [CommunityPostController::class, 'togglePin'])->name('posts.pin');
        Route::post('/{community}/posts/{post}/lock', [CommunityPostController::class, 'toggleLock'])->name('posts.lock');
        Route::post('/{community}/posts/{post}/like', [CommunityPostController::class, 'like'])->name('posts.like');
    });
});

// Messaging routes
Route::middleware('auth')->prefix('messages')->name('messages.')->group(function () {
    Route::get('/', [MessageController::class, 'index'])->name('index');
    Route::get('/create', [MessageController::class, 'create'])->name('create');
    Route::post('/', [MessageController::class, 'store'])->name('store');
    Route::get('/{conversation}', [MessageController::class, 'show'])->name('show');
    Route::post('/{conversation}/send', [MessageController::class, 'sendMessage'])->name('send');
    Route::patch('/message/{message}', [MessageController::class, 'editMessage'])->name('message.edit');
    Route::delete('/message/{message}', [MessageController::class, 'deleteMessage'])->name('message.delete');
    Route::post('/{conversation}/leave', [MessageController::class, 'leave'])->name('leave');
    Route::get('/api/search-users', [MessageController::class, 'searchUsers'])->name('search-users');
});

// Support & Help routes
Route::prefix('support')->name('support.')->group(function () {
    // Main support pages
    Route::get('/', [SupportController::class, 'index'])->name('index');
    Route::get('/contact', [SupportController::class, 'contact'])->name('contact');
    Route::post('/contact', [SupportController::class, 'submitContact'])->name('contact.submit');
    Route::get('/search', [SupportController::class, 'search'])->name('search');

    // FAQ routes
    Route::prefix('faq')->name('faq.')->group(function () {
        Route::get('/', [FaqController::class, 'index'])->name('index');
        Route::get('/category/{category}', [FaqController::class, 'category'])->name('category');
        Route::get('/{faq}', [FaqController::class, 'show'])->name('show');
        Route::post('/{faq}/helpful', [FaqController::class, 'helpful'])->name('helpful');
        Route::post('/{faq}/not-helpful', [FaqController::class, 'notHelpful'])->name('not-helpful');
    });

    // Help Articles routes
    Route::prefix('help')->name('help.')->group(function () {
        Route::get('/', [HelpArticleController::class, 'index'])->name('index');
        Route::get('/{article}', [HelpArticleController::class, 'show'])->name('show');
        Route::post('/{article}/helpful', [HelpArticleController::class, 'helpful'])->name('helpful');
        Route::post('/{article}/not-helpful', [HelpArticleController::class, 'notHelpful'])->name('not-helpful');
    });

    // Support Tickets (authenticated users only)
    Route::middleware('auth')->prefix('tickets')->name('tickets.')->group(function () {
        Route::get('/', [SupportTicketController::class, 'index'])->name('index');
        Route::get('/create', [SupportTicketController::class, 'create'])->name('create');
        Route::post('/', [SupportTicketController::class, 'store'])->name('store');
        Route::get('/{ticket}', [SupportTicketController::class, 'show'])->name('show');
        Route::post('/{ticket}/reply', [SupportTicketController::class, 'reply'])->name('reply');
        Route::post('/{ticket}/close', [SupportTicketController::class, 'close'])->name('close');
        Route::post('/{ticket}/reopen', [SupportTicketController::class, 'reopen'])->name('reopen');
    });
});

// Moderation routes (for moderators and admins)
Route::middleware(['auth', 'verified'])->prefix('moderation')->name('moderation.')->group(function () {
    Route::get('/dashboard', [ModerationController::class, 'dashboard'])->name('dashboard');
    Route::get('/reports', [ModerationController::class, 'reports'])->name('reports.index');
    Route::get('/reports/{report}', [ModerationController::class, 'showReport'])->name('reports.show');
    Route::post('/reports/{report}/assign', [ModerationController::class, 'assignReport'])->name('reports.assign');
    Route::post('/reports/{report}/resolve', [ModerationController::class, 'resolveReport'])->name('reports.resolve');
    Route::get('/users/{user}', [ModerationController::class, 'showUser'])->name('users.show');
    Route::post('/users/{user}/actions', [ModerationController::class, 'takeAction'])->name('users.action');
    Route::get('/actions', [ModerationController::class, 'actions'])->name('actions.index');
    Route::get('/logs', [ModerationController::class, 'logs'])->name('logs.index');
    Route::get('/security/logs', [ModerationController::class, 'securityLogs'])->name('security.logs');
});
