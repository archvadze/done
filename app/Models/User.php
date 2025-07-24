<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'provider',
        'provider_id',
        'oauth_avatar',
        'oauth_email_verified',
        'avatar_path',
        'bio',
        'location',
        'website',
        'creative_field',
        'lang',
        'notification_prefs',
        'privacy_prefs',
        'role',
        'status',
        'twofa_enabled',
        'twofa_backup_codes',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'twofa_secret',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'oauth_email_verified' => 'boolean',
            'twofa_enabled' => 'boolean',
            'notification_prefs' => 'array',
            'privacy_prefs' => 'array',
            'twofa_backup_codes' => 'array',
        ];
    }

    /**
     * Get linked accounts for this user
     */
    public function linkedAccounts()
    {
        return $this->hasMany(LinkedAccount::class);
    }

    /**
     * Get comments by this user
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get artworks by this user
     */
    public function artworks()
    {
        return $this->hasMany(Artwork::class);
    }

    /**
     * User evaluations
     */
    public function evaluations()
    {
        return $this->hasMany(Evaluation::class, 'evaluator_id');
    }

    /**
     * Users this user is following
     */
    public function following()
    {
        return $this->belongsToMany(User::class, 'user_follows', 'follower_id', 'following_id')
            ->withTimestamps();
    }

    /**
     * Users following this user
     */
    public function followers()
    {
        return $this->belongsToMany(User::class, 'user_follows', 'following_id', 'follower_id')
            ->withTimestamps();
    }

    /**
     * Check if this user is following another user
     */
    public function isFollowing($user)
    {
        if (!$user) return false;
        $userId = is_object($user) ? $user->id : $user;
        return $this->following()->where('following_id', $userId)->exists();
    }

    /**
     * Check if this user is followed by another user
     */
    public function isFollowedBy($user)
    {
        if (!$user) return false;
        $userId = is_object($user) ? $user->id : $user;
        return $this->followers()->where('follower_id', $userId)->exists();
    }

    /**
     * Follow a user
     */
    public function follow($user)
    {
        $userId = is_object($user) ? $user->id : $user;

        // Prevent self-following
        if ($userId === $this->id) {
            return false;
        }

        // Check if already following
        if ($this->isFollowing($userId)) {
            return false;
        }

        $this->following()->attach($userId);
        return true;
    }

    /**
     * Unfollow a user
     */
    public function unfollow($user)
    {
        $userId = is_object($user) ? $user->id : $user;
        $this->following()->detach($userId);
        return true;
    }

    /**
     * Get following count
     */
    public function getFollowingCountAttribute()
    {
        return $this->following()->count();
    }

    /**
     * Get followers count
     */
    public function getFollowersCountAttribute()
    {
        return $this->followers()->count();
    }

    /**
     * Check if user has linked account for provider
     */
    public function hasLinkedAccount(string $provider): bool
    {
        return $this->linkedAccounts()->where('provider', $provider)->exists();
    }

    /**
     * Get avatar URL (prioritize custom avatar, fallback to OAuth)
     */
    public function getAvatarUrlAttribute(): ?string
    {
        if ($this->avatar_path) {
            // If avatar_path already contains 'avatars/', use it directly
            if (str_contains($this->avatar_path, 'avatars/')) {
                return asset('storage/' . $this->avatar_path);
            } else {
                return asset('storage/avatars/' . $this->avatar_path);
            }
        }

        return $this->oauth_avatar;
    }

    /**
     * Check if user is an artist
     */
    public function isArtist(): bool
    {
        return $this->role === 'artist';
    }

    /**
     * Check if user is a moderator
     */
    public function isModerator(): bool
    {
        return $this->role === 'moderator';
    }

    /**
     * Check if user is an admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user can evaluate artworks
     */
    public function canEvaluate(): bool
    {
        return $this->isModerator() || $this->isAdmin();
    }

    /**
     * Check if user can evaluate specific artwork
     */
    public function canEvaluateArtwork(Artwork $artwork): bool
    {
        // Cannot evaluate own artwork
        if ($this->id === $artwork->user_id) {
            return false;
        }

        // Only moderators and admins can evaluate
        return $this->canEvaluate();
    }
}
