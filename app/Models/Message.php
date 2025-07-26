<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'user_id',
        'content',
        'type',
        'attachments',
        'edited_at',
        'is_deleted',
        'metadata',
    ];

    protected $casts = [
        'attachments' => 'array',
        'metadata' => 'array',
        'edited_at' => 'datetime',
        'is_deleted' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the conversation this message belongs to
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * Get the user who sent this message
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if message is edited
     */
    public function isEdited(): bool
    {
        return !is_null($this->edited_at);
    }

    /**
     * Mark message as edited
     */
    public function markAsEdited(): void
    {
        $this->update(['edited_at' => now()]);
    }

    /**
     * Soft delete message
     */
    public function softDelete(): void
    {
        $this->update([
            'is_deleted' => true,
            'content' => 'This message has been deleted.',
        ]);
    }

    /**
     * Check if user can edit this message
     */
    public function canEdit(User $user): bool
    {
        return $this->user_id === $user->id && !$this->is_deleted;
    }

    /**
     * Check if user can delete this message
     */
    public function canDelete(User $user): bool
    {
        return $this->user_id === $user->id && !$this->is_deleted;
    }

    /**
     * Get formatted message content
     */
    public function getFormattedContent(): string
    {
        if ($this->is_deleted) {
            return '<em class="text-gray-500">This message has been deleted.</em>';
        }

        return nl2br(e($this->content));
    }

    /**
     * Scope for non-deleted messages
     */
    public function scopeNotDeleted($query)
    {
        return $query->where('is_deleted', false);
    }

    /**
     * Scope for messages by type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
