<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'type',
        'created_by',
        'last_message_at',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'last_message_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user who created this conversation
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all messages in this conversation
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get the latest message
     */
    public function latestMessage(): HasMany
    {
        return $this->messages()->limit(1);
    }

    /**
     * Get participants of this conversation
     */
    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'conversation_participants')
            ->withPivot(['joined_at', 'left_at', 'last_read_at', 'is_muted'])
            ->withTimestamps();
    }

    /**
     * Get active participants (haven't left)
     */
    public function activeParticipants(): BelongsToMany
    {
        return $this->participants()->wherePivotNull('left_at');
    }

    /**
     * Check if user is participant
     */
    public function hasParticipant(User $user): bool
    {
        return $this->activeParticipants()->where('user_id', $user->id)->exists();
    }

    /**
     * Add participant to conversation
     */
    public function addParticipant(User $user): bool
    {
        if ($this->hasParticipant($user)) {
            return false;
        }

        $this->participants()->attach($user->id, [
            'joined_at' => now(),
        ]);

        return true;
    }

    /**
     * Remove participant from conversation
     */
    public function removeParticipant(User $user): bool
    {
        if (!$this->hasParticipant($user)) {
            return false;
        }

        $this->participants()->updateExistingPivot($user->id, [
            'left_at' => now(),
        ]);

        return true;
    }

    /**
     * Mark conversation as read for user
     */
    public function markAsRead(User $user): void
    {
        $this->participants()->updateExistingPivot($user->id, [
            'last_read_at' => now(),
        ]);
    }

    /**
     * Get unread messages count for user
     */
    public function getUnreadCount(User $user): int
    {
        $participant = $this->participants()->where('user_id', $user->id)->first();
        
        if (!$participant || !$participant->pivot->last_read_at) {
            return $this->messages()->where('user_id', '!=', $user->id)->count();
        }

        return $this->messages()
            ->where('user_id', '!=', $user->id)
            ->where('created_at', '>', $participant->pivot->last_read_at)
            ->count();
    }

    /**
     * Get conversation title for display
     */
    public function getDisplayTitle(User $currentUser): string
    {
        if ($this->title) {
            return $this->title;
        }

        if ($this->type === 'direct') {
            $otherParticipant = $this->activeParticipants()
                ->where('user_id', '!=', $currentUser->id)
                ->first();
            
            return $otherParticipant ? $otherParticipant->name : 'Unknown User';
        }

        return 'Group Chat';
    }

    /**
     * Create direct message conversation between two users
     */
    public static function createDirectMessage(User $user1, User $user2): self
    {
        // Check if conversation already exists
        $existingConversation = self::whereHas('participants', function ($query) use ($user1) {
            $query->where('user_id', $user1->id)->whereNull('left_at');
        })->whereHas('participants', function ($query) use ($user2) {
            $query->where('user_id', $user2->id)->whereNull('left_at');
        })->where('type', 'direct')->first();

        if ($existingConversation) {
            return $existingConversation;
        }

        $conversation = self::create([
            'type' => 'direct',
            'created_by' => $user1->id,
        ]);

        $conversation->addParticipant($user1);
        $conversation->addParticipant($user2);

        return $conversation;
    }

    /**
     * Scope for conversations with specific user
     */
    public function scopeWithUser($query, User $user)
    {
        return $query->whereHas('activeParticipants', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        });
    }
}
