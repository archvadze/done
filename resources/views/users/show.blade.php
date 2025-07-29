<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $user->name }}'s Profile</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="min-h-screen">
    <!-- Navigation -->
    <nav class="nav-background">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="/" class="text-xl font-bold text-primary">ArtGallery</a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="/artworks" class="text-gray-600 hover:text-gray-900">Gallery</a>
                    <a href="/leaderboard" class="text-gray-600 hover:text-gray-900">Leaderboard</a>
                    @auth
                        <a href="/profile" class="text-gray-600 hover:text-gray-900">My Profile</a>
                        <a href="/artworks/create"
                            class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">Upload</a>
                    @else
                        <a href="/login" class="text-gray-600 hover:text-gray-900">Login</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Profile Header -->
        <div class="bg-white overflow-hidden shadow rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        @if ($user->avatar_url)
                            <img class="h-20 w-20 rounded-full" src="{{ $user->avatar_url }}" alt="{{ $user->name }}">
                        @else
                            <div class="h-20 w-20 rounded-full bg-gray-300 flex items-center justify-center">
                                <span class="text-2xl font-medium text-gray-700">{{ substr($user->name, 0, 1) }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="ml-5 flex-1">
                        <div class="flex items-center justify-between">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h1>
                                @if ($user->bio)
                                    <p class="text-gray-600 mt-1">{{ $user->bio }}</p>
                                @endif
                                <div class="flex items-center mt-2 text-sm text-gray-500">
                                    @if ($user->location)
                                        <span class="flex items-center mr-4">
                                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            {{ $user->location }}
                                        </span>
                                    @endif
                                    @if ($user->website)
                                        <a href="{{ $user->website }}" target="_blank"
                                            class="flex items-center mr-4 text-blue-600 hover:underline">
                                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                            </svg>
                                            Website
                                        </a>
                                    @endif
                                    <span>Member since {{ $user->created_at->format('M Y') }}</span>
                                </div>
                            </div>
                            <div class="text-right">
                                @auth
                                    @if (auth()->id() === $user->id)
                                        <a href="/profile/edit"
                                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                                            Edit Profile
                                        </a>
                                    @else
                                        <div class="flex items-center space-x-3">
                                            <!-- Follow Button -->
                                            <button id="follow-btn" onclick="toggleFollow({{ $user->id }})"
                                                class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ auth()->user()->isFollowing($user) ? 'bg-gray-200 text-gray-700 hover:bg-gray-300' : 'bg-blue-600 text-white hover:bg-blue-700' }}">
                                                <span
                                                    id="follow-text">{{ auth()->user()->isFollowing($user) ? 'Following' : 'Follow' }}</span>
                                            </button>

                                            <!-- Message Button -->
                                            <button
                                                class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-md text-sm font-medium">
                                                Message
                                            </button>
                                        </div>
                                    @endif
                                @else
                                    <a href="/login"
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                                        Follow
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Testing Navigation Panel (Only for development) -->
        @if (app()->environment(['local', 'development']) || request()->has('debug'))
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                <h3 class="text-lg font-semibold text-yellow-800 mb-3">🧪 Testing Navigation</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    @if ($user->isArtist())
                        <div class="bg-white p-3 rounded border">
                            <h4 class="font-medium text-gray-900">👩‍🎨 Alice (Artist)</h4>
                            <div class="mt-2 space-y-1 text-sm">
                                <a href="/users/{{ $user->id }}" class="block text-blue-600 hover:underline">👤
                                    Profile</a>
                                <a href="/users/{{ $user->id }}/followers"
                                    class="block text-blue-600 hover:underline">👥 Followers</a>
                                <a href="/users/{{ $user->id }}/following"
                                    class="block text-blue-600 hover:underline">➡️ Following</a>
                            </div>
                        </div>
                        <div class="bg-white p-3 rounded border">
                            <h4 class="font-medium text-gray-900">🎨 Alice's Artworks</h4>
                            <div class="mt-2 space-y-1 text-sm">
                                @foreach ($user->artworks()->where('status', 'published')->take(3)->get() as $artwork)
                                    <a href="/artworks/{{ $artwork->id }}"
                                        class="block text-blue-600 hover:underline">
                                        🖼️ {{ $artwork->getTitle() }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if ($user->isModerator())
                        <div class="bg-white p-3 rounded border">
                            <h4 class="font-medium text-gray-900">🛡️ Bob (Moderator)</h4>
                            <div class="mt-2 space-y-1 text-sm">
                                <a href="/users/{{ $user->id }}" class="block text-blue-600 hover:underline">👤
                                    Profile</a>
                                <a href="/users/{{ $user->id }}/followers"
                                    class="block text-blue-600 hover:underline">👥 Followers</a>
                                <a href="/users/{{ $user->id }}/following"
                                    class="block text-blue-600 hover:underline">➡️ Following</a>
                            </div>
                        </div>
                        <div class="bg-white p-3 rounded border">
                            <h4 class="font-medium text-gray-900">⚖️ Evaluation Tasks</h4>
                            <div class="mt-2 space-y-1 text-sm">
                                @php
                                    $otherUsers = \App\Models\User::where('id', '!=', $user->id)->get();
                                @endphp
                                @foreach ($otherUsers as $otherUser)
                                    @foreach ($otherUser->artworks()->where('status', 'published')->take(2)->get() as $artwork)
                                        <a href="/artworks/{{ $artwork->id }}/evaluations/create"
                                            class="block text-purple-600 hover:underline">
                                            📊 Evaluate: {{ $artwork->getTitle() }}
                                        </a>
                                    @endforeach
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Quick Links -->
                    <div class="bg-white p-3 rounded border">
                        <h4 class="font-medium text-gray-900">🔗 Quick Links</h4>
                        <div class="mt-2 space-y-1 text-sm">
                            <a href="/artworks" class="block text-blue-600 hover:underline">🖼️ All Artworks</a>
                            <a href="/leaderboard" class="block text-blue-600 hover:underline">🏆 Leaderboard</a>
                            <a href="/locale/ka" class="block text-blue-600 hover:underline">🇬🇪 ქართული</a>
                            <a href="/locale/en" class="block text-blue-600 hover:underline">🇺🇸 English</a>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Social Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Artworks</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['artworks_count'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    <a href="{{ route('users.followers', $user) }}"
                                        class="hover:text-gray-700">Followers</a>
                                </dt>
                                <dd class="text-lg font-medium text-gray-900" id="followers-count">
                                    {{ $user->followers_count }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    <a href="{{ route('users.following', $user) }}"
                                        class="hover:text-gray-700">Following</a>
                                </dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $user->following_count }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Avg ACQ Score</dt>
                                <dd class="text-lg font-medium text-gray-900">
                                    {{ $stats['avg_acq_score'] ? number_format($stats['avg_acq_score'], 1) : 'N/A' }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Artworks -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Recent Artworks</h3>
                    @if ($stats['artworks_count'] > 6)
                        <a href="{{ route('users.artworks', $user) }}"
                            class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            View All ({{ $stats['artworks_count'] }})
                        </a>
                    @endif
                </div>

                @if ($artworks->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach ($artworks as $artwork)
                            <div class="border rounded-lg overflow-hidden hover:shadow-md transition-shadow">
                                <div class="aspect-video bg-gray-200 flex items-center justify-center">
                                    @if ($artwork->file_path)
                                        <div class="w-full h-full bg-gray-300 flex items-center justify-center">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @else
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    @endif
                                </div>
                                <div class="p-4">
                                    <h4 class="font-semibold text-gray-900 mb-1">
                                        {{ $artwork->getTitle() ?? 'Untitled' }}</h4>
                                    <p class="text-sm text-gray-600 mb-2">
                                        {{ ucfirst($artwork->category ?? 'uncategorized') }}</p>
                                    <div class="flex items-center justify-between text-sm text-gray-500">
                                        <div class="flex items-center space-x-2">
                                            <span>{{ $artwork->view_count }} views</span>
                                            <span>{{ $artwork->like_count }} likes</span>
                                        </div>
                                        @if ($artwork->acq_score)
                                            <span class="font-medium text-blue-600">ACQ:
                                                {{ number_format($artwork->acq_score, 1) }}</span>
                                        @endif
                                    </div>
                                    <div class="mt-3">
                                        <a href="/artworks/{{ $artwork->id }}"
                                            class="text-blue-600 hover:text-blue-800 text-sm">View Artwork</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12 text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <p class="mt-2">No public artworks yet</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        // Follow/Unfollow functionality
        async function toggleFollow(userId) {
            const followBtn = document.getElementById('follow-btn');
            const followText = document.getElementById('follow-text');
            const followersCount = document.getElementById('followers-count');

            // Disable button during request
            followBtn.disabled = true;

            try {
                const response = await fetch(`/users/${userId}/follow`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    }
                });

                const data = await response.json();

                if (data.success) {
                    // Update button text and style
                    if (data.action === 'followed') {
                        followText.textContent = 'Following';
                        followBtn.className =
                            'px-4 py-2 rounded-md text-sm font-medium transition-colors bg-gray-200 text-gray-700 hover:bg-gray-300';
                    } else {
                        followText.textContent = 'Follow';
                        followBtn.className =
                            'px-4 py-2 rounded-md text-sm font-medium transition-colors bg-blue-600 text-white hover:bg-blue-700';
                    }

                    // Update followers count
                    followersCount.textContent = data.followers_count;

                    // Show success message
                    showMessage(`Successfully ${data.action} user!`, 'success');
                } else {
                    showMessage(data.message || 'Failed to toggle follow', 'error');
                }
            } catch (error) {
                console.error('Follow error:', error);
                showMessage('Failed to toggle follow. Please try again.', 'error');
            } finally {
                followBtn.disabled = false;
            }
        }

        // Show message helper
        function showMessage(message, type) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 ${
                type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
            }`;
            messageDiv.textContent = message;

            document.body.appendChild(messageDiv);

            setTimeout(() => {
                messageDiv.remove();
            }, 3000);
        }
    </script>
</body>

</html>
