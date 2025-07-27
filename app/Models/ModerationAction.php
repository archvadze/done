<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Carbon\Carbon;

class ModerationAction extends Model
{
    use HasFactory;

    protected $fillable = [
        'moderator_id',
        'target_user_id',
        'target_type',
        'target_id',
        'report_id',
        'action_type',
        'reason',
        'duration_hours',
        'expires_at',
        'metadata',
        'is_active',
        'reversed_at',
        'reversed_by',
        'reversal_reason',
    ];

    protected $casts = [
        'metadata' => 'array',
        'expires_at' => 'datetime',
        'reversed_at' => 'datetime',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the moderator who took this action
     */
    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderator_id');
    }

    /**
     * Get the user targeted by this action
     */
    public function targetUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }

    /**
     * Get the target model (artwork, comment, etc.)
     */
    public function target(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the related moderation report
     */
    public function report(): BelongsTo
    {
        return $this->belongsTo(ModerationReport::class, 'report_id');
    }

    /**
     * Get the moderator who reversed this action
     */
    public function reversedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reversed_by');
    }

    /**
     * Check if action is currently active
     */
    public function isActive(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            $this->update(['is_active' => false]);
            return false;
        }

        return true;
    }

    /**
     * Check if action is permanent
     */
    public function isPermanent(): bool
    {
        return $this->expires_at === null && $this->is_active;
    }

    /**
     * Check if action is temporary
     */
    public function isTemporary(): bool
    {
        return $this->expires_at !== null && $this->is_active;
    }

    /**
     * Check if action has expired
     */
    public function hasExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if action is reversed
     */
    public function isReversed(): bool
    {
        return $this->reversed_at !== null;
    }

    /**
     * Reverse this action
     */
    public function reverse(User $moderator, string $reason): void
    {
        $this->update([
            'is_active' => false,
            'reversed_at' => now(),
            'reversed_by' => $moderator->id,
            'reversal_reason' => $reason,
        ]);

        // Log the reversal
        SecurityLog::create([
            'user_id' => $moderator->id,
            'event_type' => 'moderation_action_reversed',
            'event_category' => 'moderation',
            'description' => "Moderation action #{$this->id} reversed",
            'metadata' => [
                'action_id' => $this->id,
                'action_type' => $this->action_type,
                'target_user_id' => $this->target_user_id,
                'reversal_reason' => $reason,
            ],
            'severity' => 'warning',
        ]);

        // Handle specific action reversals
        $this->handleActionReversal();
    }

    /**
     * Handle specific action reversal logic
     */
    protected function handleActionReversal(): void
    {
        switch ($this->action_type) {
            case 'ban':
                // Re-enable user account
                if ($this->targetUser) {
                    $this->targetUser->update(['is_banned' => false]);
                }
                break;

            case 'suspend':
                // Remove suspension
                if ($this->targetUser) {
                    $this->targetUser->update(['is_suspended' => false]);
                }
                break;

            case 'hide_content':
                // Restore content visibility
                if ($this->target) {
                    $this->target->update(['is_hidden' => false]);
                }
                break;

            case 'remove_content':
                // Restore content
                if ($this->target) {
                    $this->target->update(['is_removed' => false]);
                }
                break;
        }
    }

    /**
     * Apply the moderation action
     */
    public function apply(): void
    {
        switch ($this->action_type) {
            case 'warning':
                $this->applyWarning();
                break;

            case 'ban':
                $this->applyBan();
                break;

            case 'suspend':
                $this->applySuspension();
                break;

            case 'hide_content':
                $this->applyContentHide();
                break;

            case 'remove_content':
                $this->applyContentRemoval();
                break;

            case 'copyright_takedown':
                $this->applyCopyrightTakedown();
                break;
        }

        // Log the action application
        SecurityLog::create([
            'user_id' => $this->moderator_id,
            'event_type' => 'moderation_action_applied',
            'event_category' => 'moderation',
            'description' => "Moderation action applied: {$this->action_type}",
            'metadata' => [
                'action_id' => $this->id,
                'action_type' => $this->action_type,
                'target_user_id' => $this->target_user_id,
                'duration_hours' => $this->duration_hours,
            ],
            'severity' => $this->getSeverityLevel(),
        ]);
    }

    /**
     * Apply warning
     */
    protected function applyWarning(): void
    {
        // Warnings are just recorded, no direct action needed
    }

    /**
     * Apply ban
     */
    protected function applyBan(): void
    {
        if ($this->targetUser) {
            $this->targetUser->update(['is_banned' => true]);
        }
    }

    /**
     * Apply suspension
     */
    protected function applySuspension(): void
    {
        if ($this->targetUser) {
            $this->targetUser->update(['is_suspended' => true]);
        }
    }

    /**
     * Apply content hiding
     */
    protected function applyContentHide(): void
    {
        if ($this->target) {
            $this->target->update(['is_hidden' => true]);
        }
    }

    /**
     * Apply content removal
     */
    protected function applyContentRemoval(): void
    {
        if ($this->target) {
            $this->target->update(['is_removed' => true]);
        }
    }

    /**
     * Apply copyright takedown
     */
    protected function applyCopyrightTakedown(): void
    {
        if ($this->target) {
            $this->target->update([
                'is_removed' => true,
                'removal_reason' => 'copyright_takedown',
            ]);
        }
    }

    /**
     * Get severity level for logging
     */
    protected function getSeverityLevel(): string
    {
        return match ($this->action_type) {
            'warning' => 'info',
            'hide_content' => 'warning',
            'suspend', 'remove_content' => 'high',
            'ban', 'copyright_takedown' => 'critical',
            default => 'info',
        };
    }

    /**
     * Get action badge color
     */
    public function getActionBadgeColor(): string
    {
        return match ($this->action_type) {
            'warning' => 'bg-yellow-100 text-yellow-800',
            'hide_content' => 'bg-orange-100 text-orange-800',
            'suspend', 'remove_content' => 'bg-red-100 text-red-800',
            'ban', 'copyright_takedown' => 'bg-red-600 text-white',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get time remaining
     */
    public function getTimeRemaining(): ?string
    {
        if (!$this->expires_at || $this->hasExpired()) {
            return null;
        }

        return $this->expires_at->diffForHumans();
    }

    /**
     * Scope for active actions
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for expired actions
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now())
            ->where('is_active', true);
    }

    /**
     * Scope for permanent actions
     */
    public function scopePermanent($query)
    {
        return $query->whereNull('expires_at')
            ->where('is_active', true);
    }

    /**
     * Scope for temporary actions
     */
    public function scopeTemporary($query)
    {
        return $query->whereNotNull('expires_at')
            ->where('is_active', true);
    }
}
