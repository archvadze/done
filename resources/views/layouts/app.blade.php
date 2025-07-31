<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Acumen Craft')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
                            {{ __('Artworks') }}
                        </a>

                        <a href="{{ route('communities.index') }}" 
                           class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                            {{ __('Communities') }}
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

                            <a href="{{ route('nft.collection') }}" 
                               class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                                {{ __('My NFTs') }}
                            </a>

                            <a href="{{ route('messages.index') }}" 
                               class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                                {{ __('Messages') }}
                            </a>
                        @endauth
                    </div>
                </div>

                <!-- Right Side -->
                <div class="flex items-center space-x-4">
                    @guest
                        <a href="{{ route('login') }}" 
                           class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium hidden sm:block">
                            {{ __('Login') }}
                        </a>
                        <a href="{{ route('register') }}" 
                           class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-md text-sm font-medium hidden sm:block">
                            {{ __('Register') }}
                        </a>
                    @else
                        <!-- User Dropdown -->
                        <div class="relative">
                            <button onclick="toggleUserMenu()" 
                                    class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <span class="mr-2 text-gray-700">{{ auth()->user()->name }}</span>
                                <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </div>
                            </button>

                            <div id="user-menu" class="hidden origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5">
                                <div class="py-1">
                                    <a href="{{ route('users.profile') }}" 
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        {{ __('Profile') }}
                                    </a>
                                    <a href="{{ route('users.artworks', auth()->user()) }}" 
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        {{ __('My Artworks') }}
                                    </a>
                                    <a href="{{ route('payments.show') }}" 
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        {{ __('Payments') }}
                                    </a>
                                    <a href="{{ route('payments.history') }}" 
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        {{ __('Payment History') }}
                                    </a>
                                    <a href="{{ route('two-factor.show') }}" 
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        {{ __('Security Settings') }}
                                    </a>
                                    
                                    @if(auth()->user()->isModerator() || auth()->user()->isAdmin())
                                        <div class="border-t border-gray-100"></div>
                                        <a href="{{ route('moderation.dashboard') }}" 
                                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            {{ __('Moderation Panel') }}
                                        </a>
                                    @endif
                                    
                                    @if(auth()->user()->isAdmin())
                                        <a href="{{ route('admin.dashboard') }}" 
                                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            {{ __('Admin Panel') }}
                                        </a>
                                    @endif
                                    
                                    <div class="border-t border-gray-100"></div>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" 
                                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            {{ __('Logout') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Balance Display -->
                        <div class="text-sm text-gray-600 hidden sm:block">
                            {{ __('Balance:') }} 
                            <span class="font-semibold text-green-600">
                                {{ number_format(auth()->user()->balance ?? 0, 2) }} {{ auth()->user()->balance_currency ?? 'USD' }}
                            </span>
                        </div>
                    @endguest

                    <!-- Mobile menu button -->
                    <button type="button" 
                            class="sm:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500"
                            onclick="toggleMobileMenu()">
                        <span class="sr-only">{{ __('Open main menu') }}</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu (hidden by default) -->
        <div class="sm:hidden hidden" id="mobile-menu">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <a href="{{ url('/') }}" 
                   class="text-gray-500 hover:text-gray-700 block px-3 py-2 rounded-md text-base font-medium">
                    {{ __('Home') }}
                </a>
                <a href="{{ route('artworks.index') }}" 
                   class="text-gray-500 hover:text-gray-700 block px-3 py-2 rounded-md text-base font-medium">
                    {{ __('Artworks') }}
                </a>
                <a href="{{ route('communities.index') }}" 
                   class="text-gray-500 hover:text-gray-700 block px-3 py-2 rounded-md text-base font-medium">
                    {{ __('Communities') }}
                </a>
                <a href="{{ route('support.index') }}" 
                   class="text-gray-500 hover:text-gray-700 block px-3 py-2 rounded-md text-base font-medium">
                    {{ __('Support') }}
                </a>
                
                @auth
                    <a href="{{ route('dashboard') }}" 
                       class="text-gray-500 hover:text-gray-700 block px-3 py-2 rounded-md text-base font-medium">
                        {{ __('Dashboard') }}
                    </a>
                    <a href="{{ route('nft.collection') }}" 
                       class="text-gray-500 hover:text-gray-700 block px-3 py-2 rounded-md text-base font-medium">
                        {{ __('My NFTs') }}
                    </a>
                    <a href="{{ route('messages.index') }}" 
                       class="text-gray-500 hover:text-gray-700 block px-3 py-2 rounded-md text-base font-medium">
                        {{ __('Messages') }}
                    </a>
                @else
                    <a href="{{ route('login') }}" 
                       class="text-gray-500 hover:text-gray-700 block px-3 py-2 rounded-md text-base font-medium">
                        {{ __('Login') }}
                    </a>
                    <a href="{{ route('register') }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white block px-3 py-2 rounded-md text-base font-medium">
                        {{ __('Register') }}
                    </a>
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
