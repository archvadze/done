<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

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
        return $this->avatar_path
            ? asset('storage/avatars/' . $this->avatar_path)
            : $this->oauth_avatar;
    }
}
