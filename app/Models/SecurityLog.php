<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SecurityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'event_type',
        'event_category',
        'description',
        'ip_address',
        'user_agent',
        'url',
        'method',
        'status_code',
        'metadata',
        'severity',
        'source_type',
        'source_id',
        'session_id',
        'request_id',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user associated with this log entry
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the source model (if applicable)
     */
    public function source(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Create a security log entry
     */
    public static function log(array $data): self
    {
        // Automatically capture request data if not provided
        if (request()) {
            $data = array_merge([
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'url' => request()->fullUrl(),
                'method' => request()->method(),
                'session_id' => session()->getId(),
            ], $data);
        }

        return self::create($data);
    }

    /**
     * Log authentication events
     */
    public static function logAuth(string $eventType, ?User $user = null, array $metadata = []): self
    {
        return self::log([
            'user_id' => $user?->id,
            'event_type' => $eventType,
            'event_category' => 'authentication',
            'description' => self::getAuthDescription($eventType),
            'metadata' => $metadata,
            'severity' => self::getAuthSeverity($eventType),
        ]);
    }

    /**
     * Log moderation events
     */
    public static function logModeration(string $eventType, User $moderator, array $metadata = []): self
    {
        return self::log([
            'user_id' => $moderator->id,
            'event_type' => $eventType,
            'event_category' => 'moderation',
            'description' => self::getModerationDescription($eventType),
            'metadata' => $metadata,
            'severity' => self::getModerationSeverity($eventType),
        ]);
    }

    /**
     * Log security events
     */
    public static function logSecurity(string $eventType, ?User $user = null, array $metadata = []): self
    {
        return self::log([
            'user_id' => $user?->id,
            'event_type' => $eventType,
            'event_category' => 'security',
            'description' => self::getSecurityDescription($eventType),
            'metadata' => $metadata,
            'severity' => self::getSecuritySeverity($eventType),
        ]);
    }

    /**
     * Log system events
     */
    public static function logSystem(string $eventType, array $metadata = []): self
    {
        return self::log([
            'event_type' => $eventType,
            'event_category' => 'system',
            'description' => self::getSystemDescription($eventType),
            'metadata' => $metadata,
            'severity' => 'info',
        ]);
    }

    /**
     * Get authentication event descriptions
     */
    protected static function getAuthDescription(string $eventType): string
    {
        return match ($eventType) {
            'login_success' => 'User logged in successfully',
            'login_failed' => 'Failed login attempt',
            'logout' => 'User logged out',
            'password_changed' => 'User password changed',
            'email_changed' => 'User email changed',
            '2fa_enabled' => 'Two-factor authentication enabled',
            '2fa_disabled' => 'Two-factor authentication disabled',
            'account_locked' => 'Account locked due to failed attempts',
            'account_unlocked' => 'Account unlocked',
            'password_reset_requested' => 'Password reset requested',
            'password_reset_completed' => 'Password reset completed',
            default => "Authentication event: {$eventType}",
        };
    }

    /**
     * Get moderation event descriptions
     */
    protected static function getModerationDescription(string $eventType): string
    {
        return match ($eventType) {
            'report_created' => 'Moderation report created',
            'report_assigned' => 'Report assigned to moderator',
            'report_resolved' => 'Report resolved',
            'report_dismissed' => 'Report dismissed',
            'action_applied' => 'Moderation action applied',
            'action_reversed' => 'Moderation action reversed',
            'content_hidden' => 'Content hidden by moderator',
            'content_removed' => 'Content removed by moderator',
            'user_banned' => 'User banned',
            'user_suspended' => 'User suspended',
            'copyright_takedown' => 'Copyright takedown executed',
            default => "Moderation event: {$eventType}",
        };
    }

    /**
     * Get security event descriptions
     */
    protected static function getSecurityDescription(string $eventType): string
    {
        return match ($eventType) {
            'suspicious_activity' => 'Suspicious activity detected',
            'brute_force_attempt' => 'Brute force attack detected',
            'unauthorized_access' => 'Unauthorized access attempt',
            'data_breach_attempt' => 'Potential data breach attempt',
            'malicious_upload' => 'Malicious file upload detected',
            'sql_injection_attempt' => 'SQL injection attempt detected',
            'xss_attempt' => 'XSS attack attempt detected',
            'csrf_attack' => 'CSRF attack detected',
            'privilege_escalation' => 'Privilege escalation attempt',
            'api_abuse' => 'API abuse detected',
            default => "Security event: {$eventType}",
        };
    }

    /**
     * Get system event descriptions
     */
    protected static function getSystemDescription(string $eventType): string
    {
        return match ($eventType) {
            'backup_created' => 'System backup created',
            'backup_restored' => 'System backup restored',
            'maintenance_start' => 'Maintenance mode started',
            'maintenance_end' => 'Maintenance mode ended',
            'cache_cleared' => 'System cache cleared',
            'config_updated' => 'System configuration updated',
            'migration_run' => 'Database migration executed',
            default => "System event: {$eventType}",
        };
    }

    /**
     * Get authentication severity levels
     */
    protected static function getAuthSeverity(string $eventType): string
    {
        return match ($eventType) {
            'login_failed', 'account_locked' => 'warning',
            'password_changed', 'email_changed', '2fa_enabled', '2fa_disabled' => 'info',
            'login_success', 'logout', 'password_reset_completed' => 'info',
            'account_unlocked' => 'info',
            default => 'info',
        };
    }

    /**
     * Get moderation severity levels
     */
    protected static function getModerationSeverity(string $eventType): string
    {
        return match ($eventType) {
            'report_created', 'report_assigned' => 'info',
            'report_resolved', 'report_dismissed' => 'info',
            'content_hidden', 'action_applied' => 'warning',
            'content_removed', 'user_suspended' => 'high',
            'user_banned', 'copyright_takedown' => 'critical',
            'action_reversed' => 'warning',
            default => 'info',
        };
    }

    /**
     * Get security severity levels
     */
    protected static function getSecuritySeverity(string $eventType): string
    {
        return match ($eventType) {
            'suspicious_activity', 'api_abuse' => 'warning',
            'brute_force_attempt', 'unauthorized_access' => 'high',
            'data_breach_attempt', 'malicious_upload', 'privilege_escalation' => 'critical',
            'sql_injection_attempt', 'xss_attempt', 'csrf_attack' => 'critical',
            default => 'warning',
        };
    }

    /**
     * Get severity badge color
     */
    public function getSeverityBadgeColor(): string
    {
        return match ($this->severity) {
            'info' => 'bg-blue-100 text-blue-800',
            'warning' => 'bg-yellow-100 text-yellow-800',
            'high' => 'bg-orange-100 text-orange-800',
            'critical' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Scope for critical events
     */
    public function scopeCritical($query)
    {
        return $query->where('severity', 'critical');
    }

    /**
     * Scope for high severity events
     */
    public function scopeHigh($query)
    {
        return $query->where('severity', 'high');
    }

    /**
     * Scope for warning events
     */
    public function scopeWarning($query)
    {
        return $query->where('severity', 'warning');
    }

    /**
     * Scope for recent events
     */
    public function scopeRecent($query, int $hours = 24)
    {
        return $query->where('created_at', '>=', now()->subHours($hours));
    }

    /**
     * Scope by event category
     */
    public function scopeCategory($query, string $category)
    {
        return $query->where('event_category', $category);
    }

    /**
     * Scope by event type
     */
    public function scopeEventType($query, string $eventType)
    {
        return $query->where('event_type', $eventType);
    }

    /**
     * Scope by user
     */
    public function scopeForUser($query, User $user)
    {
        return $query->where('user_id', $user->id);
    }

    /**
     * Scope by IP address
     */
    public function scopeFromIp($query, string $ipAddress)
    {
        return $query->where('ip_address', $ipAddress);
    }
}
