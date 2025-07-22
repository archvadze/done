<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Artwork;
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
            'parent_id' => $request->parent_id,
            'content' => $request->content,
            'status' => 'active',
        ]);

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
