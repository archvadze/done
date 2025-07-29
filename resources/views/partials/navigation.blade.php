<!-- Navigation -->
<nav class="bg-white shadow-sm border-b sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <div class="flex items-center">
                <a href="{{ url('/') }}" class="text-xl font-bold text-gray-900">
                    🎨 Acumen Craft
                </a>
            </div>
            <div class="flex items-center space-x-4">
                <x-locale-switcher />
                <a href="{{ route('evaluations.leaderboard') }}" class="text-gray-600 hover:text-gray-900">🏆
                    {{ __('app.leaderboard') }}</a>
                @auth
                    <a href="{{ route('artworks.create') }}"
                        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                        {{ __('app.upload_artwork') }}
                    </a>
                    <a href="{{ route('users.profile') }}"
                        class="text-gray-600 hover:text-gray-900">{{ __('app.my_profile') }}</a>
                    <a href="{{ route('dashboard') }}"
                        class="text-gray-600 hover:text-gray-900">{{ __('app.dashboard') }}</a>
                @else
                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900">{{ __('app.login') }}</a>
                    <a href="{{ route('register') }}"
                        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">{{ __('app.sign_up') }}</a>
                @endauth
            </div>
        </div>
    </div>
</nav>
