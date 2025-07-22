<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use PragmaRX\Google2FA\Google2FA;

/*
|--------------------------------------------------------------------------
| Development Routes
|--------------------------------------------------------------------------
|
| These routes are only available in the local environment for testing
| purposes. They provide quick access to user accounts without requiring
| OAuth provider setup or complex authentication flows.
|
*/

if (app()->environment('local')) {
    // Development quick login
    Route::get('/dev-login/{email}', function ($email) {
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            return redirect('/login')->with('error', 'User not found');
        }
        
        Auth::login($user);
        
        return redirect('/dashboard')->with('success', 'Logged in as ' . $user->name . ' (development mode)');
    })->name('dev-login');
    
    // Development 2FA code generator
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
    
    // Development 2FA test enable
    Route::get('/dev-2fa-enable/{email}', function ($email) {
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            return "User not found";
        }
        
        if (!Auth::check() || Auth::id() !== $user->id) {
            Auth::login($user);
        }
        
        $google2fa = new Google2FA();
        
        // Generate secret if user doesn't have one
        if (!$user->twofa_secret) {
            $secret = $google2fa->generateSecretKey();
            $user->twofa_secret = $secret;
            $user->save();
        }
        
        // Generate current OTP and try to enable 2FA
        $currentCode = $google2fa->getCurrentOtp($user->twofa_secret);
        $isValidCode = $google2fa->verifyKey($user->twofa_secret, $currentCode);
        
        if ($isValidCode) {
            // Enable 2FA
            $user->twofa_enabled = true;
            
            // Generate backup codes
            $backupCodes = [];
            for ($i = 0; $i < 8; $i++) {
                $backupCodes[] = strtoupper(substr(str_replace(['/', '+', '='], '', base64_encode(random_bytes(6))), 0, 8));
            }
            $user->twofa_backup_codes = json_encode($backupCodes);
            $user->save();
            
            $html = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 40px auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; background: #f0f8f0;'>
                <h2 style='color: #2d5016;'>✅ 2FA Successfully Enabled!</h2>
                <p><strong>User:</strong> {$user->name} ({$user->email})</p>
                <p><strong>2FA Status:</strong> <span style='color: green; font-weight: bold;'>Enabled</span></p>
                <p><strong>Used OTP:</strong> <code>{$currentCode}</code></p>
                
                <div style='margin-top: 20px; padding: 15px; background: #fff; border-radius: 5px; border: 1px solid #ccc;'>
                    <h3>🔐 Backup Codes (Save These!):</h3>
                    <div style='display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; font-family: monospace; font-size: 14px;'>";
            
            foreach ($backupCodes as $code) {
                $html .= "<div style='background: #f8f9fa; padding: 8px; border-radius: 3px; text-align: center; border: 1px solid #e9ecef;'>{$code}</div>";
            }
            
            $html .= "
                    </div>
                    <p style='color: #dc3545; font-size: 12px; margin-top: 10px;'><strong>Important:</strong> Save these backup codes in a safe place. You can use them to access your account if you lose your authenticator device.</p>
                </div>
                
                <div style='margin-top: 20px; padding: 15px; background: #e3f2fd; border-radius: 5px;'>
                    <p><strong>Next Steps:</strong></p>
                    <ol>
                        <li><a href='/dashboard'>Go to Dashboard</a> - Check the 2FA status</li>
                        <li><a href='/logout' onclick='fetch(\"/logout\", {method: \"POST\"}); return false;'>Logout</a> - Test 2FA login flow</li>
                        <li><a href='/2fa'>2FA Settings</a> - Manage your 2FA</li>
                    </ol>
                </div>
            </div>";
            
            return $html;
        } else {
            return "Invalid OTP code: {$currentCode}";
        }
    })->name('dev.2fa-enable');
    
    // Development logout
    Route::get('/dev-logout', function () {
        Auth::logout();
        return redirect('/login')->with('success', 'Logged out successfully');
    })->name('dev.logout');
}
