<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Artwork;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Services\FileUploadService;
use Exception;

class ArtworkApiController extends Controller
{
    private FileUploadService $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    /**
     * Display a listing of artworks
     */
    public function index(Request $request): JsonResponse
    {
        $query = Artwork::with(['user:id,name,avatar_path'])->published();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(title, '$.en')) LIKE ?", ["%{$search}%"])
                    ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(title, '$.ka')) LIKE ?", ["%{$search}%"])
                    ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(description, '$.en')) LIKE ?", ["%{$search}%"])
                    ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(description, '$.ka')) LIKE ?", ["%{$search}%"])
                    ->orWhere('category', 'like', "%{$search}%")
                    ->orWhere('subcategory', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        // AI generated filter
        if ($request->boolean('ai_generated')) {
            $query->where('is_ai_generated', true);
        }

        // Sorting
        $sort = $request->input('sort', 'newest');
        switch ($sort) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'most_liked':
                $query->withCount('likes')->orderBy('likes_count', 'desc');
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

        $perPage = min($request->input('per_page', 15), 50); // Max 50 per page
        $artworks = $query->paginate($perPage);

        return response()->json([
            'success' => true,
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

    /**
     * Store a newly created artwork
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title_en' => 'required|string|max:255',
            'title_ka' => 'nullable|string|max:255',
            'description_en' => 'nullable|string|max:2000',
            'description_ka' => 'nullable|string|max:2000',
            'file' => 'required|file|max:102400', // 100MB
            'category' => 'required|string|in:digital-art,painting,photography,sculpture,music,video,mixed-media',
            'subcategory' => 'nullable|string|max:100',
            'license_type' => 'required|in:' . implode(',', array_keys(Artwork::getLicenseTypes())),
            'copyright_notice' => 'nullable|string|max:500',
            'watermark_enabled' => 'boolean',
            'tags' => 'nullable|array|max:10',
            'tags.*' => 'string|max:50',
            'is_ai_generated' => 'boolean',
            'ai_tools_used' => 'nullable|array|max:10',
            'ai_tools_used.*' => 'string|max:100',
            'visibility' => 'required|in:public,private,unlisted',
            'comments_enabled' => 'boolean',
            'downloads_enabled' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Prepare metadata for upload service
            $metadata = [
                'title' => [
                    'en' => $request->title_en,
                    'ka' => $request->title_ka
                ],
                'description' => $request->filled('description_en') || $request->filled('description_ka') ? [
                    'en' => $request->description_en,
                    'ka' => $request->description_ka
                ] : null,
                'license_type' => $request->license_type,
                'copyright_notice' => $request->copyright_notice,
                'watermark_enabled' => $request->boolean('watermark_enabled', true),
                'tags' => $request->tags ?? [],
                'category' => $request->category,
                'subcategory' => $request->subcategory,
                'is_ai_generated' => $request->boolean('is_ai_generated', false),
                'ai_tools_used' => $request->is_ai_generated ? ($request->ai_tools_used ?? []) : null,
                'visibility' => $request->visibility,
                'comments_enabled' => $request->boolean('comments_enabled', true),
                'downloads_enabled' => $request->boolean('downloads_enabled', false),
            ];

            // Upload file and create artwork
            $artwork = $this->fileUploadService->uploadArtwork(
                $request->file('file'),
                Auth::user(),
                $metadata
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Artwork uploaded successfully!',
                'data' => [
                    'id' => $artwork->id,
                    'title' => $artwork->getTitle(),
                    'url' => route('artworks.show', $artwork),
                    'thumbnail' => $artwork->getThumbnailUrl(),
                    'status' => $artwork->status,
                ]
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();

            logger('API artwork upload failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'file_name' => $request->file('file')?->getClientOriginalName()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified artwork
     */
    public function show(Artwork $artwork): JsonResponse
    {
        // Check if artwork is accessible
        if (!$this->canViewArtwork($artwork)) {
            return response()->json([
                'success' => false,
                'message' => 'Artwork not found or not accessible'
            ], 404);
        }

        // Load relationships
        $artwork->load([
            'user:id,name,avatar_path,created_at',
            'evaluations' => function ($query) {
                $query->where('status', 'approved')
                    ->with('evaluator:id,name')
                    ->latest()
                    ->limit(5);
            }
        ]);

        // Increment view count (API calls also count as views)
        $artwork->incrementViewCount();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $artwork->id,
                'title' => $artwork->getTitle(),
                'description' => $artwork->getDescription(),
                'file_url' => $artwork->getFileUrl(),
                'thumbnail_url' => $artwork->getThumbnailUrl(),
                'category' => $artwork->category,
                'subcategory' => $artwork->subcategory,
                'tags' => $artwork->tags,
                'license_type' => $artwork->license_type,
                'copyright_notice' => $artwork->copyright_notice,
                'is_ai_generated' => $artwork->is_ai_generated,
                'ai_tools_used' => $artwork->ai_tools_used,
                'visibility' => $artwork->visibility,
                'status' => $artwork->status,
                'acq_score' => $artwork->acq_score,
                'evaluation_count' => $artwork->evaluation_count,
                'view_count' => $artwork->view_count,
                'like_count' => $artwork->like_count,
                'comment_count' => $artwork->comment_count,
                'created_at' => $artwork->created_at,
                'published_at' => $artwork->published_at,
                'user' => $artwork->user,
                'recent_evaluations' => $artwork->evaluations,
                'is_liked' => Auth::check() ? $artwork->isLikedBy(Auth::user()) : false,
                'can_edit' => Auth::check() && Auth::id() === $artwork->user_id,
            ]
        ]);
    }

    /**
     * Update the specified artwork
     */
    public function update(Request $request, Artwork $artwork): JsonResponse
    {
        if (Auth::id() !== $artwork->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title_en' => 'required|string|max:255',
            'title_ka' => 'nullable|string|max:255',
            'description_en' => 'nullable|string|max:2000',
            'description_ka' => 'nullable|string|max:2000',
            'category' => 'nullable|string|in:digital-art,painting,photography,sculpture,music,video,mixed-media',
            'subcategory' => 'nullable|string|max:100',
            'license_type' => 'required|in:' . implode(',', array_keys(Artwork::getLicenseTypes())),
            'copyright_notice' => 'nullable|string|max:500',
            'watermark_enabled' => 'boolean',
            'tags' => 'nullable|array|max:10',
            'tags.*' => 'string|max:50',
            'is_ai_generated' => 'boolean',
            'ai_tools_used' => 'nullable|array|max:10',
            'ai_tools_used.*' => 'string|max:100',
            'visibility' => 'required|in:public,private,unlisted',
            'comments_enabled' => 'boolean',
            'downloads_enabled' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $artwork->update([
                'title' => [
                    'en' => $request->title_en,
                    'ka' => $request->title_ka
                ],
                'description' => $request->filled('description_en') || $request->filled('description_ka') ? [
                    'en' => $request->description_en,
                    'ka' => $request->description_ka
                ] : null,
                'license_type' => $request->license_type,
                'copyright_notice' => $request->copyright_notice,
                'watermark_enabled' => $request->boolean('watermark_enabled'),
                'tags' => $request->tags ?? [],
                'category' => $request->category,
                'subcategory' => $request->subcategory,
                'is_ai_generated' => $request->boolean('is_ai_generated'),
                'ai_tools_used' => $request->is_ai_generated ? ($request->ai_tools_used ?? []) : null,
                'visibility' => $request->visibility,
                'comments_enabled' => $request->boolean('comments_enabled'),
                'downloads_enabled' => $request->boolean('downloads_enabled'),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Artwork updated successfully!',
                'data' => [
                    'id' => $artwork->id,
                    'title' => $artwork->getTitle(),
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Update failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified artwork
     */
    public function destroy(Artwork $artwork): JsonResponse
    {
        if (Auth::id() !== $artwork->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        try {
            DB::beginTransaction();

            // Delete associated files
            $this->fileUploadService->deleteArtworkFiles($artwork);

            // Delete the artwork record
            $artwork->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Artwork deleted successfully!'
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Delete failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle like/unlike for an artwork
     */
    public function toggleLike(Artwork $artwork): JsonResponse
    {
        $user = Auth::user();

        // Users cannot like their own artwork
        if ($artwork->user_id === $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot like your own artwork'
            ], 403);
        }

        try {
            $liked = $artwork->likes()->where('user_id', $user->id)->exists();

            if ($liked) {
                // Unlike
                $artwork->likes()->where('user_id', $user->id)->delete();
                $artwork->decrement('like_count');
                $action = 'unliked';
            } else {
                // Like
                $artwork->likes()->create(['user_id' => $user->id]);
                $artwork->increment('like_count');
                $action = 'liked';
            }

            return response()->json([
                'success' => true,
                'action' => $action,
                'like_count' => $artwork->fresh()->like_count,
                'is_liked' => !$liked
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle like'
            ], 500);
        }
    }

    /**
     * Publish a draft artwork
     */
    public function publish(Artwork $artwork): JsonResponse
    {
        if (Auth::id() !== $artwork->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        if ($artwork->status !== 'draft') {
            return response()->json([
                'success' => false,
                'message' => 'Only draft artworks can be published'
            ], 400);
        }

        try {
            $artwork->update([
                'status' => 'published',
                'published_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Artwork published successfully!',
                'data' => [
                    'status' => $artwork->status,
                    'published_at' => $artwork->published_at,
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to publish artwork'
            ], 500);
        }
    }

    /**
     * Unpublish a published artwork
     */
    public function unpublish(Artwork $artwork): JsonResponse
    {
        if (Auth::id() !== $artwork->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        if ($artwork->status !== 'published') {
            return response()->json([
                'success' => false,
                'message' => 'Only published artworks can be unpublished'
            ], 400);
        }

        try {
            $artwork->update([
                'status' => 'draft',
                'published_at' => null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Artwork unpublished successfully!',
                'data' => [
                    'status' => $artwork->status,
                    'published_at' => $artwork->published_at,
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to unpublish artwork'
            ], 500);
        }
    }

    /**
     * Check if user can view artwork based on visibility and ownership
     */
    private function canViewArtwork(Artwork $artwork): bool
    {
        // Public artworks are always visible
        if ($artwork->visibility === 'public' && $artwork->status === 'published') {
            return true;
        }

        // Check if user is authenticated
        $user = Auth::user();
        if (!$user) {
            return false;
        }

        // Owner can always view their artworks
        if ($artwork->user_id === $user->id) {
            return true;
        }

        // Unlisted artworks are visible to anyone with the link
        if ($artwork->visibility === 'unlisted' && $artwork->status === 'published') {
            return true;
        }

        return false;
    }
}
