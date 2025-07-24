<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use App\Models\ArtworkCategory;
use App\Models\Language;
use App\Services\FileUploadService;
use App\Services\LanguageDetectionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Exception;

class ArtworkController extends Controller
{
    use AuthorizesRequests;

    private FileUploadService $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
        $this->middleware('auth')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Artwork::with(['user', 'likes'])->published();

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
            case 'title':
                $query->orderByRaw("JSON_UNQUOTE(JSON_EXTRACT(title, '$.en')) ASC");
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $artworks = $query->paginate(12);

        // Get available categories for filtering
        $categories = collect([
            (object)['slug' => 'digital-art', 'name' => ['en' => 'Digital Art', 'ka' => 'ციფრული ხელოვნება']],
            (object)['slug' => 'painting', 'name' => ['en' => 'Painting', 'ka' => 'ნახატი']],
            (object)['slug' => 'photography', 'name' => ['en' => 'Photography', 'ka' => 'ფოტოგრაფია']],
            (object)['slug' => 'sculpture', 'name' => ['en' => 'Sculpture', 'ka' => 'ქანდაკება']],
            (object)['slug' => 'music', 'name' => ['en' => 'Music', 'ka' => 'მუსიკა']],
            (object)['slug' => 'video', 'name' => ['en' => 'Video', 'ka' => 'ვიდეო']],
            (object)['slug' => 'mixed-media', 'name' => ['en' => 'Mixed Media', 'ka' => 'შერეული მედია']],
        ]);

        // Add display name to each category object
        $categories = $categories->map(function ($category) {
            $locale = app()->getLocale();
            $category->display_name = $category->name[$locale] ?? $category->name['en'];
            return $category;
        });

        return view('artworks.index', compact('artworks', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Artwork::class);

        // Get available categories for the form
        $categories = collect([
            (object)['slug' => 'digital-art', 'name' => ['en' => 'Digital Art', 'ka' => 'ციფრული ხელოვნება']],
            (object)['slug' => 'painting', 'name' => ['en' => 'Painting', 'ka' => 'ნახატი']],
            (object)['slug' => 'photography', 'name' => ['en' => 'Photography', 'ka' => 'ფოტოგრაფია']],
            (object)['slug' => 'sculpture', 'name' => ['en' => 'Sculpture', 'ka' => 'ქანდაკება']],
            (object)['slug' => 'music', 'name' => ['en' => 'Music', 'ka' => 'მუსიკა']],
            (object)['slug' => 'video', 'name' => ['en' => 'Video', 'ka' => 'ვიდეო']],
            (object)['slug' => 'mixed-media', 'name' => ['en' => 'Mixed Media', 'ka' => 'შერეული მედია']],
        ]);

        // Add display_name to each category object
        $categories = $categories->map(function ($category) {
            $locale = app()->getLocale();
            $category->display_name = $category->name[$locale] ?? $category->name['en'];
            return $category;
        });

        // Available license types
        $licenseTypes = [
            'all_rights_reserved' => 'All Rights Reserved',
            'cc_by' => 'Creative Commons - Attribution',
            'cc_by_sa' => 'Creative Commons - Attribution-ShareAlike',
            'cc_by_nc' => 'Creative Commons - Attribution-NonCommercial',
            'cc_by_nc_sa' => 'Creative Commons - Attribution-NonCommercial-ShareAlike',
            'cc_by_nd' => 'Creative Commons - Attribution-NoDerivs',
            'cc_by_nc_nd' => 'Creative Commons - Attribution-NonCommercial-NoDerivs',
            'public_domain' => 'Public Domain',
        ];

        return view('artworks.create', compact('categories', 'licenseTypes'));
    }
    /**
     * Store a newly created artwork with file upload
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $this->authorize('create', Artwork::class);

        // Simple validation rules
        $validationRules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
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
        ];

        $validator = Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            // Auto-detect language and translate to all active languages
            $languageService = app(LanguageDetectionService::class);
            $detectedLanguage = $languageService->detectLanguage($request->title);
            
            // Auto-translate title and description to all active languages
            $titleTranslations = $languageService->autoTranslate($request->title, $detectedLanguage);
            $descriptionTranslations = $request->description ? 
                $languageService->autoTranslate($request->description, $detectedLanguage) : null;

            // Prepare metadata for upload service
            $metadata = [
                'title' => $titleTranslations,
                'description' => $descriptionTranslations,
                'content_language' => $detectedLanguage,
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

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Artwork uploaded successfully!',
                    'artwork' => [
                        'id' => $artwork->id,
                        'title' => $artwork->getTitle(),
                        'url' => route('artworks.show', $artwork),
                        'thumbnail' => $artwork->getThumbnailUrl(),
                    ]
                ]);
            }

            return redirect()
                ->route('artworks.show', $artwork)
                ->with('success', 'Artwork uploaded successfully!');
        } catch (Exception $e) {
            DB::rollBack();

            logger('Artwork upload failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'file_name' => $request->file('file')?->getClientOriginalName()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Upload failed: ' . $e->getMessage()
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Upload failed: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified artwork with view tracking
     */
    public function show(Artwork $artwork): View
    {
        // Check if artwork is accessible
        if (!$this->canViewArtwork($artwork)) {
            abort(404);
        }

        // Increment view count (avoid multiple counts from same user in session)
        $sessionKey = 'viewed_artwork_' . $artwork->id;
        if (!session($sessionKey)) {
            $artwork->incrementViewCount();
            session([$sessionKey => true]);
        }

        // Load relationships
        $artwork->load([
            'user',
            'likes' => function ($query) {
                $query->with('user');
            }
        ]);

        // Get related artworks
        $relatedArtworks = Artwork::published()
            ->where('id', '!=', $artwork->id)
            ->where(function ($query) use ($artwork) {
                $query->where('user_id', $artwork->user_id)
                    ->orWhere('category', $artwork->category);
            })
            ->limit(6)
            ->get();

        return view('artworks.show', compact('artwork', 'relatedArtworks'));
    }

