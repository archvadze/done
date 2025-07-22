<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $artwork->getTitle() }} - Acumen Craft</title>

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="{{ $artwork->getTitle() }}">
    <meta property="og:description"
        content="{{ $artwork->getDescription() ? Str::limit($artwork->getDescription(), 160) : 'Artwork by ' . $artwork->user->name }}">
    @if ($artwork->file_path)
        <meta property="og:image" content="{{ Storage::url($artwork->file_path) }}">
    @endif
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .artwork-main {
            max-height: 80vh;
            object-fit: contain;
        }

        .zoom-container {
            overflow: hidden;
            cursor: zoom-in;
            position: relative;
        }

        .zoom-container.zoomed {
            cursor: zoom-out;
        }

        .zoom-container img {
            transition: transform 0.3s ease;
        }

        .zoom-container.zoomed img {
            transform: scale(2);
        }

        .metadata-card {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        }

        .tag-badge {
            transition: all 0.2s ease;
        }

        .tag-badge:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
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
                            <a href="{{ route('artworks.index') }}" class="text-blue-600 hover:text-blue-700">← Back to
                                Gallery</a>
                        </nav>
                    </div>
                    <div class="flex items-center space-x-4">
                        @auth
                            @if (auth()->id() === $artwork->user_id)
                                <a href="{{ route('artworks.edit', $artwork) }}"
                                    class="text-gray-600 hover:text-gray-900">Edit</a>
                            @endif
                            <a href="{{ route('artworks.create') }}"
                                class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                                Upload
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900">Login</a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Artwork Display -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
                        @if ($artwork->file_path)
                            @if (Str::startsWith($artwork->file_type, 'image/'))
                                <!-- Image -->
                                <div class="zoom-container" onclick="toggleZoom(this)">
                                    <img src="{{ Storage::url($artwork->file_path) }}"
                                        alt="{{ $artwork->getTitle() }}" class="artwork-main w-full">
                                </div>
                            @elseif(Str::startsWith($artwork->file_type, 'video/'))
                                <!-- Video -->
                                <video controls class="artwork-main w-full">
                                    <source src="{{ Storage::url($artwork->file_path) }}"
                                        type="{{ $artwork->file_type }}">
                                    Your browser does not support the video tag.
                                </video>
                            @elseif(Str::startsWith($artwork->file_type, 'audio/'))
                                <!-- Audio with Cover -->
                                <div class="bg-gray-100 p-8 text-center">
                                    <div class="text-8xl mb-4">🎵</div>
                                    <h3 class="text-xl font-semibold mb-4">{{ $artwork->getTitle() }}</h3>
                                    <audio controls class="w-full max-w-md mx-auto">
                                        <source src="{{ Storage::url($artwork->file_path) }}"
                                            type="{{ $artwork->file_type }}">
                                        Your browser does not support the audio tag.
                                    </audio>
                                </div>
                            @else
                                <!-- Other File Types -->
                                <div class="bg-gray-100 p-8 text-center">
                                    <div class="text-8xl mb-4">📄</div>
                                    <h3 class="text-xl font-semibold mb-4">{{ $artwork->getTitle() }}</h3>
                                    <a href="{{ Storage::url($artwork->file_path) }}" target="_blank"
                                        class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition-colors">
                                        View File
                                    </a>
                                </div>
                            @endif
                        @else
                            <!-- No File -->
                            <div class="bg-gray-100 p-8 text-center">
                                <div class="text-8xl mb-4">🎨</div>
                                <h3 class="text-xl font-semibold">{{ $artwork->getTitle() }}</h3>
                            </div>
                        @endif
                    </div>

                    <!-- Actions Bar -->
                    <div class="flex items-center justify-between mt-6 bg-white rounded-lg shadow-sm border px-6 py-4">
                        <div class="flex items-center space-x-6">
                            <!-- Like Button -->
                            @auth
                                <form method="POST" action="{{ route('artworks.toggle-like', $artwork) }}" class="inline">
                                    @csrf
                                    <button type="submit"
                                        class="flex items-center space-x-2 {{ $artwork->isLikedBy(auth()->user()) ? 'text-red-500' : 'text-gray-600 hover:text-red-500' }} transition-colors">
                                        <span
                                            class="text-xl">{{ $artwork->isLikedBy(auth()->user()) ? '❤️' : '🤍' }}</span>
                                        <span class="font-medium">{{ $artwork->likes_count }}
                                            {{ $artwork->likes_count == 1 ? 'Like' : 'Likes' }}</span>
                                    </button>
                                </form>
                            @else
                                <div class="flex items-center space-x-2 text-gray-600">
                                    <span class="text-xl">🤍</span>
                                    <span class="font-medium">{{ $artwork->likes_count }}
                                        {{ $artwork->likes_count == 1 ? 'Like' : 'Likes' }}</span>
                                </div>
                            @endauth

                            <!-- Views -->
                            <div class="flex items-center space-x-2 text-gray-600">
                                <span class="text-xl">👁️</span>
                                <span class="font-medium">{{ $artwork->view_count ?? 0 }} Views</span>
                            </div>
                        </div>

                        <div class="flex items-center space-x-3">
                            <!-- Share Button -->
                            <button onclick="shareArtwork()"
                                class="flex items-center space-x-2 text-gray-600 hover:text-blue-600 transition-colors">
                                <span class="text-xl">🔗</span>
                                <span class="font-medium">Share</span>
                            </button>

                            <!-- Download Button -->
                            @if ($artwork->downloads_enabled && $artwork->file_path)
                                <a href="{{ Storage::url($artwork->file_path) }}" download
                                    class="flex items-center space-x-2 text-gray-600 hover:text-green-600 transition-colors">
                                    <span class="text-xl">📥</span>
                                    <span class="font-medium">Download</span>
                                </a>
                            @endif

                            <!-- Report Button -->
                            <button onclick="reportArtwork()"
                                class="flex items-center space-x-2 text-gray-600 hover:text-red-600 transition-colors">
                                <span class="text-xl">🚩</span>
                                <span class="font-medium">Report</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Artwork Info Sidebar -->
                <div class="space-y-6">
                    <!-- Basic Info -->
                    <div class="bg-white rounded-lg shadow-sm border p-6">
                        <h1 class="text-2xl font-bold text-gray-900 mb-3">{{ $artwork->getTitle() }}</h1>

                        <!-- Artist Info -->
                        <div class="flex items-center mb-4">
                            <div
                                class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold mr-3">
                                {{ substr($artwork->user->name, 0, 1) }}
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">{{ $artwork->user->name }}</h3>
                                <p class="text-sm text-gray-600">{{ $artwork->created_at->format('M j, Y') }}</p>
                            </div>
                        </div>

                        <!-- Description -->
                        @if ($artwork->getDescription())
                            <div class="mb-4">
                                <h4 class="font-semibold text-gray-900 mb-2">Description</h4>
                                <p class="text-gray-700 leading-relaxed">{{ $artwork->getDescription() }}</p>
                            </div>
                        @endif

                        <!-- Tags -->
                        @if ($artwork->tags && is_array($artwork->tags) && count($artwork->tags) > 0)
                            <div class="mb-4">
                                <h4 class="font-semibold text-gray-900 mb-2">Tags</h4>
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($artwork->tags as $tag)
                                        <span
                                            class="tag-badge inline-flex items-center px-3 py-1 rounded-full text-xs bg-blue-100 text-blue-800 cursor-pointer">
                                            {{ $tag }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Category -->
                        @if ($artwork->category)
                            <div class="mb-4">
                                <h4 class="font-semibold text-gray-900 mb-2">Category</h4>
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-green-100 text-green-800">
                                    {{ $artwork->category }}
                                </span>
                            </div>
                        @endif

                        <!-- AI Generated Badge -->
                        @if ($artwork->is_ai_generated)
                            <div class="mb-4">
                                <div
                                    class="flex items-center space-x-2 p-3 bg-purple-50 border border-purple-200 rounded-lg">
                                    <span class="text-2xl">🤖</span>
                                    <div>
                                        <p class="font-semibold text-purple-900">AI Generated Content</p>
                                        @if ($artwork->ai_tools_used && is_array($artwork->ai_tools_used))
                                            <p class="text-sm text-purple-700">
                                                Created with: {{ implode(', ', $artwork->ai_tools_used) }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Technical Details -->
                    <div class="metadata-card rounded-lg p-6">
                        <h3 class="font-semibold text-gray-900 mb-4">📊 Technical Details</h3>

                        <div class="space-y-3 text-sm">
                            @if ($artwork->file_type)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">File Type:</span>
                                    <span
                                        class="font-medium">{{ strtoupper(pathinfo($artwork->file_path, PATHINFO_EXTENSION)) }}</span>
                                </div>
                            @endif

                            @if ($artwork->file_size)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">File Size:</span>
                                    <span
                                        class="font-medium">{{ number_format($artwork->file_size / 1024 / 1024, 2) }}
                                        MB</span>
                                </div>
                            @endif

                            @if ($artwork->metadata && is_array($artwork->metadata))
                                @foreach ($artwork->metadata as $key => $value)
                                    @if (in_array($key, ['width', 'height', 'duration', 'bitrate', 'color_space']))
                                        <div class="flex justify-between">
                                            <span
                                                class="text-gray-600">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                            <span class="font-medium">
                                                @if ($key == 'duration')
                                                    {{ gmdate('H:i:s', $value) }}
                                                @elseif($key == 'bitrate')
                                                    {{ number_format($value / 1000) }} kbps
                                                @elseif(in_array($key, ['width', 'height']))
                                                    {{ $value }}px
                                                @else
                                                    {{ $value }}
                                                @endif
                                            </span>
                                        </div>
                                    @endif
                                @endforeach
                            @endif

                            <div class="flex justify-between">
                                <span class="text-gray-600">Uploaded:</span>
                                <span class="font-medium">{{ $artwork->created_at->format('M j, Y g:i A') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- License Info -->
                    <div class="bg-white rounded-lg shadow-sm border p-6">
                        <h3 class="font-semibold text-gray-900 mb-3">⚖️ License & Rights</h3>

                        <div class="space-y-3">
                            <div>
                                <span class="text-sm text-gray-600">License Type:</span>
                                <p class="font-medium">
                                    {{ ucfirst(str_replace('_', ' ', $artwork->license_type ?? 'all_rights_reserved')) }}
                                </p>
                            </div>

                            @if ($artwork->copyright_notice)
                                <div>
                                    <span class="text-sm text-gray-600">Copyright Notice:</span>
                                    <p class="text-sm text-gray-700 bg-gray-50 p-2 rounded mt-1">
                                        {{ $artwork->copyright_notice }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- ACQ Score (if applicable) -->
                    @if (isset($artwork->acq_score) && $artwork->acq_score > 0)
                        <div class="bg-white rounded-lg shadow-sm border p-6">
                            <h3 class="font-semibold text-gray-900 mb-3">🏆 ACQ Score</h3>
                            <div class="text-center">
                                <div class="text-3xl font-bold text-blue-600 mb-2">
                                    {{ number_format($artwork->acq_score, 1) }}</div>
                                <div class="text-sm text-gray-600">
                                    @if ($artwork->acq_criteria && is_array($artwork->acq_criteria))
                                        <div class="mt-2 space-y-1">
                                            @foreach ($artwork->acq_criteria as $criterion => $score)
                                                <div class="flex justify-between">
                                                    <span>{{ ucfirst($criterion) }}:</span>
                                                    <span class="font-medium">{{ $score }}/10</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Owner Actions -->
                    @auth
                        @if (auth()->id() === $artwork->user_id)
                            <div class="bg-white rounded-lg shadow-sm border p-6">
                                <h3 class="font-semibold text-gray-900 mb-3">⚙️ Manage Artwork</h3>
                                <div class="space-y-2">
                                    <a href="{{ route('artworks.edit', $artwork) }}"
                                        class="block w-full text-center bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition-colors">
                                        Edit Artwork
                                    </a>

                                    @if ($artwork->status === 'draft')
                                        <form method="POST" action="{{ route('artworks.publish', $artwork) }}"
                                            class="block">
                                            @csrf
                                            <button type="submit"
                                                class="w-full bg-green-600 text-white py-2 rounded-md hover:bg-green-700 transition-colors">
                                                Publish Artwork
                                            </button>
                                        </form>
                                    @else
                                        <button onclick="unpublishArtwork()"
                                            class="w-full bg-yellow-600 text-white py-2 rounded-md hover:bg-yellow-700 transition-colors">
                                            Unpublish
                                        </button>
                                    @endif

                                    <button onclick="deleteArtwork()"
                                        class="w-full bg-red-600 text-white py-2 rounded-md hover:bg-red-700 transition-colors">
                                        Delete Artwork
                                    </button>
                                </div>
                            </div>
                        @endif
                    @endauth
                </div>
            </div>

            <!-- Comments Section -->
            <div class="mt-12 bg-white rounded-lg shadow-sm border p-6" id="comments-section">
                <h3 class="text-xl font-semibold text-gray-900 mb-6">
                    💬 Comments (<span id="comments-count">{{ $artwork->allComments()->count() }}</span>)
                </h3>

                <!-- Add Comment Form -->
                @auth
                    <form id="comment-form" class="mb-8">
                        @csrf
                        <input type="hidden" name="artwork_id" value="{{ $artwork->id }}">
                        <input type="hidden" name="parent_id" value="" id="parent_id">
                        <div class="mb-4">
                            <textarea name="content" id="comment-content" rows="3" 
                                placeholder="Share your thoughts about this artwork..."
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                required maxlength="2000"></textarea>
                            <div class="text-right text-sm text-gray-500 mt-1">
                                <span id="char-count">0</span>/2000
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <div id="reply-info" class="hidden text-sm text-gray-600">
                                Replying to <span id="reply-username" class="font-medium"></span>
                                <button type="button" onclick="cancelReply()" class="text-blue-600 hover:underline ml-2">Cancel</button>
                            </div>
                            <button type="submit" id="submit-btn"
                                class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors disabled:opacity-50"
                                disabled>
                                <span id="submit-text">Add Comment</span>
                                <span id="loading" class="hidden">Adding...</span>
                            </button>
                        </div>
                    </form>
                @else
                    <div class="mb-8 p-4 bg-gray-50 rounded-lg text-center">
                        <p class="text-gray-600 mb-3">Want to leave a comment?</p>
                        <a href="{{ route('login') }}"
                            class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                            Login to Comment
                        </a>
                    </div>
                @endauth

                <!-- Comments List -->
                <div id="comments-list" class="space-y-6">
                    <!-- Comments will be loaded here via JavaScript -->
                    <div id="comments-loading" class="text-center text-gray-500 py-8">
                        <div class="text-4xl mb-2">⏳</div>
                        <p>Loading comments...</p>
                    </div>
                </div>

                <!-- Load More Button -->
                <div id="load-more-container" class="hidden text-center mt-6">
                    <button id="load-more-btn" onclick="loadMoreComments()" 
                        class="bg-gray-100 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-200 transition-colors">
                        Load More Comments
                    </button>
                </div>
            </div>

            <!-- Related Artworks -->
            <div class="mt-12">
                <h3 class="text-xl font-semibold text-gray-900 mb-6">🎨 Related Artworks</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Placeholder for related artworks -->
                    @for ($i = 0; $i < 4; $i++)
                        <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
                            <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                <div class="text-center text-gray-500">
                                    <div class="text-3xl mb-2">🎨</div>
                                    <div class="text-sm">Related Artwork</div>
                                </div>
                            </div>
                            <div class="p-4">
                                <h4 class="font-semibold text-gray-900 mb-1">Similar Artwork</h4>
                                <p class="text-sm text-gray-600">By Artist Name</p>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleZoom(container) {
            container.classList.toggle('zoomed');
        }

        function shareArtwork() {
            if (navigator.share) {
                navigator.share({
                    title: '{{ $artwork->getTitle() }}',
                    text: 'Check out this artwork by {{ $artwork->user->name }}',
                    url: window.location.href
                });
            } else {
                // Fallback to clipboard
                navigator.clipboard.writeText(window.location.href).then(() => {
                    alert('Link copied to clipboard!');
                });
            }
        }

        function reportArtwork() {
            if (confirm('Are you sure you want to report this artwork?')) {
                // Implement reporting functionality
                alert('Thank you for your report. We will review this artwork.');
            }
        }

        function unpublishArtwork() {
            if (confirm('Are you sure you want to unpublish this artwork? It will no longer be visible to other users.')) {
                // Implement unpublish functionality
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '#';

                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);

                document.body.appendChild(form);
                form.submit();
            }
        }

        function deleteArtwork() {
            if (confirm('Are you sure you want to delete this artwork? This action cannot be undone.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route('artworks.destroy', $artwork) }}';

                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);

                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                form.appendChild(methodInput);

                document.body.appendChild(form);
                form.submit();
            }
        }

        // Comments System JavaScript
        let currentPage = 1;
        let isLoading = false;

        // Initialize comments when page loads
        document.addEventListener('DOMContentLoaded', function() {
            loadComments();
            setupCommentForm();
            setupCharacterCounter();
        });

        function loadComments(page = 1) {
            if (isLoading) return;
            
            isLoading = true;
            const loadingEl = document.getElementById('comments-loading');
            
            if (page === 1) {
                loadingEl.style.display = 'block';
            }

            fetch(`{{ route('comments.get', $artwork) }}?page=${page}`)
                .then(response => response.json())
                .then(data => {
                    if (page === 1) {
                        renderComments(data.comments.data);
                        loadingEl.style.display = 'none';
                    } else {
                        appendComments(data.comments.data);
                    }
                    
                    // Update comments count
                    document.getElementById('comments-count').textContent = data.total_comments;
                    
                    // Show/hide load more button
                    const loadMoreContainer = document.getElementById('load-more-container');
                    if (data.comments.next_page_url) {
                        loadMoreContainer.classList.remove('hidden');
                        currentPage = page;
                    } else {
                        loadMoreContainer.classList.add('hidden');
                    }
                })
                .catch(error => {
                    console.error('Error loading comments:', error);
                    if (page === 1) {
                        loadingEl.innerHTML = '<p class="text-red-500">Failed to load comments. Please refresh the page.</p>';
                    }
                })
                .finally(() => {
                    isLoading = false;
                });
        }

        function renderComments(comments) {
            const commentsList = document.getElementById('comments-list');
            commentsList.innerHTML = '';
            
            if (comments.length === 0) {
                commentsList.innerHTML = `
                    <div class="text-center text-gray-500 py-8">
                        <div class="text-4xl mb-2">💬</div>
                        <p>No comments yet. Be the first to share your thoughts!</p>
                    </div>
                `;
                return;
            }

            comments.forEach(comment => {
                commentsList.appendChild(createCommentElement(comment));
            });
        }

        function appendComments(comments) {
            const commentsList = document.getElementById('comments-list');
            comments.forEach(comment => {
                commentsList.appendChild(createCommentElement(comment));
            });
        }

        function createCommentElement(comment) {
            const commentDiv = document.createElement('div');
            commentDiv.className = 'border-b border-gray-100 pb-4';
            commentDiv.setAttribute('data-comment-id', comment.id);
            
            const isEdited = comment.is_edited ? 
                `<span class="text-xs text-gray-400 ml-2">(edited)</span>` : '';
                
            commentDiv.innerHTML = `
                <div class="flex space-x-3">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                            <span class="text-sm font-medium">${comment.user.name[0].toUpperCase()}</span>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center space-x-2">
                            <p class="text-sm font-medium text-gray-900">${comment.user.name}</p>
                            <p class="text-xs text-gray-500">${formatDate(comment.created_at)}</p>
                            ${isEdited}
                        </div>
                        <div class="mt-1">
                            <p class="text-sm text-gray-700 whitespace-pre-wrap" data-comment-content="${comment.id}">
                                ${comment.content}
                            </p>
                        </div>
                        <div class="mt-2 flex items-center space-x-4">
                            @auth
                            <button onclick="startReply(${comment.id}, '${comment.user.name}')" 
                                class="text-xs text-blue-600 hover:underline">
                                Reply
                            </button>
                            ${comment.user.id === {{ auth()->id() ?? 0 }} ? `
                                <button onclick="editComment(${comment.id})" 
                                    class="text-xs text-gray-600 hover:underline">
                                    Edit
                                </button>
                                <button onclick="deleteComment(${comment.id})" 
                                    class="text-xs text-red-600 hover:underline">
                                    Delete
                                </button>
                            ` : ''}
                            @endauth
                        </div>
                        
                        <!-- Replies -->
                        ${comment.replies && comment.replies.length > 0 ? `
                            <div class="mt-4 pl-4 border-l-2 border-gray-100 space-y-3">
                                ${comment.replies.map(reply => createReplyHTML(reply)).join('')}
                            </div>
                        ` : ''}
                    </div>
                </div>
            `;
            
            return commentDiv;
        }

        function createReplyHTML(reply) {
            const isEdited = reply.is_edited ? 
                `<span class="text-xs text-gray-400 ml-2">(edited)</span>` : '';
                
            return `
                <div class="flex space-x-3" data-comment-id="${reply.id}">
                    <div class="flex-shrink-0">
                        <div class="w-6 h-6 bg-gray-300 rounded-full flex items-center justify-center">
                            <span class="text-xs font-medium">${reply.user.name[0].toUpperCase()}</span>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center space-x-2">
                            <p class="text-sm font-medium text-gray-900">${reply.user.name}</p>
                            <p class="text-xs text-gray-500">${formatDate(reply.created_at)}</p>
                            ${isEdited}
                        </div>
                        <div class="mt-1">
                            <p class="text-sm text-gray-700 whitespace-pre-wrap" data-comment-content="${reply.id}">
                                ${reply.content}
                            </p>
                        </div>
                        <div class="mt-2 flex items-center space-x-4">
                            @auth
                            ${reply.user.id === {{ auth()->id() ?? 0 }} ? `
                                <button onclick="editComment(${reply.id})" 
                                    class="text-xs text-gray-600 hover:underline">
                                    Edit
                                </button>
                                <button onclick="deleteComment(${reply.id})" 
                                    class="text-xs text-red-600 hover:underline">
                                    Delete
                                </button>
                            ` : ''}
                            @endauth
                        </div>
                    </div>
                </div>
            `;
        }

        function setupCommentForm() {
            const form = document.getElementById('comment-form');
            if (!form) return;
            
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                submitComment();
            });
        }

        function setupCharacterCounter() {
            const textarea = document.getElementById('comment-content');
            const counter = document.getElementById('char-count');
            const submitBtn = document.getElementById('submit-btn');
            
            if (!textarea) return;
            
            textarea.addEventListener('input', function() {
                const length = this.value.length;
                counter.textContent = length;
                submitBtn.disabled = length === 0 || length > 2000;
            });
        }

        function submitComment() {
            const form = document.getElementById('comment-form');
            const submitBtn = document.getElementById('submit-btn');
            const submitText = document.getElementById('submit-text');
            const loading = document.getElementById('loading');
            
            submitBtn.disabled = true;
            submitText.classList.add('hidden');
            loading.classList.remove('hidden');
            
            const formData = new FormData(form);
            
            fetch('{{ route('comments.store') }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    form.reset();
                    document.getElementById('char-count').textContent = '0';
                    cancelReply();
                    loadComments(); // Reload comments
                    showMessage(data.message, 'success');
                } else {
                    showMessage(data.message || 'Failed to add comment', 'error');
                }
            })
            .catch(error => {
                console.error('Error submitting comment:', error);
                showMessage('Failed to add comment. Please try again.', 'error');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitText.classList.remove('hidden');
                loading.classList.add('hidden');
            });
        }

        function startReply(commentId, username) {
            document.getElementById('parent_id').value = commentId;
            document.getElementById('reply-username').textContent = username;
            document.getElementById('reply-info').classList.remove('hidden');
            document.getElementById('comment-content').focus();
            document.getElementById('submit-text').textContent = 'Reply';
        }

        function cancelReply() {
            document.getElementById('parent_id').value = '';
            document.getElementById('reply-info').classList.add('hidden');
            document.getElementById('submit-text').textContent = 'Add Comment';
        }

        function loadMoreComments() {
            loadComments(currentPage + 1);
        }

        function editComment(commentId) {
            // Implementation for editing comments
            console.log('Edit comment:', commentId);
        }

        function deleteComment(commentId) {
            if (!confirm('Are you sure you want to delete this comment?')) return;
            
            fetch(`/comments/${commentId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadComments(); // Reload comments
                    showMessage(data.message, 'success');
                } else {
                    showMessage(data.message || 'Failed to delete comment', 'error');
                }
            })
            .catch(error => {
                console.error('Error deleting comment:', error);
                showMessage('Failed to delete comment. Please try again.', 'error');
            });
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diffMs = now - date;
            const diffMins = Math.floor(diffMs / 60000);
            const diffHours = Math.floor(diffMs / 3600000);
            const diffDays = Math.floor(diffMs / 86400000);
            
            if (diffMins < 1) return 'just now';
            if (diffMins < 60) return `${diffMins}m ago`;
            if (diffHours < 24) return `${diffHours}h ago`;
            if (diffDays < 7) return `${diffDays}d ago`;
            
            return date.toLocaleDateString();
        }

        function showMessage(message, type) {
            const div = document.createElement('div');
            div.className = `fixed top-4 right-4 px-6 py-3 rounded-md shadow-lg z-50 ${
                type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
            }`;
            div.textContent = `${type === 'success' ? '✅' : '❌'} ${message}`;
            document.body.appendChild(div);
            setTimeout(() => div.remove(), 5000);
        }

        // Show success/error messages
        @if (session('success'))
            const successDiv = document.createElement('div');
            successDiv.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-md shadow-lg z-50';
            successDiv.textContent = '✅ {{ session('success') }}';
            document.body.appendChild(successDiv);
            setTimeout(() => successDiv.remove(), 5000);
        @endif

        @if (session('error'))
            const errorDiv = document.createElement('div');
            errorDiv.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-md shadow-lg z-50';
            errorDiv.textContent = '❌ {{ session('error') }}';
            document.body.appendChild(errorDiv);
            setTimeout(() => errorDiv.remove(), 5000);
        @endif

        // Update view count after 3 seconds
        setTimeout(() => {
            fetch('{{ route('artworks.show', $artwork) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    track_view: true
                })
            });
        }, 3000);
    </script>
</body>

</html>
