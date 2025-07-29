<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Art Gallery - Acumen Craft</title>

    <!-- Favicons from Style Guide -->
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="shortcut icon" href="/favicon.ico">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .artwork-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            background-color: var(--bg-card);
            overflow: hidden;
        }

        .artwork-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(196, 136, 64, 0.25);
        }

        .masonry-grid {
            columns: 1;
            column-gap: 1rem;
        }

        @media (min-width: 640px) {
            .masonry-grid {
                columns: 2;
            }
        }

        @media (min-width: 1024px) {
            .masonry-grid {
                columns: 3;
            }
        }

        @media (min-width: 1280px) {
            .masonry-grid {
                columns: 4;
            }
        }

        .artwork-image {
            width: 100%;
            height: auto;
            object-fit: cover;
        }

        .filter-btn {
            transition: all 0.2s ease;
        }

        .filter-btn.active {
            background-color: #c28840;
            color: #090909;
            transform: scale(1.05);
        }
    </style>
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="nav-background sticky top-0 z-40">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center">
                        <a href="{{ url('/') }}" class="text-xl font-bold text-primary">
                            🎨 Acumen Craft
                        </a>
                    </div>
                    <div class="flex items-center space-x-4">
                        @auth
                            <a href="{{ route('artworks.create') }}"
                                class="btn-primary px-4 py-2">
                                Upload Artwork
                            </a>
                            <a href="{{ route('dashboard') }}" class="btn-text">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="btn-text">Login</a>
                            <a href="{{ route('login') }}"
                                class="btn-primary px-4 py-2">Sign
                                Up</a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Header -->
        <div class="text-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="text-center">
                    <h1 class="text-4xl font-bold mb-4">Art Gallery</h1>
                    <p class="text-xl opacity-90">Discover amazing artworks from talented creators</p>
                </div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="bg-background p-6 mb-8">
                <!-- Search Bar -->
                <div class="mb-6">
                    <form method="GET" action="{{ route('artworks.index') }}" class="relative">
                        <div class="flex">
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Search artworks..."
                                class="flex-1 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary">
                            <button type="submit"
                                class="btn-primary px-6 py-2">
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
                        class="filter-btn px-4 py-2 {{ !request('category') ? 'active' : 'btn-text' }}">
                        All Categories
                    </a>

                    @foreach ($categories as $category)
                        <a href="{{ route('artworks.index', array_merge(request()->all(), ['category' => $category->slug])) }}"
                            class="filter-btn px-4 py-2 {{ request('category') == $category->slug ? 'active' : 'btn-text' }}">
                            {{ $category->display_name }}
                        </a>
                    @endforeach
                </div>

                <!-- Sort and Filter Options -->
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="flex flex-wrap items-center gap-4">
                        <!-- Sort -->
                        <div class="flex items-center space-x-2">
                            <label class="text-sm font-medium">Sort by:</label>
                            <select onchange="updateSort(this.value)"
                                class="px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
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
                                class="h-4 w-4 text-primary focus:ring-primary">
                            <label for="ai-filter" class="text-sm">AI Generated</label>
                        </div>
                    </div>

                    <!-- Results Count -->
                    <div class="text-sm">
                        Showing {{ $artworks->firstItem() ?? 0 }} - {{ $artworks->lastItem() ?? 0 }} of
                        {{ $artworks->total() }} artworks
                    </div>
                </div>
            </div>

            <!-- Artworks Grid -->
            @if ($artworks->count() > 0)
                <div class="masonry-grid">
                    @foreach ($artworks as $artwork)
                        <div class="artwork-card mb-4 break-inside-avoid">
                            <!-- Artwork Image -->
                            <a href="{{ route('artworks.show', $artwork) }}" class="block">
                                @if ($artwork->file_path)
                                    <img src="{{ $artwork->getThumbnailUrl() }}" alt="{{ $artwork->getTitle() }}"
                                        class="artwork-image" loading="lazy">
                                @else
                                    <div class="w-full h-48 flex items-center justify-center">
                                        <div class="text-center text-gray-500">
                                            <div class="text-4xl mb-2">🎨</div>
                                            <div class="text-sm">No Preview</div>
                                        </div>
                                    </div>
                                @endif
                            </a> <!-- Artwork Info -->
                            <div class="p-4">
                                <!-- Title -->
                                <h3 class="font-semibold mb-2 line-clamp-2">
                                    <a href="{{ route('artworks.show', $artwork) }}" class="hover:text-primary">
                                        {{ $artwork->getTitle() }}
                                    </a>
                                </h3>

                                <!-- Description -->
                                @if ($artwork->getDescription())
                                    <p class="text-secondary text-sm mb-3 line-clamp-2">
                                        {{ $artwork->getDescription() }}
                                    </p>
                                @endif

                                <!-- Metadata -->
                                <div class="flex items-center justify-between mb-3">
                                    <div class="text-xs text-secondary">
                                        By <a href="#"
                                            class="text-primary hover:text-primary-dark font-medium">{{ $artwork->user->name }}</a>
                                    </div>
                                    <div class="text-xs text-secondary">
                                        {{ $artwork->created_at->format('M j, Y') }}
                                    </div>
                                </div>

                                <!-- Tags & Badges -->
                                <div class="flex flex-wrap gap-1 mb-3">
                                    @if ($artwork->is_ai_generated)
                                        <span
                                            class="inline-flex items-center px-2 py-1 text-xs">
                                            🤖 AI
                                        </span>
                                    @endif

                                    @if ($artwork->category)
                                        <span
                                            class="inline-flex items-center px-2 py-1 text-xs">
                                            {{ $artwork->category }}
                                        </span>
                                    @endif

                                    @if ($artwork->tags && is_array($artwork->tags))
                                        @foreach (array_slice($artwork->tags, 0, 2) as $tag)
                                            <span
                                                class="inline-flex items-center px-2 py-1 text-xs">
                                                {{ $tag }}
                                            </span>
                                        @endforeach
                                        @if (count($artwork->tags) > 2)
                                            <span class="text-xs text-secondary">+{{ count($artwork->tags) - 2 }}</span>
                                        @endif
                                    @endif
                                </div>

                                <!-- Actions -->
                                <div class="flex items-center justify-between">
                                    <!-- Like Button -->
                                    @auth
                                        @if(auth()->user()->id !== $artwork->user_id)
                                            <!-- Show like button for other users -->
                                            <form method="POST" action="{{ route('artworks.like', $artwork) }}"
                                                class="inline">
                                                @csrf
                                                <button type="submit"
                                                    class="likes-btn flex items-center space-x-1 transition-colors">
                                                    <span
                                                        class="text-sm">{{ $artwork->isLikedBy(auth()->user()) ? '❤️' : '🤍' }}</span>
                                                    <span class="text-sm">{{ $artwork->likes_count }}</span>
                                                </button>
                                            </form>
                                        @else
                                            <!-- Show non-interactive likes count for artwork owner -->
                                            <div class="flex items-center space-x-1 text-gray-500">
                                                <span class="text-sm">❤️</span>
                                                <span class="text-sm">{{ $artwork->likes_count }}</span>
                                            </div>
                                        @endif
                                    @else
                                        <div class="flex items-center space-x-1">
                                            <span class="text-sm">🤍</span>
                                            <span class="text-sm">{{ $artwork->likes_count }}</span>
                                        </div>
                                    @endauth

                                    <!-- View Count -->
                                    <div class="flex items-center space-x-1 text-secondary">
                                        <span class="text-sm">👁️</span>
                                        <span class="text-sm">{{ $artwork->view_count ?? 0 }}</span>
                                    </div>

                                    <!-- File Info -->
                                    @if ($artwork->file_size)
                                        <div class="text-xs text-secondary">
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
                    <h3 class="text-xl font-semibold mb-2">No artworks found</h3>
                    <p class="text-secondary mb-8">
                        @if (request()->hasAny(['search', 'category', 'ai_generated']))
                            Try adjusting your filters or search terms.
                        @else
                            Be the first to share your creative work!
                        @endif
                    </p>

                    <div class="flex justify-center space-x-4">
                        @if (request()->hasAny(['search', 'category', 'ai_generated']))
                            <a href="{{ route('artworks.index') }}"
                                class="btn-secondary px-6 py-2">
                                Clear Filters
                            </a>
                        @endif

                        @auth
                            <a href="{{ route('artworks.create') }}"
                                class="btn-primary px-6 py-2">
                                Upload First Artwork
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                                class="btn-primary px-6 py-2">
                                Join & Upload
                            </a>
                        @endauth
                    </div>
                </div>
            @endif
        </div>

        <!-- Footer -->
        <footer class="mt-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="text-center">
                    <div class="text-2xl font-bold mb-4">🎨 Acumen Craft</div>
                    <p class="text-secondary mb-4">Empowering creativity through technology</p>
                    <div class="flex justify-center space-x-6">
                        <a href="#" class="text-secondary hover:text-primary">About</a>
                        <a href="#" class="text-secondary hover:text-primary">Terms</a>
                        <a href="#" class="text-secondary hover:text-primary">Privacy</a>
                        <a href="#" class="text-secondary hover:text-primary">Contact</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <script>
        function updateSort(sortValue) {
            const url = new URL(window.location.href);
            if (sortValue) {
                url.searchParams.set('sort', sortValue);
            } else {
                url.searchParams.delete('sort');
            }
            window.location.href = url.toString();
        }

        function toggleAIFilter(checked) {
            const url = new URL(window.location.href);
            if (checked) {
                url.searchParams.set('ai_generated', '1');
            } else {
                url.searchParams.delete('ai_generated');
            }
            window.location.href = url.toString();
        }

        // Infinite scroll (optional enhancement)
        let loading = false;

        function loadMore() {
            if (loading) return;

            const nextPageUrl = document.querySelector('a[rel="next"]')?.href;
            if (!nextPageUrl) return;

            loading = true;

            fetch(nextPageUrl)
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newArtworks = doc.querySelectorAll('.artwork-card');
                    const grid = document.querySelector('.masonry-grid');

                    newArtworks.forEach(artwork => {
                        grid.appendChild(artwork);
                    });

                    // Update pagination
                    const newPagination = doc.querySelector('.pagination');
                    if (newPagination) {
                        document.querySelector('.pagination').innerHTML = newPagination.innerHTML;
                    }

                    loading = false;
                })
                .catch(error => {
                    console.error('Error loading more artworks:', error);
                    loading = false;
                });
        }

        // Auto-load more when scrolling near bottom
        window.addEventListener('scroll', () => {
            if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 1000) {
                loadMore();
            }
        });

        // Show success/error messages
        @if (session('success'))
            const successDiv = document.createElement('div');
            successDiv.className = 'fixed top-4 right-4 text-white px-6 py-3 shadow-lg z-50';
            successDiv.textContent = '✅ {{ session('success') }}';
            document.body.appendChild(successDiv);
            setTimeout(() => successDiv.remove(), 5000);
        @endif

        @if (session('error'))
            const errorDiv = document.createElement('div');
            errorDiv.className = 'fixed top-4 right-4 text-white px-6 py-3 shadow-lg z-50';
            errorDiv.textContent = '❌ {{ session('error') }}';
            document.body.appendChild(errorDiv);
            setTimeout(() => errorDiv.remove(), 5000);
        @endif
    </script>

    <style>
        /* Line clamp utilities */
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</body>

</html>
