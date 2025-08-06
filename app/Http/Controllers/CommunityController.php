<?php

namespace App\Http\Controllers;

use App\Models\Community;
use App\Models\CommunityPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CommunityController extends Controller
{
    /**
     * Display listing of communities
     */
    public function index(Request $request)
    {
        $query = Community::with(['creator'])
            ->active()
            ->public();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sort options
        $sort = $request->get('sort', 'popular');
        switch ($sort) {
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'alphabetical':
                $query->orderBy('name', 'asc');
                break;
            case 'popular':
            default:
                $query->orderBy('member_count', 'desc');
                break;
        }

        $communities = $query->paginate(12);

        return view('communities.index', compact('communities'));
    }

    /**
     * Show the form for creating a new community
     */
    public function create()
    {
        return view('communities.create');
    }

    /**
     * Store a newly created community
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:communities,name',
            'description' => 'required|string|max:1000',
            'privacy' => 'required|in:public,private,hidden',
            'requires_approval' => 'boolean',
            'rules' => 'nullable|array',
            'rules.*' => 'string|max:500',
            'cover_image' => 'nullable|image|max:2048',
            'avatar' => 'nullable|image|max:1024',
        ]);

        // Generate unique slug
        $slug = Str::slug($validated['name']);
        $originalSlug = $slug;
        $counter = 1;
        
        while (Community::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $validated['slug'] = $slug;
        $validated['creator_id'] = Auth::id();

        // Handle file uploads
        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('communities/covers', 'public');
        }

        if ($request->hasFile('avatar')) {
            $validated['avatar'] = $request->file('avatar')->store('communities/avatars', 'public');
        }

        $community = Community::create($validated);

        // Add creator as admin member
        $community->addMember(Auth::user(), 'admin');

        return redirect()->route('communities.show', $community->slug)
            ->with('success', 'Community created successfully!');
    }

    /**
     * Display the specified community
     */
    public function show(Community $community, Request $request)
    {
        try {
            Log::info('Controller show method called', ['community_id' => $community->id]);
            
            $user = Auth::user();
            Log::info('User retrieved', ['user_id' => $user ? $user->id : 'none']);
            
            // Check if user can view this community
            $isGlobalAdmin = $user && $user->role === 'admin';
            
            if ($community->privacy === 'private' && (!$user || (!$isGlobalAdmin && !$community->isMember($user)))) {
                abort(403, 'This community is private.');
            }

            if ($community->privacy === 'hidden' && (!$user || (!$isGlobalAdmin && !$community->canModerate($user)))) {
                abort(404);
            }
            Log::info('Privacy checks passed');

            // Load community data
            $community->load(['creator', 'activeMembers' => function ($query) {
                $query->latest('community_members.joined_at')->limit(12);
            }]);
            Log::info('Community data loaded');

            // Get posts with filters
            $postsQuery = $community->posts()
                ->with(['user', 'comments'])
                ->where('is_locked', false);
            Log::info('Posts query created');

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
            Log::info('Posts query sorted');

            $posts = $postsQuery->paginate(10);
            Log::info('Posts paginated', ['count' => $posts->count()]);

            // Get pinned posts separately for display at top
            $pinnedPosts = $community->pinnedPosts()->with(['user', 'comments'])->get();
            Log::info('Pinned posts retrieved', ['count' => $pinnedPosts->count()]);

            Log::info('About to return view');
            return view('communities.show', compact('community', 'posts', 'pinnedPosts'));
            
        } catch (\Exception $e) {
            Log::error('Error in CommunityController@show', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Show the form for editing community
     */
    public function edit(Community $community)
    {
        // Only creator and admins can edit
        if (!$community->canModerate(Auth::user())) {
            abort(403);
        }

        return view('communities.edit', compact('community'));
    }

    /**
     * Update the specified community
     */
    public function update(Request $request, Community $community)
    {
        // Only creator and admins can edit
        if (!$community->canModerate(Auth::user())) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('communities', 'name')->ignore($community->id)],
            'description' => 'required|string|max:1000',
            'privacy' => 'required|in:public,private,hidden',
            'requires_approval' => 'boolean',
            'rules' => 'nullable|array',
            'rules.*' => 'string|max:500',
            'cover_image' => 'nullable|image|max:2048',
            'avatar' => 'nullable|image|max:1024',
        ]);

        // Handle file uploads
        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('communities/covers', 'public');
        }

        if ($request->hasFile('avatar')) {
            $validated['avatar'] = $request->file('avatar')->store('communities/avatars', 'public');
        }

        $community->update($validated);

        return redirect()->route('communities.show', $community->slug)
            ->with('success', 'Community updated successfully!');
    }

    /**
     * Join a community
     */
    public function join(Community $community)
    {
        $user = Auth::user();

        if ($community->isMember($user)) {
            return back()->with('error', 'You are already a member of this community.');
        }

        $status = $community->requires_approval ? 'pending' : 'active';
        $community->addMember($user, 'member', $status);

        $message = $status === 'pending' 
            ? 'Your membership request has been sent for approval.'
            : 'You have successfully joined the community!';

        return back()->with('success', $message);
    }

    /**
     * Leave a community
     */
    public function leave(Community $community)
    {
        $user = Auth::user();

        if (!$community->isMember($user)) {
            return back()->with('error', 'You are not a member of this community.');
        }

        if ($community->isCreator($user)) {
            return back()->with('error', 'Community creators cannot leave their community.');
        }

        $community->removeMember($user);

        return back()->with('success', 'You have left the community.');
    }

    /**
     * Show community members
     */
    public function members(Community $community)
    {
        // Check access
        if ($community->privacy === 'private' && !$community->isMember(Auth::user() ?? new \App\Models\User())) {
            abort(403);
        }

        $members = $community->activeMembers()
            ->withPivot(['role', 'joined_at'])
            ->orderBy('community_members.role', 'desc')
            ->orderBy('community_members.joined_at', 'desc')
            ->paginate(24);

        return view('communities.members', compact('community', 'members'));
    }

    /**
     * Delete community (creator only)
     */
    public function destroy(Community $community)
    {
        if (!$community->isCreator(Auth::user())) {
            abort(403);
        }

        $community->delete();

        return redirect()->route('communities.index')
            ->with('success', 'Community deleted successfully.');
    }
}
