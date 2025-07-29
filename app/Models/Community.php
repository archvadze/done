<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Community extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'cover_image',
        'avatar',
        'creator_id',
        'privacy',
        'requires_approval',
        'rules',
        'metadata',
        'status',
        'member_count',
    ];

    protected $casts = [
        'rules' => 'array',
        'metadata' => 'array',
        'requires_approval' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($community) {
            if (empty($community->slug)) {
                $community->slug = Str::slug($community->name);
            }
        });
    }

    /**
     * Get the creator of the community
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    /**
     * Get all posts in this community
     */
    public function posts(): HasMany
    {
        return $this->hasMany(CommunityPost::class);
    }

    /**
     * Get recent posts in this community
     */
    public function recentPosts(): HasMany
    {
        return $this->posts()->orderBy('created_at', 'desc');
    }

    /**
     * Get pinned posts in this community
     */
    public function pinnedPosts(): HasMany
    {
        return $this->posts()->where('is_pinned', true)->orderBy('created_at', 'desc');
    }

    /**
     * Get all members through the pivot table
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'community_members')
            ->withPivot(['role', 'status', 'joined_at'])
            ->withTimestamps();
    }

    /**
     * Get active members only
     */
    public function activeMembers(): BelongsToMany
    {
        return $this->members()->wherePivot('status', 'active');
    }

    /**
     * Get moderators of this community
     */
    public function moderators(): BelongsToMany
    {
        return $this->members()->wherePivot('role', 'moderator')->wherePivot('status', 'active');
    }

    /**
     * Get admins of this community
     */
    public function admins(): BelongsToMany
    {
        return $this->members()->wherePivot('role', 'admin')->wherePivot('status', 'active');
    }

    /**
     * Check if user is a member
     */
    public function isMember(User $user): bool
    {
        return $this->members()->where('user_id', $user->id)->wherePivot('status', 'active')->exists();
    }

    /**
     * Check if user is a moderator
     */
    public function isModerator(User $user): bool
    {
        return $this->members()
            ->where('user_id', $user->id)
            ->wherePivot('role', 'moderator')
            ->wherePivot('status', 'active')
            ->exists();
    }

    /**
     * Check if user is an admin
     */
    public function isAdmin(User $user): bool
    {
        return $this->members()
            ->where('user_id', $user->id)
            ->wherePivot('role', 'admin')
            ->wherePivot('status', 'active')
            ->exists();
    }

    /**
     * Check if user is creator
     */
    public function isCreator(User $user): bool
    {
        return $this->creator_id === $user->id;
    }

    /**
     * Check if user can moderate
     */
    public function canModerate(User $user): bool
    {
        return $this->isCreator($user) 
            || $this->isAdmin($user) 
            || $this->isModerator($user)
            || $user->isModerator() 
            || $user->isAdmin();
    }

    /**
     * Add a member to the community
     */
    public function addMember(User $user, string $role = 'member', string $status = 'active'): bool
    {
        if ($this->isMember($user)) {
            return false;
        }

        $this->members()->attach($user->id, [
            'role' => $role,
            'status' => $status,
            'joined_at' => now(),
        ]);

        $this->increment('member_count');
        return true;
    }

    /**
     * Remove a member from the community
     */
    public function removeMember(User $user): bool
    {
        if (!$this->isMember($user)) {
            return false;
        }

        $this->members()->detach($user->id);
        $this->decrement('member_count');
        return true;
    }

    /**
     * Get the route key name for model binding
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Scope for public communities
     */
    public function scopePublic($query)
    {
        return $query->where('privacy', 'public');
    }

    /**
     * Scope for active communities
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
