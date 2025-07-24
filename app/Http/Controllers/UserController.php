<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Show user profile
     */
    public function profile()
    {
        $user = Auth::user();
        
        // Get user statistics with real data
        try {
            $artworksCount = $user->artworks()->count();
            $publishedArtworksCount = $user->artworks()->where('status', 'published')->count();
            $evaluationsCount = $user->evaluations()->count();
            $avgAcqScore = $user->artworks()->whereNotNull('acq_score')->avg('acq_score');
            $totalLikes = $user->artworks()->sum('like_count');
        } catch (\Exception $e) {
            // Fallback values if relationships fail
            $artworksCount = 0;
            $publishedArtworksCount = 0;
            $evaluationsCount = 0;
            $avgAcqScore = null;
            $totalLikes = 0;
        }
        
        return view('users.profile', compact(
            'user', 
            'artworksCount', 
            'publishedArtworksCount', 
            'evaluationsCount', 
            'avgAcqScore', 
            'totalLikes'
        ));
    }

    /**
     * Show user edit form
     */
    public function edit()
    {
        return view('users.edit', ['user' => Auth::user()]);
    }

    /**
     * Update user profile
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'bio' => 'nullable|string|max:500',
            'location' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'current_password' => 'nullable|string',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Check current password if changing password
        if ($request->filled('password')) {
            if (!$request->filled('current_password')) {
                return back()->withErrors(['current_password' => 'Current password is required to change password.']);
            }
            
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect.']);
            }
        }

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar_path && Storage::exists('public/' . $user->avatar_path)) {
                Storage::delete('public/' . $user->avatar_path);
            }
            
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar_path = $avatarPath;
        }

        // Update user data
        $user->name = $request->name;
        $user->email = $request->email;
        $user->bio = $request->bio;
        $user->location = $request->location;
        $user->website = $request->website;
        
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        
        $user->save();

        return redirect()->route('users.profile')->with('success', 'Profile updated successfully!');
    }

    /**
     * Show public user profile
     */
    public function show(User $user)
    {
        // Get user's public artworks
        $artworks = $user->artworks()
            ->where('status', 'published')
            ->where('visibility', 'public')
            ->latest()
            ->limit(6)
            ->get();
            
        // Get user statistics
        $stats = [
            'artworks_count' => $user->artworks()
                ->where('status', 'published')
                ->where('visibility', 'public')
                ->count(),
            'evaluations_count' => $user->evaluations()->count(),
            'avg_acq_score' => $user->artworks()
                ->where('status', 'published')
                ->whereNotNull('acq_score')
                ->avg('acq_score'),
            'total_likes' => $user->artworks()
                ->where('status', 'published')
                ->sum('like_count'),
        ];

        return view('users.show', compact('user', 'artworks', 'stats'));
    }

    /**
     * Get user's artworks (for full gallery page)
     */
    public function artworks(User $user, Request $request)
    {
        // Build artworks query
        $query = $user->artworks();
        
        // Apply category filter
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        
        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(title, '$.en')) LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(title, '$.ka')) LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(description, '$.en')) LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(description, '$.ka')) LIKE ?", ["%{$search}%"]);
            });
        }
        
        // For other users, only show published artworks
        if (Auth::id() !== $user->id) {
            $query->where('status', 'published');
        }
        
        // Get paginated artworks
        $artworks = $query->latest()->paginate(12);
        
        // Calculate user statistics
        $stats = [
            'artworks_count' => $user->artworks()->count(),
            'published_artworks' => $user->artworks()->where('status', 'published')->count(),
            'total_views' => $user->artworks()->sum('view_count'),
            'total_likes' => $user->artworks()->sum('like_count'),
            'avg_acq_score' => $user->artworks()->whereNotNull('acq_score')->avg('acq_score'),
        ];
        
        if ($request->expectsJson()) {
            return response()->json([
                'artworks' => $artworks->items(),
                'pagination' => [
                    'current_page' => $artworks->currentPage(),
                    'last_page' => $artworks->lastPage(),
                    'total' => $artworks->total(),
                ],
                'stats' => $stats
            ]);
        }
        
        return view('users.artworks', compact('user', 'artworks', 'stats'));
    }

    /**
     * Follow/Unfollow a user
     */
    public function toggleFollow(User $user)
    {
        $currentUser = Auth::user();
        
        if (!$currentUser) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required'
            ], 401);
        }
        
        // Prevent self-following
        if ($currentUser->id === $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot follow yourself'
            ], 403);
        }
        
        try {
            $isFollowing = $currentUser->isFollowing($user);
            
            if ($isFollowing) {
                $currentUser->unfollow($user);
                $action = 'unfollowed';
            } else {
                $currentUser->follow($user);
                $action = 'followed';
            }
            
            return response()->json([
                'success' => true,
                'action' => $action,
                'is_following' => !$isFollowing,
                'followers_count' => $user->fresh()->followers_count
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle follow'
            ], 500);
        }
    }

    /**
     * Get user's followers
     */
    public function followers(User $user)
    {
        $followers = $user->followers()
            ->withPivot('created_at')
            ->orderBy('pivot_created_at', 'desc')
            ->paginate(20);
            
        return view('users.followers', compact('user', 'followers'));
    }

    /**
     * Get user's following list
     */
    public function following(User $user)
    {
        $following = $user->following()
            ->withPivot('created_at')
            ->orderBy('pivot_created_at', 'desc')
            ->paginate(20);
            
        return view('users.following', compact('user', 'following'));
    }
}
