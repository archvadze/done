<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('Logout') }} - {{ config('app.name', 'ArtCraft') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        <!-- Logo -->
        <div class="mb-6">
            <a href="/" class="flex items-center space-x-2">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
                    <span class="text-white font-bold text-xl">AC</span>
                </div>
                <span class="text-2xl font-bold text-gray-900">ArtCraft</span>
            </a>
        </div>

        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <div class="text-center">
                <!-- Logout Icon -->
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </div>

                <h2 class="text-xl font-semibold text-gray-900 mb-2">{{ __('Confirm Logout') }}</h2>
                <p class="text-gray-600 mb-6">{{ __('Are you sure you want to log out from ArtCraft?') }}</p>

                <div class="flex space-x-4">
                    <!-- Cancel Button -->
                    <a href="{{ url()->previous() ?: '/dashboard' }}" 
                       class="flex-1 inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        {{ __('Cancel') }}
                    </a>

                    <!-- Logout Form -->
                    <form method="POST" action="{{ route('logout') }}" class="flex-1">
                        @csrf
                        <button type="submit" 
                                class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            {{ __('Logout') }}
                        </button>
                    </form>
                </div>

                <!-- Additional Info -->
                <div class="mt-6 text-center">
                    <p class="text-xs text-gray-500">
                        {{ __('Logged in as') }}: <strong>{{ Auth::user()->name }}</strong>
                    </p>
                </div>
            </div>
        </div>

        <!-- Quick Navigation -->
        <div class="mt-6 text-center">
            <div class="flex justify-center space-x-4 text-sm">
                <a href="/" class="text-gray-600 hover:text-gray-900">{{ __('Home') }}</a>
                <span class="text-gray-400">•</span>
                <a href="/dashboard" class="text-gray-600 hover:text-gray-900">{{ __('Dashboard') }}</a>
                <span class="text-gray-400">•</span>
                <a href="/artworks" class="text-gray-600 hover:text-gray-900">{{ __('Gallery') }}</a>
            </div>
        </div>
    </div>

    <script>
        // Auto-focus on logout button for better UX
        document.addEventListener('DOMContentLoaded', function() {
            const logoutButton = document.querySelector('button[type="submit"]');
            if (logoutButton) {
                logoutButton.focus();
            }
        });

        // Handle keyboard shortcuts
        document.addEventListener('keydown', function(event) {
            // ESC key - cancel logout
            if (event.key === 'Escape') {
                window.history.back();
            }
            // Enter key - confirm logout
            if (event.key === 'Enter' && event.target.tagName !== 'BUTTON') {
                const logoutForm = document.querySelector('form[action="{{ route('logout') }}"]');
                if (logoutForm) {
                    logoutForm.submit();
                }
            }
        });
    </script>
</body>
</html>
