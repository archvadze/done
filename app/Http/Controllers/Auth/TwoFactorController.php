<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class TwoFactorController extends Controller
{
    /**
     * Show the 2FA setup page
     */
    public function show()
    {
        $user = Auth::user();
        $google2fa = new Google2FA();

        if (!$user->twofa_secret) {
            // Generate a new secret for the user
            $secret = $google2fa->generateSecretKey();
            $user->twofa_secret = $secret;
            $user->save();
        }

        // Generate QR Code
        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $user->twofa_secret
        );

        $qrCode = $this->generateQRCode($qrCodeUrl);

        return view('auth.two-factor', [
            'user' => $user,
            'secret' => $user->twofa_secret,
            'qrCode' => $qrCode
        ]);
    }

    /**
     * Enable 2FA for the user
     */
    public function enable(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6'
        ]);

        $user = Auth::user();
        $google2fa = new Google2FA();

        $valid = $google2fa->verifyKey($user->twofa_secret, $request->code);

        if ($valid) {
            $user->twofa_enabled = true;
            $user->save();

            return redirect()->route('two-factor.show')
                ->with('success', '2FA has been enabled successfully!');
        }

        return redirect()->back()
            ->with('error', 'Invalid verification code. Please try again.');
    }

    /**
     * Disable 2FA for the user
     */
    public function disable(Request $request)
    {
        $user = Auth::user();

        // If user doesn't have password (OAuth only), allow direct disable
        if (!$user->password) {
            $user->twofa_enabled = false;
            $user->twofa_secret = null;
            $user->save();

            return redirect()->route('two-factor.show')
                ->with('success', '2FA has been disabled successfully!');
        }

        // For users with passwords, require password verification
        $request->validate([
            'password' => 'required|string'
        ]);

        if (!password_verify($request->password, $user->password)) {
            return redirect()->back()
                ->with('error', 'Invalid password. Please try again.');
        }

        $user->twofa_enabled = false;
        $user->twofa_secret = null;
        $user->save();

        return redirect()->route('two-factor.show')
            ->with('success', '2FA has been disabled successfully!');
    }

    /**
     * Show 2FA verification form
     */
    public function verify()
    {
        return view('auth.two-factor-verify');
    }

    /**
     * Verify the 2FA code during login
     */
    public function verifyCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6'
        ]);

        $user = Auth::user();
        $google2fa = new Google2FA();

        $valid = $google2fa->verifyKey($user->twofa_secret, $request->code);

        if ($valid) {
            // Mark 2FA as verified for this session
            session(['2fa_verified' => true]);

            return redirect()->intended('/dashboard')
                ->with('success', 'Two-factor authentication verified successfully!');
        }

        return redirect()->back()
            ->with('error', 'Invalid verification code. Please try again.');
    }

    /**
     * Generate backup codes
     */
    public function generateBackupCodes()
    {
        $user = Auth::user();
        $codes = [];

        for ($i = 0; $i < 8; $i++) {
            $codes[] = substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 8);
        }

        $user->twofa_backup_codes = json_encode($codes);
        $user->save();

        return view('auth.backup-codes', [
            'codes' => $codes
        ]);
    }

    /**
     * Generate QR Code SVG
     */
    private function generateQRCode($url): string
    {
        $renderer = new ImageRenderer(
            new RendererStyle(400),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);
        return $writer->writeString($url);
    }
}
