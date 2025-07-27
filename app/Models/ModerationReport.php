<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ModerationReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'reporter_id',
        'reported_user_id',
        'reportable_type',
        'reportable_id',
        'reason',
        'description',
        'status',
        'priority',
        'assigned_to',
        'evidence',
        'reviewed_at',
        'resolved_at',
        'resolution_notes',
    ];

    protected $casts = [
        'evidence' => 'array',
        'resolution_notes' => 'array',
        'reviewed_at' => 'datetime',
        'resolved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user who reported this
     */
    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    /**
     * Get the reported user
     */
    public function reportedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_user_id');
    }

    /**
     * Get the moderator assigned to this report
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the reportable model (artwork, comment, etc.)
     */
    public function reportable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get moderation actions taken for this report
     */
    public function actions(): HasMany
    {
        return $this->hasMany(ModerationAction::class, 'report_id');
    }

    /**
     * Check if report is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if report is under review
     */
    public function isUnderReview(): bool
    {
        return $this->status === 'under_review';
    }

    /**
     * Check if report is resolved
     */
    public function isResolved(): bool
    {
        return $this->status === 'resolved';
    }

    /**
     * Check if report is dismissed
     */
    public function isDismissed(): bool
    {
        return $this->status === 'dismissed';
    }

    /**
     * Assign report to moderator
     */
    public function assignTo(User $moderator): void
    {
        $this->update([
            'assigned_to' => $moderator->id,
            'status' => $this->status === 'pending' ? 'under_review' : $this->status,
            'reviewed_at' => $this->reviewed_at ?? now(),
        ]);

        // Log the assignment
        SecurityLog::create([
            'user_id' => $moderator->id,
            'event_type' => 'report_assigned',
            'event_category' => 'moderation',
            'description' => "Report #{$this->id} assigned to moderator",
            'metadata' => [
                'report_id' => $this->id,
                'reason' => $this->reason,
            ],
            'severity' => 'info',
        ]);
    }

    /**
     * Mark report as resolved
     */
    public function resolve(array $notes = []): void
    {
        $this->update([
            'status' => 'resolved',
            'resolved_at' => now(),
            'resolution_notes' => $notes,
        ]);

        SecurityLog::create([
            'user_id' => $this->assigned_to,
            'event_type' => 'report_resolved',
            'event_category' => 'moderation',
            'description' => "Report #{$this->id} resolved",
            'metadata' => [
                'report_id' => $this->id,
                'reason' => $this->reason,
                'resolution_notes' => $notes,
            ],
            'severity' => 'info',
        ]);
    }

    /**
     * Dismiss report
     */
    public function dismiss(string $reason = ''): void
    {
        $this->update([
            'status' => 'dismissed',
            'resolved_at' => now(),
            'resolution_notes' => ['dismissed_reason' => $reason],
        ]);

        SecurityLog::create([
            'user_id' => $this->assigned_to,
            'event_type' => 'report_dismissed',
            'event_category' => 'moderation',
            'description' => "Report #{$this->id} dismissed",
            'metadata' => [
                'report_id' => $this->id,
                'reason' => $this->reason,
                'dismiss_reason' => $reason,
            ],
            'severity' => 'info',
        ]);
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeColor(): string
    {
        return match ($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'under_review' => 'bg-blue-100 text-blue-800',
            'resolved' => 'bg-green-100 text-green-800',
            'dismissed' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get priority badge color
     */
    public function getPriorityBadgeColor(): string
    {
        return match ($this->priority) {
            'urgent' => 'bg-red-100 text-red-800',
            'high' => 'bg-orange-100 text-orange-800',
            'normal' => 'bg-blue-100 text-blue-800',
            'low' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Scope for pending reports
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for assigned reports
     */
    public function scopeAssigned($query)
    {
        return $query->whereNotNull('assigned_to');
    }

    /**
     * Scope for unassigned reports
     */
    public function scopeUnassigned($query)
    {
        return $query->whereNull('assigned_to');
    }

    /**
     * Scope for high priority reports
     */
    public function scopeHighPriority($query)
    {
        return $query->whereIn('priority', ['high', 'urgent']);
    }
}
