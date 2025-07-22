<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
use PragmaRX\Google2FA\Google2FA;

/*
|--------------------------------------------------------------------------
| Development Test Routes
|--------------------------------------------------------------------------
*/

if (app()->environment('local')) {
    Route::get('/dev-2fa-code/{email}', function ($email) {
        $user = User::where('email', $email)->first();

        if (!$user) {
            return "User not found";
        }

        $google2fa = new Google2FA();

        // Generate secret if user doesn't have one
        if (!$user->twofa_secret) {
            $secret = $google2fa->generateSecretKey();
            $user->twofa_secret = $secret;
            $user->save();
        }

        // Generate current OTP
        $currentCode = $google2fa->getCurrentOtp($user->twofa_secret);

        $html = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 40px auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px;'>
            <h2>2FA Development Helper</h2>
            <p><strong>User:</strong> {$user->name} ({$user->email})</p>
            <p><strong>2FA Status:</strong> " . ($user->twofa_enabled ? 'Enabled' : 'Disabled') . "</p>
            <p><strong>Secret Key:</strong> <code>{$user->twofa_secret}</code></p>
            <p><strong>Current OTP:</strong> <span style='font-size: 24px; font-weight: bold; color: #007bff; font-family: monospace;'>{$currentCode}</span></p>
            <div style='margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 5px;'>
                <p><strong>Instructions:</strong></p>
                <ol>
                    <li>Go to <a href='/2fa'>/2fa</a> to see the QR code</li>
                    <li>Use the OTP code above: <strong>{$currentCode}</strong></li>
                    <li>Enable 2FA by submitting the form</li>
                </ol>
            </div>
            <p style='margin-top: 20px;'><a href='/2fa'>Go to 2FA Settings</a></p>
        </div>";

        return $html;
    })->name('dev.2fa-code');
}
