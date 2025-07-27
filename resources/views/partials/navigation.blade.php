<!-- Navigation -->
<nav class="nav-background sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <div class="flex items-center">
                <a href="{{ url('/') }}" class="flex items-center space-x-3">
                    <img src="/logo.svg" alt="Acumen Craft Logo" class="h-8 w-auto">
                    <span class="logo-text text-xl font-bold">Acumen Craft</span>
                </a>
            </div>
            <div class="flex items-center space-x-4">
                <x-locale-switcher />
                <a href="{{ route('evaluations.leaderboard') }}" class="btn-text">
                    🏆 {{ __('app.leaderboard') }}
                </a>
                @auth
                    <a href="{{ route('artworks.create') }}" class="btn-primary px-4 py-2 rounded-md">
                        {{ __('app.upload_artwork') }}
                    </a>
                    <a href="{{ route('users.profile') }}" class="btn-text">
                        {{ __('app.my_profile') }}
                    </a>
                    <a href="{{ route('dashboard') }}" class="btn-text">
                        {{ __('app.dashboard') }}
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn-text">
                        {{ __('app.login') }}
                    </a>
                    <a href="{{ route('register') }}" class="btn-primary px-4 py-2 rounded-md">
                        {{ __('app.sign_up') }}
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>
