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

// Leaderboard route
Route::get('/leaderboard', function () {
    return view('leaderboard.index');
})->name('leaderboard');

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
});

// User routes
Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
Route::get('/users/{user}/artworks', [UserController::class, 'artworks'])->name('users.artworks');
Route::get('/users/{user}/followers', [UserController::class, 'followers'])->name('users.followers');
Route::get('/users/{user}/following', [UserController::class, 'following'])->name('users.following');
Route::middleware('auth')->group(function () {
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::get('/profile', [UserController::class, 'profile'])->name('users.profile');
    Route::get('/profile/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update');
    Route::delete('/profile/avatar', [UserController::class, 'deleteAvatar'])->name('profile.avatar.delete');
    Route::get('/settings', [UserController::class, 'settings'])->name('settings');
    Route::put('/settings', [UserController::class, 'updateSettings'])->name('settings.update');
});

// Social authentication routes
Route::get('/auth/{provider}', [SocialAuthController::class, 'redirectToProvider'])->name('auth.provider');
Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'handleProviderCallback'])->name('social.callback');

// Two-factor authentication routes
Route::middleware('auth')->group(function () {
    Route::get('/2fa/setup', [TwoFactorController::class, 'setup'])->name('2fa.setup');
    Route::get('/2fa', [TwoFactorController::class, 'show'])->name('two-factor.show');
    Route::post('/2fa/enable', [TwoFactorController::class, 'enable'])->name('2fa.enable');
    Route::post('/2fa/disable', [TwoFactorController::class, 'disable'])->name('2fa.disable');
    Route::get('/2fa/verify', [TwoFactorController::class, 'verify'])->name('2fa.verify');
    Route::post('/2fa/verify', [TwoFactorController::class, 'validateToken'])->name('2fa.validate');
});

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

// Handle GET requests to logout by redirecting to login
Route::get('/logout', function () {
    return redirect('/login')->with('message', 'Please use the logout button to sign out.');
});

// Language management routes
Route::middleware('auth')->prefix('api')->group(function () {
    Route::get('/languages', [LanguageController::class, 'index'])->name('api.languages.index');
    Route::post('/languages', [LanguageController::class, 'store'])->name('api.languages.store');
    Route::put('/languages/{language}', [LanguageController::class, 'update'])->name('api.languages.update');
    Route::delete('/languages/{language}', [LanguageController::class, 'destroy'])->name('api.languages.destroy');
});

// Payment and NFT routes
Route::middleware('auth')->group(function () {
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/form', [PaymentController::class, 'show'])->name('payments.show');
    Route::get('/payments/history', [PaymentController::class, 'history'])->name('payments.history');
    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
    
    Route::get('/nfts', [NftController::class, 'index'])->name('nfts.index');
    Route::get('/nft/collection', [NftController::class, 'collection'])->name('nft.collection');
    Route::post('/nfts', [NftController::class, 'store'])->name('nfts.store');
    Route::get('/nfts/{nft}', [NftController::class, 'show'])->name('nfts.show');
});

// Community routes
Route::get('/community', [CommunityController::class, 'index'])->name('communities.index');
Route::get('/community/{community}', [CommunityController::class, 'show'])->name('communities.show');

// Test route for debugging community issues
Route::get('/test-community/{slug}', [App\Http\Controllers\TestCommunityController::class, 'test']);

// Redirect /communities to /community for backward compatibility
Route::get('/communities', function () {
    return redirect('/community');
});
Route::get('/community/posts/{post}', [CommunityPostController::class, 'show'])->name('community.posts.show');

Route::middleware('auth')->group(function () {
    Route::post('/community/posts', [CommunityPostController::class, 'store'])->name('community.posts.store');
    Route::put('/community/posts/{post}', [CommunityPostController::class, 'update'])->name('community.posts.update');
    Route::delete('/community/posts/{post}', [CommunityPostController::class, 'destroy'])->name('community.posts.destroy');
});

