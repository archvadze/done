<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin Panel') - Acumen Craft</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <style>
        .admin-sidebar {
            min-height: calc(100vh - 4rem);
        }

        .admin-nav-item {
            @apply block px-4 py-2 text-sm acumen-text hover:acumen-secondary transition-colors;
        }

        .admin-nav-item.active {
            @apply acumen-primary;
        }

        .admin-stats-card {
            @apply acumen-bg p-6 shadow-sm;
        }

        .admin-table {
            @apply min-w-full divide-y divide-acumen-medium-gold;
        }

        .admin-table th {
            @apply px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider;
        }

        .admin-table td {
            @apply px-6 py-4 whitespace-nowrap text-sm text-gray-900;
        }

        .admin-badge {
            @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium;
        }

        .admin-badge-success {
            @apply bg-green-100 text-green-800;
        }

        .admin-badge-warning {
            @apply bg-yellow-100 text-yellow-800;
        }

        .admin-badge-danger {
            @apply bg-red-100 text-red-800;
        }

        .admin-badge-info {
            @apply bg-blue-100 text-blue-800;
        }
    </style>
</head>

<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen">
        <!-- Top Navigation -->
        <nav class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold text-gray-900">
                            🛠️ Admin Panel
                        </a>
                        <span class="text-gray-400">|</span>
                        <a href="{{ url('/') }}" class="text-sm text-gray-600 hover:text-gray-900">
                            ← Back to Site
                        </a>
                    </div>

                    <div class="flex items-center space-x-4">
                        <!-- Language Switcher - Temporarily disabled -->
                        <!-- <x-locale-switcher /> -->

                        <!-- User Menu -->
                        <div class="flex items-center space-x-2">
                            <img src="{{ Auth::user()->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) }}"
                                alt="{{ Auth::user()->name }}" class="w-8 h-8 rounded-full">
                            <span class="text-sm font-medium text-gray-700">{{ Auth::user()->name }}</span>
                            <span class="admin-badge admin-badge-danger">Admin</span>
                        </div>

                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-sm text-gray-600 hover:text-gray-900">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <div class="flex">
            <!-- Sidebar -->
            <aside class="w-64 admin-sidebar bg-white shadow-sm border-r border-gray-200">
                <nav class="p-4 space-y-2">
                    <a href="{{ route('admin.dashboard') }}"
                        class="admin-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        📊 Dashboard
                    </a>

                    <a href="{{ route('admin.users') }}"
                        class="admin-nav-item {{ request()->routeIs('admin.users') ? 'active' : '' }}">
                        👥 Users Management
                    </a>

                    <a href="{{ route('admin.artworks') }}"
                        class="admin-nav-item {{ request()->routeIs('admin.artworks') ? 'active' : '' }}">
                        🎨 Artworks Management
                    </a>

                    <a href="{{ route('admin.evaluations') }}"
                        class="admin-nav-item {{ request()->routeIs('admin.evaluations') ? 'active' : '' }}">
                        ⭐ Evaluations Management
                    </a>

                    <hr class="my-4 border-gray-200">

                    <a href="{{ route('admin.languages') }}"
                        class="admin-nav-item {{ request()->routeIs('admin.languages*') ? 'active' : '' }}">
                        🌍 Languages
                    </a>

                    <a href="{{ route('admin.settings') }}"
                        class="admin-nav-item {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                        ⚙️ Settings
                    </a>

                    <a href="{{ route('admin.logs') }}"
                        class="admin-nav-item {{ request()->routeIs('admin.logs') ? 'active' : '' }}">
                        📝 System Logs
                    </a>
                </nav>
            </aside>

            <!-- Main Content -->
            <main class="flex-1 p-6">
                <!-- Page Header -->
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">@yield('title', 'Admin Dashboard')</h1>
                    @hasSection('subtitle')
                        <p class="text-gray-600 mt-1">@yield('subtitle')</p>
                    @endhasSection
                </div>

                <!-- Flash Messages -->
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

                <!-- Page Content -->
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>

</html>
