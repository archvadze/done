<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
use PragmaRX\Google2FA\Google2FA;

/*
|--------------------------------------------------------------------------
| Development Test Routes
|--------------------------------------------------------------------------
*/

if (app()->environment('local')) {
    // Test route to exactly replicate ENTIRE controller method
    Route::get('/test-entire-method/{community:slug}', function (\App\Models\Community $community, Illuminate\Http\Request $request) {
        try {
            $user = \Illuminate\Support\Facades\Auth::user();
        
            // Check if user can view this community
            if ($community->privacy === 'private' && (!$user || !$community->isMember($user))) {
                abort(403, 'This community is private.');
            }

            if ($community->privacy === 'hidden' && (!$user || !$community->canModerate($user))) {
                abort(404);
            }

            // Load community data
            $community->load(['creator', 'activeMembers' => function ($query) {
                $query->latest('community_members.joined_at')->limit(12);
            }]);

            // Get posts with filters
            $postsQuery = $community->posts()
                ->with(['user', 'comments'])
                ->where('is_locked', false);

            // Filter by post type
            if ($request->filled('type') && $request->type !== 'all') {
                $postsQuery->where('type', $request->type);
            }

            // Sort posts
            $sort = $request->get('sort', 'recent');
            switch ($sort) {
                case 'popular':
                    $postsQuery->orderBy('like_count', 'desc');
                    break;
                case 'discussed':
                    $postsQuery->orderBy('comment_count', 'desc');
                    break;
                case 'recent':
                default:
                    $postsQuery->orderBy('is_pinned', 'desc')
                              ->orderBy('created_at', 'desc');
                    break;
            }

            $posts = $postsQuery->paginate(10);

            // Get pinned posts separately for display at top
            $pinnedPosts = $community->pinnedPosts()->with(['user', 'comments'])->get();

            return view('communities.show', compact('community', 'posts', 'pinnedPosts'));
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => basename($e->getFile()),
                'trace' => array_slice(explode("\n", $e->getTraceAsString()), 0, 10)
            ], 500);
        }
    });

    // Test route to debug posts query step
    Route::get('/test-posts-step/{community:slug}', function (\App\Models\Community $community, Illuminate\Http\Request $request) {
        try {
            $user = \Illuminate\Support\Facades\Auth::user();
            
            // Privacy checks
            if ($community->privacy === 'private' && (!$user || !$community->isMember($user))) {
                abort(403, 'This community is private.');
            }
            if ($community->privacy === 'hidden' && (!$user || !$community->canModerate($user))) {
                abort(404);
            }

            // Load community data
            $community->load(['creator', 'activeMembers' => function ($query) {
                $query->latest('community_members.joined_at')->limit(12);
            }]);

            // Get posts with filters - EXACTLY like the controller
            $postsQuery = $community->posts()
                ->with(['user', 'comments'])
                ->where('is_locked', false);

            // Sort posts
            $sort = $request->get('sort', 'recent');
            switch ($sort) {
                case 'popular':
                    $postsQuery->orderBy('like_count', 'desc');
                    break;
                case 'discussed':
                    $postsQuery->orderBy('comment_count', 'desc');
                    break;
                case 'recent':
                default:
                    $postsQuery->orderBy('is_pinned', 'desc')
                              ->orderBy('created_at', 'desc');
                    break;
            }

            $posts = $postsQuery->paginate(10);

            // Get pinned posts separately for display at top
            $pinnedPosts = $community->pinnedPosts()->with(['user', 'comments'])->get();

            return response()->json([
                'success' => true,
                'message' => 'Posts loaded successfully',
                'posts_count' => $posts->count(),
                'pinned_posts_count' => $pinnedPosts->count()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => basename($e->getFile()),
                'trace' => array_slice(explode("\n", $e->getTraceAsString()), 0, 8)
            ], 500);
        }
    });

    // Test route to debug load step by step
    Route::get('/test-load-step/{community:slug}', function (\App\Models\Community $community, Illuminate\Http\Request $request) {
        try {
            $user = \Illuminate\Support\Facades\Auth::user();
            
            // Check if user can view this community
            if ($community->privacy === 'private' && (!$user || !$community->isMember($user))) {
                abort(403, 'This community is private.');
            }

            if ($community->privacy === 'hidden' && (!$user || !$community->canModerate($user))) {
                abort(404);
            }

            // Step 1: Try to load community data - EXACTLY like the controller
            $community->load(['creator', 'activeMembers' => function ($query) {
                $query->latest('community_members.joined_at')->limit(12);
            }]);

            return response()->json([
                'success' => true,
                'message' => 'Community loaded successfully',
                'community_id' => $community->id,
                'creator_name' => $community->creator ? $community->creator->name : 'No creator',
                'members_count' => $community->activeMembers->count()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => basename($e->getFile()),
                'trace' => array_slice(explode("\n", $e->getTraceAsString()), 0, 5)
            ], 500);
        }
    });

    // Test route to debug exact controller replication
    Route::get('/test-exact-controller/{community:slug}', function (\App\Models\Community $community, Illuminate\Http\Request $request) {
        try {
            $user = \Illuminate\Support\Facades\Auth::user();
            
            // Check if user can view this community
            if ($community->privacy === 'private' && (!$user || !$community->isMember($user))) {
                abort(403, 'This community is private.');
            }

            if ($community->privacy === 'hidden' && (!$user || !$community->canModerate($user))) {
                abort(404);
            }

            return response()->json([
                'success' => true,
                'message' => 'All privacy checks passed',
                'community_id' => $community->id,
                'community_name' => $community->name,
                'privacy' => $community->privacy
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => basename($e->getFile()),
                'trace' => array_slice(explode("\n", $e->getTraceAsString()), 0, 5)
            ], 500);
        }
    });

    // Test route to capture exact view rendering error
    Route::get('/test-view-render/{slug}', function ($slug) {
        try {
            $community = \App\Models\Community::where('slug', $slug)->first();
            
            if (!$community) {
                abort(404);
            }

            // Load community data - EXACTLY like the controller
            $community->load(['creator', 'activeMembers' => function ($query) {
                $query->latest('community_members.joined_at')->limit(12);
            }]);

            // Get posts with filters - EXACTLY like the controller
            $postsQuery = $community->posts()
                ->with(['user', 'comments'])
                ->where('is_locked', false);

            $postsQuery->orderBy('is_pinned', 'desc')
                      ->orderBy('created_at', 'desc');

            $posts = $postsQuery->paginate(10);

            // Get pinned posts separately for display at top - EXACTLY like the controller
            $pinnedPosts = $community->pinnedPosts()->with(['user', 'comments'])->get();

            // Now try to render the view
            return view('communities.show', compact('community', 'posts', 'pinnedPosts'));
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => array_slice(explode("\n", $e->getTraceAsString()), 0, 10)
            ], 500);
        }
    });

    // Test route to exactly replicate controller logic  
    Route::get('/test-full-community/{slug}', function ($slug) {
        try {
            $community = \App\Models\Community::where('slug', $slug)->first();
            
            if (!$community) {
                abort(404);
            }

            // Check if user can view this community
            if ($community->privacy === 'private' && !$community->isMember(\Illuminate\Support\Facades\Auth::user() ?? new \App\Models\User())) {
                abort(403, 'This community is private.');
            }

            if ($community->privacy === 'hidden' && !$community->canModerate(\Illuminate\Support\Facades\Auth::user() ?? new \App\Models\User())) {
                abort(404);
            }

            // Load community data - EXACTLY like the controller
            $community->load(['creator', 'activeMembers' => function ($query) {
                $query->latest('community_members.joined_at')->limit(12);
            }]);

            // Get posts with filters - EXACTLY like the controller
            $postsQuery = $community->posts()
                ->with(['user', 'comments'])
                ->where('is_locked', false);

            $postsQuery->orderBy('is_pinned', 'desc')
                      ->orderBy('created_at', 'desc');

            $posts = $postsQuery->paginate(10);

            // Get pinned posts separately for display at top - EXACTLY like the controller
            $pinnedPosts = $community->pinnedPosts()->with(['user', 'comments'])->get();

            return response()->json([
                'success' => true,
                'message' => 'All controller logic passed successfully',
                'community_id' => $community->id,
                'community_name' => $community->name,
                'posts_count' => $posts->count(),
                'pinned_posts_count' => $pinnedPosts->count()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    });

    // Test route to debug posts relationship  
    Route::get('/test-community-posts/{slug}', function ($slug) {
        try {
            $community = \App\Models\Community::where('slug', $slug)->first();
            
            if (!$community) {
                return response()->json(['error' => 'Community not found'], 404);
            }

            // Get posts with filters - mimicking the controller logic
            $postsQuery = $community->posts()
                ->with(['user', 'comments'])
                ->where('is_locked', false);

            $postsQuery->orderBy('is_pinned', 'desc')
                      ->orderBy('created_at', 'desc');

            $posts = $postsQuery->paginate(10);

            // Get pinned posts separately
            $pinnedPosts = $community->pinnedPosts()->with(['user', 'comments'])->get();
            
            return response()->json([
                'success' => true,
                'community_id' => $community->id,
                'posts_count' => $posts->count(),
                'pinned_posts_count' => $pinnedPosts->count(),
                'posts' => $posts->items(),
                'pinned_posts' => $pinnedPosts
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
    });

    // Test route to debug activeMembers relationship  
    Route::get('/test-community-members/{slug}', function ($slug) {
        try {
            $community = \App\Models\Community::where('slug', $slug)
                ->with(['creator', 'activeMembers' => function ($query) {
                    $query->latest('community_members.joined_at')->limit(12);
                }])
                ->first();
            
            if (!$community) {
                return response()->json(['error' => 'Community not found'], 404);
            }
            
            return response()->json([
                'success' => true,
                'community' => [
                    'id' => $community->id,
                    'name' => $community->name,
                    'slug' => $community->slug,
                    'creator_id' => $community->creator_id,
                    'privacy' => $community->privacy,
                    'creator' => $community->creator ? [
                        'id' => $community->creator->id,
                        'name' => $community->creator->name
                    ] : null,
                    'active_members_count' => $community->activeMembers->count(),
                    'active_members' => $community->activeMembers->map(function($member) {
                        return [
                            'id' => $member->id,
                            'name' => $member->name,
                            'role' => $member->pivot->role ?? 'member'
                        ];
                    })
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
    });

    // Test route to debug creator relationship
    Route::get('/test-community-creator/{slug}', function ($slug) {
        try {
            $community = \App\Models\Community::where('slug', $slug)->with('creator')->first();
            
            if (!$community) {
                return response()->json(['error' => 'Community not found'], 404);
            }
            
            return response()->json([
                'success' => true,
                'community' => [
                    'id' => $community->id,
                    'name' => $community->name,
                    'slug' => $community->slug,
                    'creator_id' => $community->creator_id,
                    'privacy' => $community->privacy,
                    'creator' => $community->creator ? [
                        'id' => $community->creator->id,
                        'name' => $community->creator->name,
                        'email' => $community->creator->email
                    ] : null
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
    });

    // Simple test route to debug community loading
    Route::get('/simple-community-test/{slug}', function ($slug) {
        try {
            $community = \App\Models\Community::where('slug', $slug)->first();
            
            if (!$community) {
                return response()->json(['error' => 'Community not found'], 404);
            }
            
            return response()->json([
                'success' => true,
                'community' => [
                    'id' => $community->id,
                    'name' => $community->name,
                    'slug' => $community->slug,
                    'creator_id' => $community->creator_id,
                    'privacy' => $community->privacy
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
    });

    Route::get('/dev-2fa-code/{email}', function ($email) {
        $user = User::where('email', $email)->first();

        if (!$user) {
            return "User not found";
        }

        $google2fa = new Google2FA();

        // Generate secret if user doesn't have one
        if (!$user->twofa_secret) {
            $secret = $google2fa->generateSecretKey();
            $user->twofa_secret = $secret;
            $user->save();
        }

        // Generate current OTP
        $currentCode = $google2fa->getCurrentOtp($user->twofa_secret);

        $html = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 40px auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px;'>
            <h2>2FA Development Helper</h2>
            <p><strong>User:</strong> {$user->name} ({$user->email})</p>
            <p><strong>2FA Status:</strong> " . ($user->twofa_enabled ? 'Enabled' : 'Disabled') . "</p>
            <p><strong>Secret Key:</strong> <code>{$user->twofa_secret}</code></p>
            <p><strong>Current OTP:</strong> <span style='font-size: 24px; font-weight: bold; color: #007bff; font-family: monospace;'>{$currentCode}</span></p>
            <div style='margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 5px;'>
                <p><strong>Instructions:</strong></p>
                <ol>
                    <li>Go to <a href='/2fa'>/2fa</a> to see the QR code</li>
                    <li>Use the OTP code above: <strong>{$currentCode}</strong></li>
                    <li>Enable 2FA by submitting the form</li>
                </ol>
            </div>
            <p style='margin-top: 20px;'><a href='/2fa'>Go to 2FA Settings</a></p>
        </div>";

        return $html;
    })->name('dev.2fa-code');
}
