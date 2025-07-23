<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ACQ Leaderboard - Top Rated Artworks - Acumen Craft</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .artwork-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .artwork-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .rank-badge {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        }

        .rank-badge.rank-1 {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        }

        .rank-badge.rank-2 {
            background: linear-gradient(135deg, #9ca3af 0%, #6b7280 100%);
        }

        .rank-badge.rank-3 {
            background: linear-gradient(135deg, #d97706 0%, #92400e 100%);
        }
    </style>
</head>

<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-white shadow-sm border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center space-x-4">
                        <a href="{{ url('/') }}" class="text-xl font-bold text-gray-900">
                            🎨 Acumen Craft
                        </a>
                        <nav class="hidden md:flex space-x-4">
                            <a href="{{ route('artworks.index') }}"
                                class="text-gray-600 hover:text-gray-900">Gallery</a>
                        </nav>
                    </div>
                    <div class="flex items-center space-x-4">
                        @auth
                            <a href="{{ route('users.profile') }}" class="text-gray-600 hover:text-gray-900">My Profile</a>
                            <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-900">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900">Login</a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Header -->
        <div class="bg-gradient-to-r from-purple-600 to-blue-600 text-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="text-center mb-12">
                    <h1 class="text-4xl font-bold mb-4">🏆 ACQ Leaderboard</h1>
                    <p class="text-xl text-gray-600 mb-2">Top-rated artworks by Acumen Craft Quotient</p>
                    <p class="text-sm text-gray-500 max-w-2xl mx-auto">
                        The ACQ (Acumen Craft Quotient) is a comprehensive quality metric that evaluates artworks based
                        on four key criteria:
                        Technical Skill, Composition & Design, Originality & Creativity, and Emotional Impact.
                        Each criterion is scored from 1-10, with the final ACQ representing the weighted average of
                        professional evaluations.
                    </p>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <!-- Timeframe Filter -->
            <div class="bg-white rounded-lg shadow-sm border p-4 mb-8">
                <div class="flex flex-wrap gap-2">
                    <span class="text-sm font-medium text-gray-700 mr-4">Filter by:</span>
                    <a href="{{ route('evaluations.leaderboard', ['timeframe' => 'all']) }}"
                        class="px-3 py-1 rounded-md text-sm {{ $timeframe === 'all' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        All Time
                    </a>
                    <a href="{{ route('evaluations.leaderboard', ['timeframe' => 'year']) }}"
                        class="px-3 py-1 rounded-md text-sm {{ $timeframe === 'year' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        This Year
                    </a>
                    <a href="{{ route('evaluations.leaderboard', ['timeframe' => 'month']) }}"
                        class="px-3 py-1 rounded-md text-sm {{ $timeframe === 'month' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        This Month
                    </a>
                    <a href="{{ route('evaluations.leaderboard', ['timeframe' => 'week']) }}"
                        class="px-3 py-1 rounded-md text-sm {{ $timeframe === 'week' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        This Week
                    </a>
                </div>
            </div>

            @if ($topArtworks->count() > 0)
                <!-- Top 3 Podium -->
                @if ($topArtworks->currentPage() === 1)
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
                        @foreach ($topArtworks->take(3) as $index => $artwork)
                            <div
                                class="artwork-card bg-white rounded-lg shadow-sm border overflow-hidden {{ $index === 0 ? 'md:order-2 transform md:scale-105' : ($index === 1 ? 'md:order-1' : 'md:order-3') }}">
                                <!-- Rank Badge -->
                                <div class="relative">
                                    <div
                                        class="rank-badge rank-{{ $index + 1 }} absolute top-4 left-4 z-10 w-10 h-10 rounded-full flex items-center justify-center text-white font-bold shadow-lg">
                                        {{ $index + 1 }}
                                    </div>

                                    <!-- Artwork Image -->
                                    <div class="aspect-square bg-gray-100">
                                        @if ($artwork->isImage())
                                            <img src="{{ $artwork->getThumbnailUrl() }}"
                                                alt="{{ $artwork->getTitle() }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center">
                                                <span class="text-4xl">📄</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="p-4">
                                    <div class="text-center mb-3">
                                        <div class="text-3xl font-bold text-blue-600 mb-1">
                                            {{ number_format($artwork->acq_score, 1) }}
                                        </div>
                                        <div class="text-sm text-gray-500">ACQ Score</div>
                                    </div>

                                    <h3 class="font-semibold text-gray-900 mb-1 text-center">
                                        <a href="{{ route('artworks.show', $artwork) }}" class="hover:text-blue-600">
                                            {{ $artwork->getTitle() }}
                                        </a>
                                    </h3>

                                    <p class="text-sm text-gray-600 text-center mb-2">
                                        by <a href="{{ route('users.show', $artwork->user) }}"
                                            class="hover:text-blue-600">{{ $artwork->user->name }}</a>
                                    </p>

                                    <div class="text-xs text-gray-500 text-center">
                                        {{ $artwork->evaluation_count }}
                                        evaluation{{ $artwork->evaluation_count !== 1 ? 's' : '' }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Full Rankings List -->
                <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Complete Rankings</h2>
                    </div>

                    <div class="divide-y divide-gray-200">
                        @foreach ($topArtworks as $index => $artwork)
                            @php
                                $currentRank = ($topArtworks->currentPage() - 1) * $topArtworks->perPage() + $index + 1;
                            @endphp

                            <div class="p-6 hover:bg-gray-50 transition-colors">
                                <div class="flex items-center space-x-4">
                                    <!-- Rank -->
                                    <div class="flex-shrink-0 w-12 text-center">
                                        <div class="text-lg font-bold text-gray-900">
                                            #{{ $currentRank }}
                                        </div>
                                    </div>

                                    <!-- Artwork Thumbnail -->
                                    <div class="flex-shrink-0">
                                        <div class="w-16 h-16 bg-gray-100 rounded-lg overflow-hidden">
                                            @if ($artwork->isImage())
                                                <img src="{{ $artwork->getThumbnailUrl() }}"
                                                    alt="{{ $artwork->getTitle() }}"
                                                    class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center">
                                                    <span class="text-xl">📄</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Artwork Info -->
                                    <div class="flex-grow min-w-0">
                                        <h3 class="font-semibold text-gray-900 truncate">
                                            <a href="{{ route('artworks.show', $artwork) }}"
                                                class="hover:text-blue-600">
                                                {{ $artwork->getTitle() }}
                                            </a>
                                        </h3>
                                        <p class="text-sm text-gray-600">
                                            by <a href="{{ route('users.show', $artwork->user) }}"
                                                class="hover:text-blue-600">{{ $artwork->user->name }}</a>
                                        </p>
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ $artwork->evaluation_count }} evaluations •
                                            {{ $artwork->view_count }} views •
                                            {{ $artwork->like_count }} likes
                                        </div>
                                    </div>

                                    <!-- ACQ Score -->
                                    <div class="flex-shrink-0 text-right">
                                        <div class="text-2xl font-bold text-blue-600">
                                            {{ number_format($artwork->acq_score, 1) }}
                                        </div>
                                        <div class="text-sm text-gray-500">ACQ Score</div>
                                    </div>

                                    <!-- Recent Evaluations Preview -->
                                    @if ($artwork->evaluations->count() > 0)
                                        <div class="flex-shrink-0 hidden md:block">
                                            <div class="text-xs text-gray-500 space-y-1">
                                                @foreach ($artwork->evaluations->take(2) as $evaluation)
                                                    <div>{{ $evaluation->evaluator->name }}:
                                                        {{ number_format($evaluation->overall_score ?: $evaluation->getAverageScoreAttribute(), 1) }}
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $topArtworks->appends(request()->query())->links() }}
                </div>
            @else
                <div class="bg-white rounded-lg shadow-sm border p-8 text-center">
                    <div class="text-gray-400 text-6xl mb-4">🏆</div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Evaluated Artworks</h3>
                    <p class="text-gray-600 mb-4">No artworks have been evaluated in this timeframe yet.</p>
                    <a href="{{ route('artworks.index') }}"
                        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                        Browse Artworks
                    </a>
                </div>
            @endif
        </div>
    </div>
</body>

</html>
