<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Artwork;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Services\CacheService;
use Exception;

class UserApiController extends Controller
{
    private CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }
    /**
     * Get current authenticated user's profile
     */
    public function me(): JsonResponse
    {
        $user = Auth::user();
        
        // Get cached user profile data
        $profileData = $this->cacheService->getUserProfile($user->id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar_path' => $user->avatar_path,
                'bio' => $user->bio,
                'location' => $user->location,
                'website' => $user->website,
                'created_at' => $user->created_at,
                'stats' => [
                    'artworks_count' => $profileData['artworks_count'],
                    'evaluations_count' => $profileData['evaluations_count'],
                    'average_acq_score' => round($profileData['average_acq_score'] ?? 0, 2),
                    'total_likes' => $profileData['total_likes'],
                ]
            ]
        ]);
    }

    /**
     * Update current user's profile
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'bio' => 'nullable|string|max:500',
            'location' => 'nullable|string|max:100',
            'website' => 'nullable|url|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = [
                'name' => $request->name,
                'bio' => $request->bio,
                'location' => $request->location,
                'website' => $request->website,
            ];

            // Handle avatar upload if provided
            if ($request->hasFile('avatar')) {
                // TODO: Implement avatar upload logic
                // For now, we'll skip this part
                // $avatarPath = $request->file('avatar')->store('avatars', 'public');
                // $data['avatar_path'] = $avatarPath;
            }

            $user->fill($data);
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully!',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'bio' => $user->bio,
                    'location' => $user->location,
                    'website' => $user->website,
                    'avatar_path' => $user->avatar_path,
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's artworks
     */
    public function artworks(Request $request, User $user): JsonResponse
    {
        $query = $user->artworks()
            ->with(['user:id,name,avatar_path']);

        // Only show published artworks for other users
        if (Auth::id() !== $user->id) {
            $query->where('status', 'published')
                ->where('visibility', 'public');
        }

        // Sorting
        $sort = $request->input('sort', 'newest');
        switch ($sort) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'most_liked':
                $query->orderBy('like_count', 'desc');
                break;
            case 'highest_acq':
                $query->whereNotNull('acq_score')->orderBy('acq_score', 'desc');
                break;
            case 'title':
                $query->orderByRaw("JSON_UNQUOTE(JSON_EXTRACT(title, '$.en')) ASC");
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $perPage = min($request->input('per_page', 15), 50);
        $artworks = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'avatar_path' => $user->avatar_path,
                'bio' => $user->bio,
                'location' => $user->location,
                'website' => $user->website,
                'created_at' => $user->created_at,
            ],
            'data' => $artworks->items(),
            'pagination' => [
                'current_page' => $artworks->currentPage(),
                'last_page' => $artworks->lastPage(),
                'per_page' => $artworks->perPage(),
                'total' => $artworks->total(),
                'has_more' => $artworks->hasMorePages(),
            ]
        ]);
    }
}
