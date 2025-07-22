<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\LinkedAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    /**
     * Supported OAuth providers
     */
    private const SUPPORTED_PROVIDERS = ['google', 'github', 'facebook', 'apple'];

    /**
     * Redirect to provider OAuth page
     */
    public function redirectToProvider(string $provider)
    {
        if (!in_array($provider, self::SUPPORTED_PROVIDERS)) {
            return redirect()->route('login')->with('error', 'Unsupported provider');
        }

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Handle provider callback
     */
    public function handleProviderCallback(string $provider)
    {
        if (!in_array($provider, self::SUPPORTED_PROVIDERS)) {
            return redirect()->route('login')->with('error', 'Unsupported provider');
        }

        try {
            $socialUser = Socialite::driver($provider)->user();

            // Check if user already exists with this email
            $user = User::where('email', $socialUser->getEmail())->first();

            if ($user) {
                // User exists - link account if not already linked
                $this->linkAccountToUser($user, $provider, $socialUser);
                Auth::login($user);
            } else {
                // Create new user
                $user = $this->createUserFromSocial($provider, $socialUser);
                Auth::login($user);
            }

            return redirect()->intended('/dashboard')->with('success', 'Successfully logged in!');
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    /**
     * Create new user from social provider
     */
    private function createUserFromSocial(string $provider, $socialUser): User
    {
        $user = User::create([
            'name' => $socialUser->getName(),
            'email' => $socialUser->getEmail(),
            'provider' => $provider,
            'provider_id' => $socialUser->getId(),
            'oauth_avatar' => $socialUser->getAvatar(),
            'oauth_email_verified' => true,
            'email_verified_at' => now(),
            'password' => null, // OAuth users don't have passwords initially
        ]);

        // Create linked account record
        LinkedAccount::create([
            'user_id' => $user->id,
            'provider' => $provider,
            'provider_id' => $socialUser->getId(),
            'email' => $socialUser->getEmail(),
            'avatar_url' => $socialUser->getAvatar(),
        ]);

        return $user;
    }

    /**
     * Link social account to existing user
     */
    private function linkAccountToUser(User $user, string $provider, $socialUser): void
    {
        // Check if this provider is already linked
        $linkedAccount = LinkedAccount::where('user_id', $user->id)
            ->where('provider', $provider)
            ->first();

        if (!$linkedAccount) {
            LinkedAccount::create([
                'user_id' => $user->id,
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
                'email' => $socialUser->getEmail(),
                'avatar_url' => $socialUser->getAvatar(),
            ]);
        }
    }

    /**
     * Unlink social provider from user account
     */
    public function unlinkProvider(Request $request, string $provider)
    {
        $user = Auth::user();

        // Don't allow unlinking if it's the only authentication method
        if (!$user->password && $user->linkedAccounts()->count() <= 1) {
            return redirect()->back()->with('error', 'Cannot unlink the only authentication method. Please set a password first.');
        }

        LinkedAccount::where('user_id', $user->id)
            ->where('provider', $provider)
            ->delete();

        return redirect()->back()->with('success', ucfirst($provider) . ' account unlinked successfully.');
    }
}
