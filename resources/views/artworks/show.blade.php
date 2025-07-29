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
            max-width: 100%;
            object-fit: contain;
        }

        .zoom-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 300px;
        }

        .zoom-container img {
            max-width: 100%;
            height: auto;
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

        /* Fix for responsive images */
        .artwork-container {
            position: relative;
            width: 100%;
            overflow: hidden;
        }
    </style>
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="nav-background">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center space-x-4">
                        <a href="{{ url('/') }}" class="text-xl font-bold text-primary">
                            🎨 Acumen Craft
                        </a>
                        <nav class="hidden md:flex space-x-4">
                            <a href="{{ route('artworks.index') }}" class="text-primary hover:text-primary-dark">← Back to
                                Gallery</a>
                        </nav>
                    </div>
                    <div class="flex items-center space-x-4">
                        <x-locale-switcher />
                        @auth
                            @if (auth()->id() === $artwork->user_id)
                                <a href="{{ route('artworks.edit', $artwork) }}"
                                    class="text-secondary hover:text-primary">Edit</a>
                            @endif
                            <a href="{{ route('artworks.create') }}"
                                class="btn-primary px-4 py-2">
                                Upload
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="text-secondary hover:text-primary">Login</a>
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
                    <div class="bg-white rounded-lg shadow-sm border overflow-hidden artwork-container">
                        @if ($artwork->file_path)
                            @if (Str::startsWith($artwork->file_type, 'image/'))
                                <!-- Image -->
                                <div class="zoom-container">
                                    <img src="{{ $artwork->getThumbnailUrl() }}" alt="{{ $artwork->getTitle() }}"
                                        class="artwork-main cursor-pointer"
                                        onclick="openFileModal('{{ Storage::url($artwork->file_path) }}', '{{ $artwork->getTitle() }}')">
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
                                    @if ($artwork->thumbnail_path)
                                        <div class="mb-4">
                                            <img src="{{ $artwork->getThumbnailUrl() }}"
                                                alt="{{ $artwork->getTitle() }}"
                                                class="mx-auto rounded-lg shadow-lg max-w-xs max-h-64 object-cover cursor-pointer"
                                                onclick="openFileModal('{{ Storage::url($artwork->file_path) }}', '{{ $artwork->getTitle() }}')">
                                        </div>
                                    @else
                                        <div class="text-8xl mb-4">🎵</div>
                                    @endif
                                    <h3 class="text-xl font-semibold mb-4">{{ $artwork->getTitle() }}</h3>
                                    <audio controls class="w-full max-w-md mx-auto">
                                        <source src="{{ Storage::url($artwork->file_path) }}"
                                            type="{{ $artwork->file_type }}">
                                        Your browser does not support the audio tag.
                                    </audio>
                                </div>
                            @else
                                <!-- Other File Types -->
                                @php
                                    $fileExtension = pathinfo($artwork->file_path, PATHINFO_EXTENSION);
                                    $fileIcon = match (strtolower($fileExtension)) {
                                        'pdf' => '📄',
                                        'doc', 'docx' => '📝',
                                        'txt', 'md' => '📋',
                                        'zip', 'rar', '7z' => '🗜️',
                                        'exe', 'app' => '⚙️',
                                        'json', 'xml', 'csv' => '📊',
                                        default => '📎',
                                    };
                                    $fileTypeLabel = match (strtolower($fileExtension)) {
                                        'pdf' => 'PDF Document',
                                        'doc', 'docx' => 'Word Document',
                                        'txt' => 'Text File',
                                        'md' => 'Markdown File',
                                        'zip', 'rar', '7z' => 'Archive File',
                                        'exe', 'app' => 'Application',
                                        'json' => 'JSON Data',
                                        'xml' => 'XML Data',
                                        'csv' => 'CSV Data',
                                        default => strtoupper($fileExtension) . ' File',
                                    };
                                @endphp
                                <div class="bg-gradient-to-br from-gray-50 to-gray-100 p-8 text-center">
                                    @if ($artwork->thumbnail_path)
                                        <div class="mb-4">
                                            <img src="{{ $artwork->getThumbnailUrl() }}"
                                                alt="{{ $artwork->getTitle() }}"
                                                class="mx-auto rounded-lg shadow-lg max-w-xs max-h-64 object-cover cursor-pointer"
                                                onclick="openFileModal('{{ Storage::url($artwork->file_path) }}', '{{ $artwork->getTitle() }}')">
                                        </div>
                                    @else
                                        <div class="text-8xl mb-4">{{ $fileIcon }}</div>
                                    @endif
                                    <h3 class="text-xl font-semibold mb-2">{{ $artwork->getTitle() }}</h3>
                                    <p class="text-gray-600 mb-4">{{ $fileTypeLabel }}</p>
                                    <a href="#"
                                        onclick="openFileModal('{{ Storage::url($artwork->file_path) }}', '{{ $artwork->getTitle() }}')"
                                        class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 transition-colors inline-flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                            </path>
                                        </svg>
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
                    <div class="flex items-center justify-between mt-6 bg-background px-6 py-4">
                        <div class="flex items-center space-x-6">
                            <!-- Like Button -->
                            @auth
                                <form method="POST" action="{{ route('artworks.like', $artwork) }}" class="inline">
                                    @csrf
                                    <button type="submit"
                                        class="likes-btn flex items-center space-x-2 transition-colors">
                                        <span
                                            class="text-xl">{{ $artwork->isLikedBy(auth()->user()) ? '❤️' : '🤍' }}</span>
                                        <span class="font-medium">{{ $artwork->likes_count }}
                                            {{ $artwork->likes_count == 1 ? 'Like' : 'Likes' }}</span>
                                    </button>
                                </form>
                            @else
                                <div class="flex items-center space-x-2">
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

                                    <!-- NFT Mint Button -->
                                    @if($artwork->nft)
                                        <a href="{{ route('nft.show', $artwork->nft) }}"
                                            class="block w-full text-center bg-gradient-to-r from-purple-600 to-blue-600 text-white py-2 rounded-md hover:from-purple-700 hover:to-blue-700 transition-colors">
                                            🎨 View NFT
                                        </a>
                                    @else
                                        <a href="{{ route('nft.mint', $artwork) }}"
                                            class="block w-full text-center bg-gradient-to-r from-purple-600 to-blue-600 text-white py-2 rounded-md hover:from-purple-700 hover:to-blue-700 transition-colors">
                                            🎨 Mint as NFT
                                        </a>
                                    @endif

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
                                        <form method="POST" action="{{ route('artworks.unpublish', $artwork) }}"
                                            class="block">
                                            @csrf
                                            <button type="submit"
                                                class="w-full bg-yellow-600 text-white py-2 rounded-md hover:bg-yellow-700 transition-colors">
                                                Unpublish
                                            </button>
                                        </form>
                                    @endif

                                    <button onclick="deleteArtwork()"
                                        class="w-full bg-red-600 text-white py-2 rounded-md hover:bg-red-700 transition-colors">
                                        Delete Artwork
                                    </button>
                                </div>
                            </div>
                        @endif
                    @endauth

                    <!-- Evaluation Section -->
                    @auth
                        @if (auth()->user()->canEvaluate() && auth()->user()->canEvaluateArtwork($artwork))
                            <div class="bg-white rounded-lg shadow-sm border p-6">
                                <h3 class="font-semibold text-gray-900 mb-3">🎯 ACQ Evaluation</h3>
                                <p class="text-sm text-gray-600 mb-4">
                                    As a moderator, you can evaluate this artwork using our ACQ scoring system.
                                </p>
                                <a href="{{ route('evaluations.create', $artwork) }}"
                                    class="block w-full text-center bg-purple-600 text-white py-2 rounded-md hover:bg-purple-700 transition-colors">
                                    📊 Evaluate Artwork
                                </a>
                            </div>
                        @elseif (auth()->user()->canEvaluate() && auth()->id() === $artwork->user_id)
                            <div class="bg-white rounded-lg shadow-sm border p-6">
                                <h3 class="font-semibold text-gray-900 mb-3">🎯 ACQ Evaluation</h3>
                                <p class="text-sm text-gray-500 mb-4">
                                    You cannot evaluate your own artwork.
                                </p>
                                <button disabled
                                    class="block w-full text-center bg-gray-300 text-gray-500 py-2 rounded-md cursor-not-allowed">
                                    📊 Evaluate Artwork
                                </button>
                            </div>
                        @elseif (!auth()->user()->canEvaluate() && auth()->user()->isArtist())
                            <div class="bg-white rounded-lg shadow-sm border p-6">
                                <h3 class="font-semibold text-gray-900 mb-3">🎯 ACQ Evaluation</h3>
                                <p class="text-sm text-gray-500 mb-4">
                                    Only moderators and admins can evaluate artworks.
                                </p>
                                <button disabled
                                    class="block w-full text-center bg-gray-300 text-gray-500 py-2 rounded-md cursor-not-allowed">
                                    📊 Evaluate Artwork
                                </button>
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
                            <textarea name="content" id="comment-content" rows="3" placeholder="Share your thoughts about this artwork..."
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                required maxlength="2000"></textarea>
                            <div class="text-right text-sm text-gray-500 mt-1">
                                <span id="char-count">0</span>/2000
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <div id="reply-info" class="hidden text-sm text-gray-600">
                                Replying to <span id="reply-username" class="font-medium"></span>
                                <button type="button" onclick="cancelReply()"
                                    class="text-blue-600 hover:underline ml-2">Cancel</button>
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

            // Create or show loading element
            const commentsList = document.getElementById('comments-list');
            if (!commentsList) {
                console.error('Comments list element not found');
                isLoading = false;
                return;
            }

            if (page === 1) {
                // If loading element doesn't exist, create it
                if (!loadingEl) {
                    const newLoadingEl = document.createElement('div');
                    newLoadingEl.id = 'comments-loading';
                    newLoadingEl.className = 'text-center text-gray-500 py-8';
                    newLoadingEl.innerHTML = `
                        <div class="text-4xl mb-2">⏳</div>
                        <p>Loading comments...</p>
                    `;
                    commentsList.innerHTML = '';
                    commentsList.appendChild(newLoadingEl);
                } else {
                    loadingEl.style.display = 'block';
                }
            }

            fetch(`{{ route('comments.get', $artwork) }}?page=${page}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => {
                    console.log('Comments response status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Comments data received:', data);
                    if (page === 1) {
                        renderComments(data.comments.data);
                        // Hide or remove loading element
                        const currentLoadingEl = document.getElementById('comments-loading');
                        if (currentLoadingEl) {
                            currentLoadingEl.style.display = 'none';
                        }
                    } else {
                        appendComments(data.comments.data);
                    }

                    // Update comments count
                    const commentsCountEl = document.getElementById('comments-count');
                    if (commentsCountEl) {
                        commentsCountEl.textContent = data.total_comments;
                    }

                    // Show/hide load more button
                    const loadMoreContainer = document.getElementById('load-more-container');
                    if (loadMoreContainer && data.comments.next_page_url) {
                        loadMoreContainer.classList.remove('hidden');
                        currentPage = page;
                    } else if (loadMoreContainer) {
                        loadMoreContainer.classList.add('hidden');
                    }
                })
                .catch(error => {
                    console.error('Error loading comments:', error);
                    if (page === 1) {
                        const currentLoadingEl = document.getElementById('comments-loading');
                        if (currentLoadingEl) {
                            currentLoadingEl.innerHTML =
                                '<p class="text-red-500">Failed to load comments. Please refresh the page.</p>';
                        }
                    }
                })
                .finally(() => {
                    isLoading = false;
                });
        }

        function renderComments(comments) {
            const commentsList = document.getElementById('comments-list');

            if (!commentsList) {
                console.error('Comments list element not found');
                return;
            }

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
                try {
                    const commentElement = createCommentElement(comment);
                    if (commentElement) {
                        commentsList.appendChild(commentElement);
                    }
                } catch (error) {
                    console.error('Error creating comment element:', error);
                }
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

            // Disable form immediately
            submitBtn.disabled = true;
            submitText.classList.add('hidden');
            loading.classList.remove('hidden');

            const formData = new FormData(form);

            fetch('{{ route('comments.store') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(async response => {
                    console.log('Submit comment response status:', response.status);
                    const data = await response.json();
                    console.log('Submit comment data:', data);

                    if (response.ok && data.success) {
                        // Reset form immediately
                        form.reset();
                        document.getElementById('char-count').textContent = '0';
                        cancelReply();

                        // Show success message
                        showMessage(data.message || 'Comment added successfully!', 'success');

                        // Wait a moment then reload comments to ensure database consistency
                        setTimeout(() => {
                            loadComments(); // Reload comments
                        }, 100);

                    } else {
                        // Handle validation errors
                        if (data.errors) {
                            const errorMessages = Object.values(data.errors).flat();
                            showMessage(errorMessages.join(', '), 'error');
                        } else {
                            showMessage(data.message || 'Failed to add comment', 'error');
                        }
                    }
                })
                .catch(error => {
                    console.error('Error submitting comment:', error);
                    showMessage('Failed to add comment. Please try again.', 'error');
                })
                .finally(() => {
                    // Re-enable form
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
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(async response => {
                    console.log('Delete comment response status:', response.status);
                    const data = await response.json();
                    console.log('Delete comment data:', data);

                    if (response.ok && data.success) {
                        showMessage(data.message || 'Comment deleted successfully!', 'success');
                        // Wait a moment then reload comments to ensure database consistency
                        setTimeout(() => {
                            loadComments(); // Reload comments
                        }, 100);
                    } else {
                        if (data.errors) {
                            const errorMessages = Object.values(data.errors).flat();
                            showMessage(errorMessages.join(', '), 'error');
                        } else {
                            showMessage(data.message || 'Failed to delete comment', 'error');
                        }
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

        // Update view count after 3 seconds - REMOVED because it's causing 405 errors
        // setTimeout(() => {
        //     fetch('{{ route('artworks.show', $artwork) }}', {
        //         method: 'POST',
        //         headers: {
        //             'X-CSRF-TOKEN': '{{ csrf_token() }}',
        //             'Content-Type': 'application/json'
        //         },
        //         body: JSON.stringify({
        //             track_view: true
        //         })
        //     });
        // }, 3000);

        // File Modal Function - Enhanced for Moderation/Evaluation
        function openFileModal(fileUrl, title) {
            // Determine file type from URL
            const fileExtension = fileUrl.split('.').pop().toLowerCase();
            const isImage = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'].includes(fileExtension);
            const isVideo = ['mp4', 'webm', 'ogg', 'avi', 'mov'].includes(fileExtension);
            const isAudio = ['mp3', 'wav', 'ogg', 'aac', 'm4a'].includes(fileExtension);
            const isText = ['txt', 'md', 'json', 'csv', 'xml'].includes(fileExtension);
            const isPdf = fileExtension === 'pdf';

            // Create modal overlay
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-black bg-opacity-95 flex items-center justify-center z-50 p-2';
            modal.onclick = () => modal.remove();

            // Create modal content - larger for better moderation
            const content = document.createElement('div');
            content.className = 'bg-white p-6 rounded-xl max-w-7xl w-full max-h-[98vh] overflow-auto shadow-2xl';
            content.onclick = (e) => e.stopPropagation();

            let mediaContent = '';

            if (isImage) {
                mediaContent = `
                    <div class="text-center">
                        <img src="${fileUrl}" alt="${title}" class="max-w-full max-h-[85vh] mx-auto rounded-lg shadow-lg object-contain bg-gray-50">
                    </div>`;
            } else if (isVideo) {
                mediaContent = `
                    <div class="text-center">
                        <video controls class="max-w-full max-h-[80vh] mx-auto rounded-lg shadow-lg">
                            <source src="${fileUrl}" type="video/${fileExtension}">
                            Your browser does not support the video tag.
                        </video>
                    </div>`;
            } else if (isAudio) {
                mediaContent = `
                    <div class="text-center bg-gradient-to-br from-purple-100 to-blue-100 p-12 rounded-lg">
                        <div class="text-9xl mb-8">🎵</div>
                        <h3 class="text-2xl font-semibold mb-6 text-gray-800">${title}</h3>
                        <audio controls class="w-full max-w-lg mx-auto shadow-lg rounded-lg">
                            <source src="${fileUrl}" type="audio/${fileExtension}">
                            Your browser does not support the audio element.
                        </audio>
                    </div>`;
            } else if (isPdf) {
                mediaContent = `
                    <div class="text-center">
                        <div class="text-6xl mb-4">📄</div>
                        <p class="text-gray-600 mb-4 text-lg font-medium">PDF Document</p>
                        <iframe src="${fileUrl}" class="w-full h-[80vh] border rounded-lg shadow-inner bg-gray-50"></iframe>
                    </div>`;
            } else if (isText) {
                // For text files, we'll show a preview
                mediaContent = `
                    <div class="text-center">
                        <div class="text-6xl mb-4">📝</div>
                        <p class="text-gray-600 mb-4 text-lg font-medium">Text File</p>
                        <div class="bg-gray-50 p-6 rounded-lg text-left max-h-[70vh] overflow-auto border shadow-inner">
                            <pre id="text-content" class="text-sm text-gray-800 whitespace-pre-wrap">Loading content...</pre>
                        </div>
                    </div>`;
            } else {
                mediaContent = `
                    <div class="text-center">
                        <div class="text-8xl mb-6">📎</div>
                        <p class="text-gray-600 mb-4 text-xl">File: ${fileExtension.toUpperCase()}</p>
                        <p class="text-gray-500">Use the download button below to view this file</p>
                    </div>`;
            }

            content.innerHTML = `
                <div class="flex justify-between items-center mb-6 pb-4 border-b">
                    <div>
                        <h3 class="text-2xl font-semibold text-gray-800">${title}</h3>
                        <p class="text-sm text-gray-500 mt-1">File Type: ${fileExtension.toUpperCase()}</p>
                    </div>
                    <button onclick="this.closest('.fixed').remove()" class="text-gray-500 hover:text-gray-700 transition-colors p-2 hover:bg-gray-100 rounded-lg">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="mb-8">
                    ${mediaContent}
                </div>
                <div class="flex justify-center space-x-4 pt-4 border-t">
                    <a href="${fileUrl}" download class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 transition-colors flex items-center shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Download
                    </a>
                    <a href="${fileUrl}" target="_blank" class="bg-gray-600 text-white px-8 py-3 rounded-lg hover:bg-gray-700 transition-colors flex items-center shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                        </svg>
                        Open in New Tab
                    </a>
                    <button onclick="this.closest('.fixed').remove()" class="bg-red-500 text-white px-8 py-3 rounded-lg hover:bg-red-600 transition-colors shadow-lg">
                        Close
                    </button>
                </div>
            `;

            modal.appendChild(content);
            document.body.appendChild(modal);

            // Add keyboard event listener to close modal with Escape key
            const closeModal = (e) => {
                if (e.key === 'Escape') {
                    modal.remove();
                    document.removeEventListener('keydown', closeModal);
                }
            };
            document.addEventListener('keydown', closeModal);

            // Load text content if it's a text file
            if (isText) {
                fetch(fileUrl)
                    .then(response => response.text())
                    .then(text => {
                        const textContent = document.getElementById('text-content');
                        if (textContent) {
                            textContent.textContent = text.substring(0, 5000) + (text.length > 5000 ?
                                '\n\n... (truncated)' : '');
                        }
                    })
                    .catch(error => {
                        const textContent = document.getElementById('text-content');
                        if (textContent) {
                            textContent.textContent = 'Error loading file content.';
                        }
                    });
            }
        }
    </script>
</body>

</html>
