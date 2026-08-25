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
            @apply block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors;
        }

        .admin-nav-item.active {
            @apply bg-blue-100 text-blue-700;
        }

        .admin-stats-card {
            @apply bg-white p-6 shadow-sm rounded-lg;
        }

        .admin-table {
            @apply min-w-full divide-y divide-gray-200;
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
                        📋 Evaluations
                    </a>

                    <a href="{{ route('admin.reports') }}"
                        class="admin-nav-item {{ request()->routeIs('admin.reports') ? 'active' : '' }}">
                        📈 Reports
                    </a>

                    <div class="border-t border-gray-200 my-4"></div>

                    <a href="{{ route('actions.index') }}"
                        class="admin-nav-item {{ request()->routeIs('actions.*') ? 'active' : '' }}">
                        ⚡ Moderation Actions
                    </a>

                    <a href="{{ route('logs.index') }}"
                        class="admin-nav-item {{ request()->routeIs('logs.*') ? 'active' : '' }}">
                        📄 Activity Logs
                    </a>

                    <a href="{{ route('security.logs') }}"
                        class="admin-nav-item {{ request()->routeIs('security.*') ? 'active' : '' }}">
                        🔒 Security Logs
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
