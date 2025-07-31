@extends('layouts.app')

@section('title', __('Dashboard'))

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                            {{ __('Welcome back, ') . ($user->name ?? 'User') . '!' }}
                        </h1>
                        <p class="text-gray-600 dark:text-gray-400 mt-1">
                            {{ __('Here\'s what\'s happening with your account today.') }}
                        </p>
                    </div>

                    <div class="flex items-center space-x-4">
                        <!-- Quick Actions -->
                        <a href="{{ route('artworks.create') }}"
                            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            <i class="fas fa-plus mr-2"></i>{{ __('Upload Artwork') }}
                        </a>

                        @if ($stats['open_tickets'] > 0)
                            <a href="{{ route('support.tickets.index') }}"
                                class="relative bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                <i class="fas fa-exclamation-triangle mr-2"></i>{{ __('Open Tickets') }}
                                <span
                                    class="absolute -top-2 -right-2 bg-yellow-400 text-red-800 text-xs rounded-full h-5 w-5 flex items-center justify-center font-bold">
                                    {{ $stats['open_tickets'] }}
                                </span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Artworks -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900">
                            <i class="fas fa-palette text-blue-600 dark:text-blue-400 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('Artworks') }}</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                {{ number_format($stats['artworks']) }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ number_format($stats['published_artworks']) }} {{ __('published') }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Likes Received -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-red-100 dark:bg-red-900">
                            <i class="fas fa-heart text-red-600 dark:text-red-400 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('Likes Received') }}</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                {{ number_format($stats['likes_received']) }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ __('across all artworks') }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- NFTs -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900">
                            <i class="fas fa-gem text-purple-600 dark:text-purple-400 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('NFTs Owned') }}</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['nfts']) }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                <a href="{{ route('nft.collection', $user) }}"
                                    class="text-purple-600 dark:text-purple-400 hover:underline">
                                    {{ __('View Collection') }}
                                </a>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Support Tickets -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 dark:bg-green-900">
                            <i class="fas fa-ticket-alt text-green-600 dark:text-green-400 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('Support Tickets') }}</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                {{ number_format($stats['support_tickets']) }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                @if ($stats['open_tickets'] > 0)
                                    <span class="text-red-600 dark:text-red-400">{{ $stats['open_tickets'] }}
                                        {{ __('open') }}</span>
                                @else
                                    <span class="text-green-600 dark:text-green-400">{{ __('All resolved') }}</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Account Security & Two-Factor Authentication -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ __('Account Security') }}
                        </h3>
                        <div class="flex items-center">
                            <span
                                class="text-sm text-gray-600 dark:text-gray-400 mr-2">{{ __('Two-Factor Authentication:') }}</span>
                            @if ($user->twofa_enabled)
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    <i class="fas fa-shield-alt mr-1"></i>{{ __('Enabled') }}
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>{{ __('Disabled') }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <a href="{{ route('two-factor.show') }}"
                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        <i class="fas fa-cog mr-2"></i>{{ __('Manage 2FA') }}
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Recent Artworks -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Recent Artworks') }}
                            </h3>
                            <a href="{{ route('artworks.index') }}"
                                class="text-blue-600 dark:text-blue-400 hover:underline text-sm">
                                {{ __('View All') }}
                            </a>
                        </div>
                    </div>
                    <div class="p-6">
                        @if ($recentArtworks->count() > 0)
                            <div class="space-y-4">
                                @foreach ($recentArtworks as $artwork)
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-shrink-0">
                                            @if ($artwork->image_path)
                                                <img src="{{ $artwork->getThumbnailUrl() }}" alt="{{ $artwork->title }}"
                                                    class="w-12 h-12 rounded-lg object-cover">
                                            @else
                                                <div
                                                    class="w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                                    <i class="fas fa-image text-gray-400"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                                <a href="{{ route('artworks.show', $artwork) }}"
                                                    class="hover:text-blue-600 dark:hover:text-blue-400">
                                                    {{ $artwork->title }}
                                                </a>
                                            </p>
                                            <div class="flex items-center text-xs text-gray-500 dark:text-gray-400">
                                                <span class="mr-3">{{ $artwork->created_at->diffForHumans() }}</span>
                                                @if ($artwork->is_published)
                                                    <span
                                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                        {{ __('Published') }}
                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200">
                                                        {{ __('Draft') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                            <i class="fas fa-heart text-red-500 mr-1"></i>
                                            {{ $artwork->likes()->count() }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <i class="fas fa-palette text-gray-400 text-3xl mb-3"></i>
                                <p class="text-gray-500 dark:text-gray-400">{{ __('No artworks yet') }}</p>
                                <a href="{{ route('artworks.create') }}"
                                    class="inline-block mt-2 text-blue-600 dark:text-blue-400 hover:underline text-sm">
                                    {{ __('Upload your first artwork') }}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Recent Activity & Quick Links -->
                <div class="space-y-6">
                    <!-- Recent Support Tickets -->
                    @if ($recentTickets->count() > 0)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                        {{ __('Recent Support Tickets') }}</h3>
                                    <a href="{{ route('support.tickets.index') }}"
                                        class="text-blue-600 dark:text-blue-400 hover:underline text-sm">
                                        {{ __('View All') }}
                                    </a>
                                </div>
                            </div>
                            <div class="p-6">
                                <div class="space-y-3">
                                    @foreach ($recentTickets as $ticket)
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                    <a href="{{ route('support.tickets.show', $ticket) }}"
                                                        class="hover:text-blue-600 dark:hover:text-blue-400">
                                                        #{{ $ticket->ticket_number }}
                                                    </a>
                                                </p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ Str::limit($ticket->subject, 40) }}</p>
                                            </div>
                                            <div class="text-right">
                                                @php
                                                    $statusClass = match ($ticket->status) {
                                                        'open'
                                                            => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                                        'in_progress'
                                                            => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                                        'waiting_for_customer'
                                                            => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                                        'resolved'
                                                            => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200',
                                                        'closed'
                                                            => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200',
                                                        default
                                                            => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200',
                                                    };
                                                @endphp
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $statusClass }}">
                                                    {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Quick Links -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('Quick Links') }}</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <a href="{{ route('users.profile') }}"
                                class="flex items-center p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <i class="fas fa-user text-blue-600 dark:text-blue-400 mr-3"></i>
                                <span
                                    class="text-sm font-medium text-gray-900 dark:text-white">{{ __('Edit Profile') }}</span>
                            </a>

                            <a href="{{ route('payments.history') }}"
                                class="flex items-center p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <i class="fas fa-credit-card text-green-600 dark:text-green-400 mr-3"></i>
                                <span
                                    class="text-sm font-medium text-gray-900 dark:text-white">{{ __('Payment History') }}</span>
                            </a>

                            <a href="{{ route('support.index') }}"
                                class="flex items-center p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <i class="fas fa-life-ring text-purple-600 dark:text-purple-400 mr-3"></i>
                                <span
                                    class="text-sm font-medium text-gray-900 dark:text-white">{{ __('Get Support') }}</span>
                            </a>

                            <a href="{{ route('communities.index') }}"
                                class="flex items-center p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <i class="fas fa-users text-orange-600 dark:text-orange-400 mr-3"></i>
                                <span
                                    class="text-sm font-medium text-gray-900 dark:text-white">{{ __('Communities') }}</span>
                            </a>
                        </div>
                    </div>

                    <!-- Recent NFTs -->
                    @if ($recentNfts->count() > 0)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                        {{ __('Recent NFTs') }}</h3>
                                    <a href="{{ route('nft.collection', $user) }}"
                                        class="text-blue-600 dark:text-blue-400 hover:underline text-sm">
                                        {{ __('View Collection') }}
                                    </a>
                                </div>
                            </div>
                            <div class="p-6">
                                <div class="grid grid-cols-2 gap-4">
                                    @foreach ($recentNfts as $nft)
                                        <div class="relative group">
                                            <div
                                                class="aspect-square rounded-lg overflow-hidden bg-gray-200 dark:bg-gray-700">
                                                @if ($nft->artwork && $nft->artwork->getThumbnailUrl())
                                                    <img src="{{ $nft->artwork->getThumbnailUrl() }}"
                                                        alt="{{ $nft->name }}" class="w-full h-full object-cover">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center">
                                                        <i class="fas fa-gem text-gray-400 text-2xl"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="mt-2">
                                                <p class="text-xs font-medium text-gray-900 dark:text-white truncate">
                                                    {{ $nft->name }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ ucfirst($nft->network) }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
