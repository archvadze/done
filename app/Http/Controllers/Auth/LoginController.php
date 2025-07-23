<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Show login form
     */
    public function show()
    {
        return view('auth.login');
    }

    /**
     * Handle login attempt
     */
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            $credentials = $request->only('email', 'password');
            $remember = $request->has('remember') ? true : false;

            if (Auth::attempt($credentials, $remember)) {
                $request->session()->regenerate();

                $user = Auth::user();

                // Check if user has 2FA enabled
                if ($user && $user->twofa_enabled) {
                    // Don't complete login yet - redirect to 2FA verification
                    session(['2fa_user_id' => $user->id]);
                    Auth::logout();

                    return redirect()->route('two-factor.verify')
                        ->with('success', 'Please enter your 2FA verification code.');
                }

                return redirect()->intended('/dashboard')
                    ->with('success', 'Welcome back!');
            }

            return redirect()->back()
                ->withErrors(['email' => 'The provided credentials do not match our records.'])
                ->withInput($request->except('password'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['email' => 'Login error: ' . $e->getMessage()])
                ->withInput($request->except('password'));
        }
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'You have been logged out successfully.');
    }
}
