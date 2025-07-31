<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Acumen Craft')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
</head>

<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <!-- Logo -->
                    <div class="flex-shrink-0 flex items-center">
                        <a href="{{ url('/') }}" class="text-xl font-bold text-gray-900">
                            Acumen Craft
                        </a>
                    </div>

                    <!-- Navigation Links -->
                    <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                        <a href="{{ url('/') }}"
                            class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                            {{ __('Home') }}
                        </a>

                        <a href="{{ route('artworks.index') }}"
                            class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                            {{ __('Gallery') }}
                        </a>

                        <a href="{{ route('communities.index') }}"
                            class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                            {{ __('Communities') }}
                        </a>

                        <a href="{{ route('leaderboard') }}"
                            class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                            {{ __('Leaderboard') }}
                        </a>

                        <a href="{{ route('support.index') }}"
                            class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                            {{ __('Support') }}
                        </a>

                        @auth
                            <a href="{{ route('dashboard') }}"
                                class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                                {{ __('Dashboard') }}
                            </a>
                        @endauth
                    </div>
                </div>

                <!-- Right Side -->
                <div class="flex items-center space-x-4">
                    @guest
                        <a href="{{ route('login') }}"
                            class="text-gray-500 hover:text-gray-700 px-3 py-2 text-sm font-medium hidden sm:block">
                            {{ __('Login') }}
                        </a>
                        <a href="{{ route('register') }}"
                            class="bg-blue-600 text-white hover:bg-blue-700 px-3 py-2 text-sm font-medium hidden sm:block rounded">
                            {{ __('Register') }}
                        </a>
                    @else
                        <!-- Quick Create Button -->
                        <a href="{{ route('artworks.create') }}"
                            class="bg-blue-600 text-white hover:bg-blue-700 px-4 py-2 text-sm font-medium transition-colors hidden sm:flex items-center rounded">
                            <i class="fas fa-plus mr-2"></i>{{ __('Create') }}
                        </a>

                        <!-- User Dropdown -->
                        <div class="relative">
                            <button onclick="toggleUserMenu()"
                                class="flex items-center text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <span class="mr-2 text-gray-700">{{ auth()->user()->name }}</span>
                                <div
                                    class="h-8 w-8 bg-gray-200 text-gray-700 rounded-full flex items-center justify-center">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </div>
                            </button>

                            <div id="user-menu"
                                class="hidden origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5">
                                <div class="py-1">
                                    <!-- Profile Section -->
                                    <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase">
                                        {{ __('Profile') }}
                                    </div>
                                    <a href="{{ route('users.profile') }}"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-user mr-2 text-gray-400"></i>{{ __('My Profile') }}
                                    </a>
                                    <a href="{{ route('users.edit') }}"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-edit mr-2 text-gray-400"></i>{{ __('Edit Profile') }}
                                    </a>
                                    <a href="{{ route('users.followers', auth()->user()) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-users mr-2 text-gray-400"></i>{{ __('Followers & Following') }}
                                    </a>

                                    <!-- Content Section -->
                                    <div
                                        class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase border-b border-gray-100 mt-2">
                                        {{ __('My Content') }}
                                    </div>
                                    <a href="{{ route('users.artworks', auth()->user()) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-palette mr-2 text-gray-400"></i>{{ __('My Artworks') }}
                                    </a>
                                    <a href="{{ route('nft.collection') }}"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-gem mr-2 text-gray-400"></i>{{ __('My NFTs') }}
                                    </a>
                                    <a href="{{ route('messages.index') }}"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-envelope mr-2 text-gray-400"></i>{{ __('Messages') }}
                                    </a>

                                    <!-- Payments Section -->
                                    <div
                                        class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase border-b border-gray-100 mt-2">
                                        {{ __('Payments') }}
                                    </div>
                                    <a href="{{ route('payments.show') }}"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-credit-card mr-2 text-gray-400"></i>{{ __('Wallet & Payments') }}
                                    </a>
                                    <a href="{{ route('payments.history') }}"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-history mr-2 text-gray-400"></i>{{ __('Payment History') }}
                                    </a>

                                    <!-- Support Section -->
                                    <div
                                        class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase border-b border-gray-100 mt-2">
                                        {{ __('Support') }}
                                    </div>
                                    <a href="{{ route('support.tickets.index') }}"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-ticket-alt mr-2 text-gray-400"></i>{{ __('My Tickets') }}
                                    </a>
                                    <a href="{{ route('two-factor.show') }}"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-shield-alt mr-2 text-gray-400"></i>{{ __('Security Settings') }}
                                    </a>

                                    <!-- Admin/Moderation Section -->
                                    @if (auth()->user()->isModerator() || auth()->user()->isAdmin())
                                        <div
                                            class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase border-b border-gray-100 mt-2">
                                            {{ __('Management') }}
                                        </div>
                                        @if (auth()->user()->isModerator())
                                            <a href="{{ route('moderation.dashboard') }}"
                                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <i class="fas fa-gavel mr-2 text-blue-500"></i>{{ __('Moderation Panel') }}
                                            </a>
                                        @endif
                                        @if (auth()->user()->isAdmin())
                                            <a href="{{ route('admin.dashboard') }}"
                                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <i class="fas fa-cogs mr-2 text-purple-500"></i>{{ __('Admin Panel') }}
                                            </a>
                                        @endif
                                    @endif

                                    <!-- Logout -->
                                    <div class="border-t border-gray-100 mt-2"></div>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit"
                                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i class="fas fa-sign-out-alt mr-2 text-red-500"></i>{{ __('Logout') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Balance Display -->
                        <div class="text-sm text-gray-600 hidden sm:block">
                            {{ __('Balance:') }}
                            <span class="font-semibold text-green-600">
                                {{ number_format(auth()->user()->balance ?? 0, 2) }}
                                {{ auth()->user()->balance_currency ?? 'USD' }}
                            </span>
                        </div>
                    @endguest

                    <!-- Mobile menu button -->
                    <button type="button"
                        class="sm:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500"
                        onclick="toggleMobileMenu()">
                        <span class="sr-only">{{ __('Open main menu') }}</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu (hidden by default) -->
        <div class="sm:hidden hidden" id="mobile-menu">
            <div class="px-2 pt-2 pb-3 space-y-1 bg-white border-t border-gray-200">
                <a href="{{ url('/') }}"
                    class="text-gray-500 hover:text-gray-700 block px-3 py-2 rounded-md text-base font-medium">
                    {{ __('Home') }}
                </a>
                <a href="{{ route('artworks.index') }}"
                    class="text-gray-500 hover:text-gray-700 block px-3 py-2 rounded-md text-base font-medium">
                    {{ __('Gallery') }}
                </a>
                <a href="{{ route('communities.index') }}"
                    class="text-gray-500 hover:text-gray-700 block px-3 py-2 rounded-md text-base font-medium">
                    {{ __('Communities') }}
                </a>
                <a href="{{ route('leaderboard') }}"
                    class="text-gray-500 hover:text-gray-700 block px-3 py-2 rounded-md text-base font-medium">
                    {{ __('Leaderboard') }}
                </a>
                <a href="{{ route('support.index') }}"
                    class="text-gray-500 hover:text-gray-700 block px-3 py-2 rounded-md text-base font-medium">
                    {{ __('Support') }}
                </a>

                @auth
                    <div class="border-t border-gray-200 pt-2">
                        <a href="{{ route('dashboard') }}"
                            class="text-gray-500 hover:text-gray-700 block px-3 py-2 rounded-md text-base font-medium">
                            <i class="fas fa-tachometer-alt mr-2"></i>{{ __('Dashboard') }}
                        </a>
                        <a href="{{ route('artworks.create') }}"
                            class="bg-blue-500 hover:bg-blue-600 text-white block px-3 py-2 rounded-md text-base font-medium">
                            <i class="fas fa-plus mr-2"></i>{{ __('Create Artwork') }}
                        </a>
                        <a href="{{ route('users.artworks', auth()->user()) }}"
                            class="text-gray-500 hover:text-gray-700 block px-3 py-2 rounded-md text-base font-medium">
                            <i class="fas fa-palette mr-2"></i>{{ __('My Artworks') }}
                        </a>
                        <a href="{{ route('nft.collection') }}"
                            class="text-gray-500 hover:text-gray-700 block px-3 py-2 rounded-md text-base font-medium">
                            <i class="fas fa-gem mr-2"></i>{{ __('My NFTs') }}
                        </a>
                        <a href="{{ route('messages.index') }}"
                            class="text-gray-500 hover:text-gray-700 block px-3 py-2 rounded-md text-base font-medium">
                            <i class="fas fa-envelope mr-2"></i>{{ __('Messages') }}
                        </a>
                        <a href="{{ route('payments.show') }}"
                            class="text-gray-500 hover:text-gray-700 block px-3 py-2 rounded-md text-base font-medium">
                            <i class="fas fa-credit-card mr-2"></i>{{ __('Wallet') }}
                        </a>

                        @if (auth()->user()->isModerator() || auth()->user()->isAdmin())
                            <div class="border-t border-gray-200 pt-2">
                                @if (auth()->user()->isModerator())
                                    <a href="{{ route('moderation.dashboard') }}"
                                        class="text-blue-600 hover:text-blue-800 block px-3 py-2 rounded-md text-base font-medium">
                                        <i class="fas fa-gavel mr-2"></i>{{ __('Moderation') }}
                                    </a>
                                @endif
                                @if (auth()->user()->isAdmin())
                                    <a href="{{ route('admin.dashboard') }}"
                                        class="text-purple-600 hover:text-purple-800 block px-3 py-2 rounded-md text-base font-medium">
                                        <i class="fas fa-cogs mr-2"></i>{{ __('Admin Panel') }}
                                    </a>
                                @endif
                            </div>
                        @endif

                        <div class="border-t border-gray-200 pt-2">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="text-red-600 hover:text-red-800 block w-full text-left px-3 py-2 rounded-md text-base font-medium">
                                    <i class="fas fa-sign-out-alt mr-2"></i>{{ __('Logout') }}
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="border-t border-gray-200 pt-2">
                        <a href="{{ route('login') }}"
                            class="text-gray-500 hover:text-gray-700 block px-3 py-2 rounded-md text-base font-medium">
                            {{ __('Login') }}
                        </a>
                        <a href="{{ route('register') }}"
                            class="bg-blue-600 hover:bg-blue-700 text-white block px-3 py-2 rounded-md text-base font-medium">
                            {{ __('Register') }}
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t mt-12">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="text-center text-gray-500 text-sm">
                &copy; {{ date('Y') }} Acumen Craft. {{ __('All rights reserved.') }}
            </div>
        </div>
    </footer>

    <!-- Alpine.js for dropdowns -->
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <!-- Navigation toggle scripts -->
    <script>
        function toggleMobileMenu() {
            const mobileMenu = document.getElementById('mobile-menu');
            mobileMenu.classList.toggle('hidden');
        }

        function toggleUserMenu() {
            const userMenu = document.getElementById('user-menu');
            userMenu.classList.toggle('hidden');
        }

        // Close user menu when clicking outside
        document.addEventListener('click', function(event) {
            const userMenu = document.getElementById('user-menu');
            const userButton = event.target.closest('button[onclick="toggleUserMenu()"]');

            if (!userButton && userMenu && !userMenu.contains(event.target)) {
                userMenu.classList.add('hidden');
            }
        });
    </script>
</body>

</html>