// Messaging routes
Route::middleware('auth')->group(function () {
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{conversation}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
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
    Route::patch('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
    Route::get('/users/{user}', [AdminController::class, 'showUser'])->name('users.show');
    Route::patch('/users/{user}/role', [AdminController::class, 'updateUserRole'])->name('users.role');
    Route::patch('/users/{user}/status', [AdminController::class, 'updateUserStatus'])->name('users.status');
    Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('users.delete');
    
    Route::get('/artworks/{artwork}', [AdminController::class, 'showArtwork'])->name('artworks.show');
    Route::patch('/artworks/{artwork}/status', [AdminController::class, 'updateArtworkStatus'])->name('artworks.status');
    Route::delete('/artworks/{artwork}', [AdminController::class, 'deleteArtwork'])->name('artworks.delete');
    
    Route::get('/evaluations/{evaluation}', [AdminController::class, 'showEvaluation'])->name('evaluations.show');
    Route::patch('/evaluations/{evaluation}/status', [AdminController::class, 'updateEvaluationStatus'])->name('evaluations.status');
    Route::delete('/evaluations/{evaluation}', [AdminController::class, 'deleteEvaluation'])->name('evaluations.delete');
    
    Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
    Route::get('/reports/{report}', [AdminController::class, 'showReport'])->name('reports.show');
    Route::patch('/reports/{report}/status', [AdminController::class, 'updateReportStatus'])->name('reports.status');
});

// Moderation routes (protected by moderator middleware)
Route::middleware('auth')->prefix('moderation')->name('moderation.')->group(function () {
    Route::get('/', [ModerationController::class, 'dashboard'])->name('dashboard');
});

// Support routes
Route::get('/support', [SupportController::class, 'index'])->name('support.index');
Route::get('/support/search', [SupportController::class, 'search'])->name('support.search');
Route::get('/support/contact', [SupportController::class, 'contact'])->name('support.contact');
Route::get('/support/faq', [FaqController::class, 'index'])->name('support.faq.index');
Route::get('/support/faq/{faq}', [FaqController::class, 'show'])->name('support.faq.show');
Route::post('/support/faq/{faq}/helpful', [FaqController::class, 'helpful'])->name('support.faq.helpful');
Route::post('/support/faq/{faq}/not-helpful', [FaqController::class, 'notHelpful'])->name('support.faq.not-helpful');
Route::get('/support/faq/category/{category}', [FaqController::class, 'category'])->name('support.faq.category');
Route::get('/support/help', [HelpArticleController::class, 'index'])->name('support.help.index');
Route::get('/support/help/{article}', [HelpArticleController::class, 'show'])->name('support.help.show');
Route::post('/support/help/{article}/helpful', [HelpArticleController::class, 'helpful'])->name('support.help.helpful');
Route::post('/support/help/{article}/not-helpful', [HelpArticleController::class, 'notHelpful'])->name('support.help.not-helpful');
Route::get('/support/articles', [HelpArticleController::class, 'index'])->name('support.articles');
Route::get('/support/articles/{article}', [HelpArticleController::class, 'show'])->name('support.articles.show');

Route::middleware('auth')->group(function () {
    Route::get('/support/tickets', [SupportTicketController::class, 'index'])->name('support.tickets.index');
    Route::get('/support/tickets/create', [SupportTicketController::class, 'create'])->name('support.tickets.create');
    Route::post('/support/tickets', [SupportTicketController::class, 'store'])->name('support.tickets.store');
    Route::get('/support/tickets/{ticket}', [SupportTicketController::class, 'show'])->name('support.tickets.show');
    Route::post('/support/tickets/{ticket}/reply', [SupportTicketController::class, 'reply'])->name('support.tickets.reply');
    Route::patch('/support/tickets/{ticket}/close', [SupportTicketController::class, 'close'])->name('support.tickets.close');
});

// FAQ management (admin only)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('faqs', FaqController::class)->except(['index', 'show']);
    Route::resource('help-articles', HelpArticleController::class)->except(['index', 'show']);
});

// Support ticket management (staff only)
Route::middleware(['auth', 'staff'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/tickets', [SupportTicketController::class, 'staffIndex'])->name('tickets.index');
    Route::get('/tickets/{ticket}', [SupportTicketController::class, 'staffShow'])->name('tickets.show');
    Route::post('/tickets/{ticket}/reply', [SupportTicketController::class, 'staffReply'])->name('tickets.reply');
    Route::patch('/tickets/{ticket}/assign', [SupportTicketController::class, 'assign'])->name('tickets.assign');
    Route::patch('/tickets/{ticket}/priority', [SupportTicketController::class, 'updatePriority'])->name('tickets.priority');
    Route::patch('/tickets/{ticket}/status', [SupportTicketController::class, 'updateStatus'])->name('tickets.status');
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
});

// Admin routes group with proper admin middleware
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::get('/artworks', [AdminController::class, 'artworks'])->name('admin.artworks');
    Route::get('/evaluations', [AdminController::class, 'evaluations'])->name('admin.evaluations');
    Route::get('/reports', [AdminController::class, 'reports'])->name('admin.reports');

    // User management
    Route::post('/users/{user}/ban', [ModerationController::class, 'banUser'])->name('users.ban');
    Route::post('/users/{user}/unban', [ModerationController::class, 'unbanUser'])->name('users.unban');
    Route::post('/users/{user}/actions', [ModerationController::class, 'takeAction'])->name('users.action');
    Route::get('/actions', [ModerationController::class, 'actions'])->name('actions.index');
    Route::get('/logs', [ModerationController::class, 'logs'])->name('logs.index');
    Route::get('/security/logs', [ModerationController::class, 'securityLogs'])->name('security.logs');
});

// Include test routes in development
if (app()->environment('local')) {
    include __DIR__ . '/test.php';
}
