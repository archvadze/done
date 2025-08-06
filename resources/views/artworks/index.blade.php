@extends('layouts.app')

@section('title', 'Digital Art Gallery - Acumen Craft')
@section('description', 'Explore amazing digital artworks from talented artists. Browse, discover, and enjoy a curated collection of digital art and creative masterpieces.')
@section('keywords', 'digital art, gallery, artworks, artists, creative, design, digital painting, illustration')

@push('styles')
    <style>
        .artwork-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .artwork-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .masonry-grid {
            column-count: 1;
            column-gap: 1rem;
            column-fill: balance;
        }

        @media (min-width: 640px) {
            .masonry-grid {
                column-count: 2;
            }
        }

        @media (min-width: 768px) {
            .masonry-grid {
                column-count: 3;
            }
        }

        @media (min-width: 1024px) {
            .masonry-grid {
                column-count: 4;
            }
        }

        .masonry-item {
            break-inside: avoid;
            margin-bottom: 1rem;
            display: inline-block;
            width: 100%;
        }

        .aspect-ratio-box {
            position: relative;
            width: 100%;
            height: 0;
            padding-bottom: 100%; /* 1:1 Aspect Ratio */
        }

        .aspect-ratio-box img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>
@endpush

@section('content')
<!-- Page Header -->
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-4">Art Gallery</h1>
    <p class="text-gray-600 text-lg">Discover amazing artworks from talented artists around the world</p>
