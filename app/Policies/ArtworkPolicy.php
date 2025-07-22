<?php

namespace App\Policies;

use App\Models\Artwork;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ArtworkPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user): bool
    {
        // Anyone can view published artworks
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, Artwork $artwork): bool
    {
        // Anyone can view published artworks
        if ($artwork->status === 'published') {
            return true;
        }

        // Only owner can view draft/private artworks
        return $user && $user->id === $artwork->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Any authenticated user can create artworks
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Artwork $artwork): bool
    {
        // Only the owner can update their artwork
        return $user->id === $artwork->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Artwork $artwork): bool
    {
        // Only the owner can delete their artwork
        return $user->id === $artwork->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Artwork $artwork): bool
    {
        // Only the owner can restore their artwork
        return $user->id === $artwork->user_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Artwork $artwork): bool
    {
        // Only the owner can permanently delete their artwork
        return $user->id === $artwork->user_id;
    }

    /**
     * Determine whether the user can like the artwork.
     */
    public function like(?User $user, Artwork $artwork): bool
    {
        // Only authenticated users can like artworks
        // Cannot like own artworks
        return $user && $user->id !== $artwork->user_id;
    }

    /**
     * Determine whether the user can publish/unpublish the artwork.
     */
    public function publish(User $user, Artwork $artwork): bool
    {
        // Only the owner can publish/unpublish their artwork
        return $user->id === $artwork->user_id;
    }
}
