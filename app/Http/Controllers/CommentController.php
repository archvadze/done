<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Artwork;
use App\Models\CommunityPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CommentController extends Controller
{
    /**
     * Store a newly created comment
     */
    public function store(Request $request)
    {
        // Handle both legacy artwork comments and new polymorphic comments
        if ($request->has('artwork_id')) {
            // Legacy artwork comment handling
            $request->validate([
                'artwork_id' => 'required|exists:artworks,id',
                'content' => 'required|string|min:1|max:2000',
                'parent_id' => 'nullable|exists:comments,id',
            ]);

            $artwork = Artwork::findOrFail($request->artwork_id);

            // Check if user can comment on this artwork
            if ($artwork->privacy_setting === 'private' && $artwork->user_id !== Auth::id()) {
                throw ValidationException::withMessages([
                    'artwork' => 'You cannot comment on this private artwork.'
                ]);
            }

            $comment = Comment::create([
                'user_id' => Auth::id(),
                'artwork_id' => $request->artwork_id,
                'commentable_type' => Artwork::class,
                'commentable_id' => $request->artwork_id,
                'parent_id' => $request->parent_id,
                'content' => $request->content,
                'status' => 'active',
            ]);
        } else {
            // New polymorphic comment handling
            $request->validate([
                'commentable_type' => 'required|string|in:' . implode(',', ['App\\Models\\Artwork', 'App\\Models\\CommunityPost']),
                'commentable_id' => 'required|integer',
                'content' => 'required|string|min:1|max:2000',
                'parent_id' => 'nullable|exists:comments,id',
            ]);

            // Get the commentable model
            $commentableClass = $request->commentable_type;
            $commentable = $commentableClass::findOrFail($request->commentable_id);

            // Check permissions based on the commentable type
            if ($commentable instanceof Artwork) {
                if ($commentable->privacy_setting === 'private' && $commentable->user_id !== Auth::id()) {
                    throw ValidationException::withMessages([
                        'commentable' => 'You cannot comment on this private artwork.'
                    ]);
                }
            }

            $comment = Comment::create([
                'user_id' => Auth::id(),
                'commentable_type' => $request->commentable_type,
                'commentable_id' => $request->commentable_id,
                'parent_id' => $request->parent_id,
                'content' => $request->content,
                'status' => 'active',
            ]);

            // For community posts, update comment count
            if ($commentable instanceof \App\Models\CommunityPost) {
                $commentable->increment('comment_count');
            }
        }

        // Load relationships for response
        $comment->load(['user', 'replies.user']);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'comment' => $comment,
                'message' => 'Comment added successfully!'
            ]);
        }

        return redirect()->back()
            ->with('success', 'Comment added successfully!');
    }

    /**
     * Update the specified comment
     */
    public function update(Request $request, Comment $comment)
    {
        // Check if user owns this comment
        if ($comment->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'content' => 'required|string|min:1|max:2000',
        ]);

        $comment->update([
            'content' => $request->content,
            'is_edited' => true,
            'edited_at' => now(),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'comment' => $comment,
                'message' => 'Comment updated successfully!'
            ]);
        }

        return redirect()->back()
            ->with('success', 'Comment updated successfully!');
    }

    /**
     * Remove the specified comment
     */
    public function destroy(Comment $comment)
    {
        // Check if user owns this comment or is the artwork owner
        if ($comment->user_id !== Auth::id() && $comment->artwork->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Soft delete by changing status
        $comment->update(['status' => 'deleted']);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Comment deleted successfully!'
            ]);
        }

        return redirect()->back()
            ->with('success', 'Comment deleted successfully!');
    }

    /**
     * Get comments for an artwork (AJAX)
     */
    public function getComments(Artwork $artwork)
    {
        $comments = $artwork->comments()
            ->with(['user', 'replies' => function ($query) {
                $query->with('user')->orderBy('created_at');
            }])
            ->latest()
            ->paginate(10);

        return response()->json([
            'comments' => $comments,
            'total_comments' => $artwork->allComments()->count(),
        ]);
    }
}