</div>

        <!-- Filters and Search -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="bg-white rounded-lg shadow-sm border p-6 mb-8">
                <!-- Search Bar -->
                <div class="mb-6">
                    <form method="GET" action="{{ route('artworks.index') }}" class="relative">
                        <div class="flex">
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Search artworks..."
                                class="flex-1 px-4 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <button type="submit"
                                class="bg-blue-600 text-white px-6 py-2 rounded-r-md hover:bg-blue-700 transition-colors">
                                Search
                            </button>
                        </div>

                        <!-- Keep other filters -->
                        @foreach (['category', 'sort', 'ai_generated'] as $param)
                            @if (request($param))
                                <input type="hidden" name="{{ $param }}" value="{{ request($param) }}">
                            @endif
                        @endforeach
                    </form>
                </div>

                <!-- Filter Buttons -->
                <div class="flex flex-wrap gap-3 mb-6">
                    <a href="{{ route('artworks.index', array_merge(request()->except('category'), ['category' => ''])) }}"
                        class="filter-btn px-4 py-2 rounded-md border {{ !request('category') ? 'active' : 'border-gray-300 text-gray-700 hover:bg-gray-50' }}">
                        All Categories
                    </a>

                    @foreach ($categories as $category)
                        <a href="{{ route('artworks.index', array_merge(request()->all(), ['category' => $category->slug])) }}"
                            class="filter-btn px-4 py-2 rounded-md border {{ request('category') == $category->slug ? 'active' : 'border-gray-300 text-gray-700 hover:bg-gray-50' }}">
                            {{ $category->display_name }}
                        </a>
                    @endforeach
                </div>

                <!-- Sort and Filter Options -->
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="flex flex-wrap items-center gap-4">
                        <!-- Sort -->
                        <div class="flex items-center space-x-2">
                            <label class="text-sm font-medium text-gray-700">Sort by:</label>
                            <select onchange="updateSort(this.value)"
                                class="px-3 py-1 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest
                                </option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest
                                </option>
                                <option value="most_liked" {{ request('sort') == 'most_liked' ? 'selected' : '' }}>Most
                                    Liked</option>
                                <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>Title A-Z
                                </option>
                            </select>
                        </div>

                        <!-- AI Filter -->
                        <div class="flex items-center space-x-2">
                            <input type="checkbox" id="ai-filter" {{ request('ai_generated') ? 'checked' : '' }}
                                onchange="toggleAIFilter(this.checked)"
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="ai-filter" class="text-sm text-gray-700">AI Generated</label>
                        </div>
                    </div>

                    <!-- Results Count -->
                    <div class="text-sm text-gray-600">
                        Showing {{ $artworks->firstItem() ?? 0 }} - {{ $artworks->lastItem() ?? 0 }} of
                        {{ $artworks->total() }} artworks
                    </div>
                </div>
            </div>

            <!-- Artworks Grid -->
            @if ($artworks->count() > 0)
                <div class="masonry-grid">
                    @foreach ($artworks as $artwork)
                        <div class="artwork-card bg-white rounded-lg shadow-sm border mb-4 break-inside-avoid">
                            <!-- Artwork Image -->
                            <a href="{{ route('artworks.show', $artwork) }}" class="block">
                                @if ($artwork->file_path)
                                    <img src="{{ $artwork->getThumbnailUrl() }}" alt="{{ $artwork->getTitle() }}"
                                        class="artwork-image" loading="lazy">
                                @else
                                    <div class="w-full h-48 bg-gray-200 rounded-t-lg flex items-center justify-center">
                                        <div class="text-center text-gray-500">
                                            <div class="text-4xl mb-2">🎨</div>
                                            <div class="text-sm">No Preview</div>
                                        </div>
                                    </div>
                                @endif
                            </a> <!-- Artwork Info -->
                            <div class="p-4">
                                <!-- Title -->
                                <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2">
                                    <a href="{{ route('artworks.show', $artwork) }}" class="hover:text-blue-600">
                                        {{ $artwork->getTitle() }}
                                    </a>
                                </h3>

                                <!-- Description -->
                                @if ($artwork->getDescription())
                                    <p class="text-gray-600 text-sm mb-3 line-clamp-2">
                                        {{ $artwork->getDescription() }}
                                    </p>
                                @endif

                                <!-- Metadata -->
                                <div class="flex items-center justify-between mb-3">
                                    <div class="text-xs text-gray-500">
                                        By <a href="#"
                                            class="text-blue-600 hover:text-blue-700 font-medium">{{ $artwork->user->name }}</a>
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $artwork->created_at->format('M j, Y') }}
                                    </div>
                                </div>

                                <!-- Tags & Badges -->
                                <div class="flex flex-wrap gap-1 mb-3">
                                    @if ($artwork->is_ai_generated)
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-purple-100 text-purple-800">
                                            🤖 AI
                                        </span>
                                    @endif

                                    @if ($artwork->category)
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800">
                                            {{ $artwork->category }}
                                        </span>
                                    @endif

                                    @if ($artwork->tags && is_array($artwork->tags))
                                        @foreach (array_slice($artwork->tags, 0, 2) as $tag)
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-800">
                                                {{ $tag }}
                                            </span>
                                        @endforeach
                                        @if (count($artwork->tags) > 2)
                                            <span class="text-xs text-gray-500">+{{ count($artwork->tags) - 2 }}</span>
                                        @endif
                                    @endif
                                </div>

                                <!-- Actions -->
                                <div class="flex items-center justify-between">
                                    <!-- Like Button -->
                                    @auth
                                        <form method="POST" action="{{ route('artworks.like', $artwork) }}"
                                            class="inline">
                                            @csrf
                                            <button type="submit"
                                                class="flex items-center space-x-1 text-gray-600 hover:text-red-500 transition-colors">
                                                <span
                                                    class="text-sm">{{ $artwork->isLikedBy(auth()->user()) ? '❤️' : '🤍' }}</span>
                                                <span class="text-sm">{{ $artwork->likes_count }}</span>
                                            </button>
                                        </form>
                                    @else
                                        <div class="flex items-center space-x-1 text-gray-600">
                                            <span class="text-sm">🤍</span>
                                            <span class="text-sm">{{ $artwork->likes_count }}</span>
                                        </div>
                                    @endauth

                                    <!-- View Count -->
                                    <div class="flex items-center space-x-1 text-gray-600">
                                        <span class="text-sm">👁️</span>
                                        <span class="text-sm">{{ $artwork->view_count ?? 0 }}</span>
                                    </div>

                                    <!-- File Info -->
                                    @if ($artwork->file_size)
                                        <div class="text-xs text-gray-500">
                                            {{ number_format($artwork->file_size / 1024 / 1024, 1) }}MB
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $artworks->appends(request()->query())->links() }}
                </div>
            @else
                <!-- No Artworks Found -->
                <div class="text-center py-16">
                    <div class="text-6xl mb-4">🎨</div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No artworks found</h3>
                    <p class="text-gray-600 mb-8">
                        @if (request()->hasAny(['search', 'category', 'ai_generated']))
                            Try adjusting your filters or search terms.
                        @else
                            Be the first to share your creative work!
                        @endif
                    </p>

                    <div class="flex justify-center space-x-4">
                        @if (request()->hasAny(['search', 'category', 'ai_generated']))
                            <a href="{{ route('artworks.index') }}"
                                class="bg-gray-600 text-white px-6 py-2 rounded-md hover:bg-gray-700 transition-colors">
                                Clear Filters
                            </a>
                        @endif

                        @auth
                            <a href="{{ route('artworks.create') }}"
                                class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition-colors">
                                Upload First Artwork
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                                class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition-colors">
                                Join & Upload
                            </a>
                        @endauth
                    </div>
                </div>
            @endif
        </div>

@endsection

@push('scripts')
    <script src="{{ asset('js/artworks.js') }}"></script>
    <script>
        // Pass session messages to JavaScript
        @if (session('success'))
            window.successMessage = '{{ session('success') }}';
        @endif
        
        @if (session('error'))
            window.errorMessage = '{{ session('error') }}';
        @endif
    </script>
@endpush
