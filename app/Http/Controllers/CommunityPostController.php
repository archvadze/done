<?php

namespace App\Http\Controllers;

use App\Models\Community;
use App\Models\CommunityPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommunityPostController extends Controller
{
    /**
     * Store a new community post
     */
    public function store(Request $request, Community $community)
    {
        // Check if user can post in this community
        if (!$community->isMember(Auth::user())) {
            abort(403, 'You must be a member to post in this community.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:10000',
            'type' => 'required|in:discussion,announcement,question,showcase',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:10240', // 10MB max per file
        ]);

        $validated['community_id'] = $community->id;
        $validated['user_id'] = Auth::id();

        // Handle file attachments
        if ($request->hasFile('attachments')) {
            $attachments = [];
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('community-posts', 'public');
                $attachments[] = [
                    'path' => $path,
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ];
            }
            $validated['attachments'] = $attachments;
        }

        $post = CommunityPost::create($validated);

        return redirect()->route('communities.posts.show', [$community->slug, $post->id])
            ->with('success', 'Post created successfully!');
    }

    /**
     * Display a specific post
     */
    public function show(Community $community, CommunityPost $post)
    {
        // Check if user can view this community
        if ($community->privacy === 'private' && !$community->isMember(Auth::user() ?? new \App\Models\User())) {
            abort(403, 'This community is private.');
        }

        // Check if post belongs to this community
        if ($post->community_id !== $community->id) {
            abort(404);
        }

        // Increment view count
        $post->incrementViewCount();

        // Load relationships
        $post->load(['user', 'comments.user']);

        return view('communities.posts.show', compact('community', 'post'));
    }

    /**
     * Show form for creating a new post
     */
    public function create(Community $community)
    {
        // Check if user can post in this community
        if (!$community->isMember(Auth::user())) {
            abort(403, 'You must be a member to post in this community.');
        }

        return view('communities.posts.create', compact('community'));
    }

    /**
     * Show form for editing a post
     */
    public function edit(Community $community, CommunityPost $post)
    {
        // Check if user can edit this post
        if ($post->user_id !== Auth::id() && !$community->canModerate(Auth::user())) {
            abort(403);
        }

        return view('communities.posts.edit', compact('community', 'post'));
    }

    /**
     * Update a post
     */
    public function update(Request $request, Community $community, CommunityPost $post)
    {
        // Check if user can edit this post
        if ($post->user_id !== Auth::id() && !$community->canModerate(Auth::user())) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:10000',
            'type' => 'required|in:discussion,announcement,question,showcase',
        ]);

        $post->update($validated);

        return redirect()->route('communities.posts.show', [$community->slug, $post->id])
            ->with('success', 'Post updated successfully!');
    }

    /**
     * Delete a post
     */
    public function destroy(Community $community, CommunityPost $post)
    {
        // Check if user can delete this post
        if ($post->user_id !== Auth::id() && !$community->canModerate(Auth::user())) {
            abort(403);
        }

        $post->delete();

        return redirect()->route('communities.show', $community->slug)
            ->with('success', 'Post deleted successfully!');
    }

    /**
     * Pin/unpin a post (moderators only)
     */
    public function togglePin(Community $community, CommunityPost $post)
    {
        if (!$community->canModerate(Auth::user())) {
            abort(403);
        }

        $post->togglePin();

        $message = $post->is_pinned ? 'Post pinned successfully!' : 'Post unpinned successfully!';

        return back()->with('success', $message);
    }

    /**
     * Lock/unlock a post (moderators only)
     */
    public function toggleLock(Community $community, CommunityPost $post)
    {
        if (!$community->canModerate(Auth::user())) {
            abort(403);
        }

        $post->toggleLock();

        $message = $post->is_locked ? 'Post locked successfully!' : 'Post unlocked successfully!';

        return back()->with('success', $message);
    }

    /**
     * Like a post
     */
    public function like(Community $community, CommunityPost $post)
    {
        // Check if user can view this community
        if (!$community->isMember(Auth::user())) {
            return response()->json(['error' => 'You must be a member to like posts.'], 403);
        }

        // Here you would implement a likes table for tracking individual likes
        // For now, we'll just increment the counter
        $post->incrementLikeCount();

        return response()->json([
            'success' => true,
            'like_count' => $post->like_count
        ]);
    }
}