    /**
     * Show the form for editing the specified artwork
     */
    public function edit(Artwork $artwork): View
    {
        $this->authorize('update', $artwork);

        // Get available categories for the form
        $categories = collect([
            (object)['slug' => 'digital-art', 'name' => ['en' => 'Digital Art', 'ka' => 'ციფრული ხელოვნება']],
            (object)['slug' => 'painting', 'name' => ['en' => 'Painting', 'ka' => 'ნახატი']],
            (object)['slug' => 'photography', 'name' => ['en' => 'Photography', 'ka' => 'ფოტოგრაფია']],
            (object)['slug' => 'sculpture', 'name' => ['en' => 'Sculpture', 'ka' => 'ქანდაკება']],
            (object)['slug' => 'music', 'name' => ['en' => 'Music', 'ka' => 'მუსიკა']],
            (object)['slug' => 'video', 'name' => ['en' => 'Video', 'ka' => 'ვიდეო']],
            (object)['slug' => 'mixed-media', 'name' => ['en' => 'Mixed Media', 'ka' => 'შერეული მედია']],
        ]);

        // Add getName method to each category object
        $categories = $categories->map(function ($category) {
            $category->getName = function () use ($category) {
                $locale = app()->getLocale();
                return $category->name[$locale] ?? $category->name['en'];
            };
            return $category;
        });

        // Available license types
        $licenseTypes = [
            'all_rights_reserved' => 'All Rights Reserved',
            'cc_by' => 'Creative Commons - Attribution',
            'cc_by_sa' => 'Creative Commons - Attribution-ShareAlike',
            'cc_by_nc' => 'Creative Commons - Attribution-NonCommercial',
            'cc_by_nc_sa' => 'Creative Commons - Attribution-NonCommercial-ShareAlike',
            'cc_by_nd' => 'Creative Commons - Attribution-NoDerivs',
            'cc_by_nc_nd' => 'Creative Commons - Attribution-NonCommercial-NoDerivs',
            'public_domain' => 'Public Domain',
        ];

        return view('artworks.edit', compact('artwork', 'categories', 'licenseTypes'));
    }

    /**
     * Update the specified artwork metadata
     */
    public function update(Request $request, Artwork $artwork): JsonResponse|RedirectResponse
    {
        $this->authorize('update', $artwork);

        // Simple validation rules
        $validationRules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'content_language' => 'required|string|in:en,ka,de',
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
        ];

        $validator = Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Auto-detect language and translate to all active languages
            $languageService = app(LanguageDetectionService::class);
            $detectedLanguage = $languageService->detectLanguage($request->title);
            
            // Auto-translate title and description to all active languages
            $titleTranslations = $languageService->autoTranslate($request->title, $detectedLanguage);
            $descriptionTranslations = $request->description ? 
                $languageService->autoTranslate($request->description, $detectedLanguage) : null;

            $artwork->update([
                'title' => $titleTranslations,
                'description' => $descriptionTranslations,
                'content_language' => $detectedLanguage,
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

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Artwork updated successfully!'
                ]);
            }

            return redirect()
                ->route('artworks.show', $artwork)
                ->with('success', 'Artwork updated successfully!');
        } catch (Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Update failed: ' . $e->getMessage()
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Update failed: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified artwork and its files
     */
    public function destroy(Artwork $artwork): JsonResponse|RedirectResponse
    {
        $this->authorize('delete', $artwork);

        try {
            DB::beginTransaction();

            // Delete associated files
            $this->fileUploadService->deleteArtworkFiles($artwork);

            // Delete the artwork record (cascades to likes, etc.)
            $artwork->delete();

            DB::commit();

            return redirect()
                ->route('artworks.index')
                ->with('success', 'Artwork deleted successfully!');
        } catch (Exception $e) {
            DB::rollBack();

            return back()
                ->with('error', 'Delete failed: ' . $e->getMessage());
        }
    }

    /**
     * Toggle like/unlike for an artwork
     */
    public function toggleLike(Artwork $artwork): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required'
            ], 401);
        }

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
    public function publish(Artwork $artwork): RedirectResponse
    {
        $this->authorize('update', $artwork);

        if ($artwork->status !== 'draft') {
            return back()->with('error', 'Only draft artworks can be published');
        }

        try {
            $artwork->update([
                'status' => 'published',
                'published_at' => now()
            ]);

            return back()->with('success', 'Artwork published successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Failed to publish artwork');
        }
    }

    /**
     * Unpublish a published artwork (make it draft)
     */
    public function unpublish(Artwork $artwork): RedirectResponse
    {
        $this->authorize('update', $artwork);

        if ($artwork->status !== 'published') {
            return back()->with('error', 'Only published artworks can be unpublished');
        }

        try {
            $artwork->update([
                'status' => 'draft',
                'published_at' => null
            ]);

            return back()->with('success', 'Artwork unpublished successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Failed to unpublish artwork');
        }
    }

    /**
     * Get artwork upload progress (for AJAX uploads)
     */
    public function uploadProgress(Request $request): JsonResponse
    {
        $sessionId = $request->input('session_id');
        $progress = session("upload_progress_{$sessionId}", 0);

        return response()->json([
            'progress' => $progress,
            'complete' => $progress >= 100
        ]);
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
