<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Two-Factor Authentication - Acumen Craft</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen py-8">
    <div class="max-w-4xl mx-auto px-4">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Two-Factor Authentication</h1>
                <p class="text-gray-600">Secure your account with an additional layer of protection</p>
            </div>

            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid md:grid-cols-2 gap-8">
                <!-- Left Column: Status and Controls -->
                <div>
                    <div class="mb-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Current Status</h2>
                        <div
                            class="p-4 rounded-lg {{ $user->twofa_enabled ? 'bg-green-50 border border-green-200' : 'bg-yellow-50 border border-yellow-200' }}">
                            @if ($user->twofa_enabled)
                                <div class="flex items-center text-green-800">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <span class="font-semibold">2FA is enabled</span>
                                </div>
                                <p class="text-green-700 text-sm mt-1">Your account is protected by two-factor
                                    authentication</p>
                            @else
                                <div class="flex items-center text-yellow-800">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <span class="font-semibold">2FA is disabled</span>
                                </div>
                                <p class="text-yellow-700 text-sm mt-1">Enable 2FA for additional security</p>
                            @endif
                        </div>
                    </div>

                    <!-- Actions -->
                    @if ($user->twofa_enabled)
                        <!-- Disable 2FA Form -->
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">Disable Two-Factor Authentication</h3>
                            <form method="POST" action="{{ route('two-factor.disable') }}" class="space-y-4">
                                @csrf
                                @if ($user->password)
                                    <div>
                                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                                            Enter your password to disable 2FA
                                        </label>
                                        <input type="password" id="password" name="password" required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                                    </div>
                                @else
                                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
                                        <p class="text-blue-700 text-sm">
                                            <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            You signed up via social login. No password verification needed.
                                        </p>
                                    </div>
                                @endif
                                <button type="submit"
                                    class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                    Disable 2FA
                                </button>
                            </form>
                        </div> <!-- Backup Codes -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">Recovery Codes</h3>
                            <p class="text-gray-600 text-sm mb-3">
                                Generate backup codes for account recovery if you lose access to your authenticator app.
                            </p>
                            <a href="{{ route('two-factor.backup-codes') }}"
                                class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                                Generate Backup Codes
                            </a>
                        </div>
                    @else
                        <!-- Enable 2FA Form -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">Enable Two-Factor Authentication</h3>
                            <p class="text-gray-600 text-sm mb-4">
                                After scanning the QR code with your authenticator app, enter the 6-digit code to verify
                                and enable 2FA.
                            </p>
                            <form method="POST" action="{{ route('two-factor.enable') }}" class="space-y-4">
                                @csrf
                                <div>
                                    <label for="code" class="block text-sm font-medium text-gray-700 mb-1">
                                        Verification Code
                                    </label>
                                    <input type="text" id="code" name="code" maxlength="6"
                                        pattern="[0-9]{6}" required placeholder="123456"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-center text-xl tracking-widest">
                                </div>
                                <button type="submit"
                                    class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                    Enable 2FA
                                </button>
                            </form>
                        </div>
                    @endif
                </div>

                <!-- Right Column: QR Code and Instructions -->
                @if (!$user->twofa_enabled)
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Setup Instructions</h2>

                        <!-- QR Code -->
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-3">1. Scan QR Code</h3>
                            <div class="bg-white p-4 rounded-lg border-2 border-gray-200 inline-block">
                                {!! $qrCode !!}
                            </div>
                        </div>

                        <!-- Manual Setup -->
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-3">2. Or enter manually</h3>
                            <div class="bg-gray-50 p-3 rounded-md">
                                <p class="text-sm text-gray-600 mb-2">Secret Key:</p>
                                <code
                                    class="bg-white px-2 py-1 rounded text-sm font-mono border">{{ $secret }}</code>
                            </div>
                        </div>

                        <!-- App Recommendations -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-3">3. Recommended Apps</h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex items-center">
                                    <div class="w-2 h-2 bg-blue-500 rounded-full mr-2"></div>
                                    <span>Google Authenticator (iOS/Android)</span>
                                </div>
                                <div class="flex items-center">
                                    <div class="w-2 h-2 bg-blue-500 rounded-full mr-2"></div>
                                    <span>Authy (iOS/Android/Desktop)</span>
                                </div>
                                <div class="flex items-center">
                                    <div class="w-2 h-2 bg-blue-500 rounded-full mr-2"></div>
                                    <span>Microsoft Authenticator</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Navigation -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <a href="/dashboard" class="text-blue-600 hover:text-blue-800 font-medium">
                    ← Back to Dashboard
                </a>
            </div>
        </div>
    </div>
</body>

</html>
