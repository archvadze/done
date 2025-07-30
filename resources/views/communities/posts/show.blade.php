<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $post->title }} - {{ $community->name }} - Acumen Craft</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
                            <a href="{{ route('communities.index') }}" class="text-gray-600 hover:text-gray-900">Communities</a>
                            <a href="{{ route('communities.show', $community->slug) }}" class="text-blue-600 hover:text-blue-700">{{ $community->name }}</a>
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

        <!-- Main Content -->
        <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <!-- Breadcrumb -->
            <div class="mb-6">
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-4">
                        <li>
                            <a href="{{ route('communities.index') }}" class="text-gray-500 hover:text-gray-700">Communities</a>
                        </li>
                        <li>
                            <span class="text-gray-400">/</span>
                        </li>
                        <li>
                            <a href="{{ route('communities.show', $community->slug) }}" class="text-gray-500 hover:text-gray-700">{{ $community->name }}</a>
                        </li>
                        <li>
                            <span class="text-gray-400">/</span>
                        </li>
                        <li>
                            <span class="text-gray-900 font-medium">{{ $post->title }}</span>
                        </li>
                    </ol>
                </nav>
            </div>

            <!-- Post Content -->
            <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
                <!-- Post Header -->
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold">
                            {{ substr($post->user->name, 0, 1) }}
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">{{ $post->user->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $post->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        @if($post->is_pinned)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                📌 Pinned
                            </span>
                        @endif
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            {{ ucfirst($post->type) }}
                        </span>
                    </div>
                </div>

                <!-- Post Title -->
                <h1 class="text-2xl font-bold text-gray-900 mb-4">{{ $post->title }}</h1>

                <!-- Post Content -->
                <div class="prose max-w-none mb-6">
                    <p class="text-gray-700 leading-relaxed">{{ $post->content }}</p>
                </div>

                <!-- Post Stats -->
                <div class="flex items-center space-x-6 text-sm text-gray-500 border-t pt-4">
                    <div class="flex items-center space-x-1">
                        <span>👁️</span>
                        <span>{{ $post->view_count }} views</span>
                    </div>
                    <div class="flex items-center space-x-1">
                        <span>❤️</span>
                        <span>{{ $post->like_count }} likes</span>
                    </div>
                    <div class="flex items-center space-x-1">
                        <span>💬</span>
                        <span>{{ $post->comment_count }} comments</span>
                    </div>
                    <div class="flex items-center space-x-1">
                        <span>📅</span>
                        <span>{{ $post->created_at->format('M j, Y') }}</span>
                    </div>
                </div>

                <!-- Action Buttons -->
                @auth
                    <div class="flex items-center space-x-4 mt-4 pt-4 border-t">
                        <button class="flex items-center space-x-2 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                            <span>❤️</span>
                            <span>Like</span>
                        </button>
                        <button class="flex items-center space-x-2 px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                            <span>💬</span>
                            <span>Comment</span>
                        </button>
                        @if($post->user_id === auth()->id() || $community->isAdmin(auth()->user()))
                            <a href="{{ route('communities.posts.edit', [$community->slug, $post->id]) }}" class="flex items-center space-x-2 px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors">
                                <span>✏️</span>
                                <span>Edit</span>
                            </a>
                        @endif
                    </div>
                @endauth
            </div>

            <!-- Comments Section -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Comments ({{ $post->comments->count() }})</h2>

                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @auth
                    <!-- Comment Form -->
                    <div class="mb-8">
                        <form action="{{ route('comments.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <input type="hidden" name="commentable_type" value="App\Models\CommunityPost">
                            <input type="hidden" name="commentable_id" value="{{ $post->id }}">
                            <div>
                                <label for="comment" class="sr-only">Add a comment</label>
                                <textarea 
                                    id="comment" 
                                    name="content" 
                                    rows="3" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                                    placeholder="Share your thoughts..."
                                    required
                                ></textarea>
                            </div>
                            <div class="flex justify-end">
                                <button 
                                    type="submit" 
                                    class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors font-medium"
                                >
                                    Post Comment
                                </button>
                            </div>
                        </form>
                    </div>
                @else
                    <div class="mb-8 p-4 bg-gray-50 rounded-lg text-center">
                        <p class="text-gray-600">
                            <a href="{{ route('login') }}" class="text-blue-500 hover:text-blue-600">Sign in</a> 
                            to join the discussion
                        </p>
                    </div>
                @endauth

                <!-- Comments List -->
                <div class="space-y-6">
                    @forelse($post->comments as $comment)
                        <div class="flex space-x-4">
                            <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-blue-600 rounded-full flex items-center justify-center text-white text-sm font-semibold flex-shrink-0">
                                {{ substr($comment->user->name, 0, 1) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center space-x-2">
                                            <h4 class="font-semibold text-gray-900">{{ $comment->user->name }}</h4>
                                            <span class="text-sm text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                                        </div>
                                        @if($comment->is_edited)
                                            <span class="text-xs text-gray-400">(edited)</span>
                                        @endif
                                    </div>
                                    <p class="text-gray-700">{{ $comment->content }}</p>
                                </div>
                                
                                @auth
                                    <div class="flex items-center space-x-4 mt-2 text-sm">
                                        <button class="text-gray-500 hover:text-blue-600">Reply</button>
                                        @if($comment->user_id === auth()->id())
                                            <button class="text-gray-500 hover:text-yellow-600">Edit</button>
                                            <button class="text-gray-500 hover:text-red-600">Delete</button>
                                        @endif
                                    </div>
                                @endauth

                                <!-- Replies (if any) -->
                                @if($comment->replies->count() > 0)
                                    <div class="mt-4 space-y-4">
                                        @foreach($comment->replies as $reply)
                                            <div class="flex space-x-3">
                                                <div class="w-6 h-6 bg-gradient-to-br from-purple-500 to-pink-600 rounded-full flex items-center justify-center text-white text-xs font-semibold flex-shrink-0">
                                                    {{ substr($reply->user->name, 0, 1) }}
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="bg-white border rounded-lg p-3">
                                                        <div class="flex items-center justify-between mb-1">
                                                            <div class="flex items-center space-x-2">
                                                                <h5 class="font-medium text-gray-900 text-sm">{{ $reply->user->name }}</h5>
                                                                <span class="text-xs text-gray-500">{{ $reply->created_at->diffForHumans() }}</span>
                                                            </div>
                                                        </div>
                                                        <p class="text-gray-700 text-sm">{{ $reply->content }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <div class="text-gray-400 text-4xl mb-4">💬</div>
                            <p class="text-gray-500">No comments yet. Be the first to share your thoughts!</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Back to Community -->
            <div class="mt-8 text-center">
                <a href="{{ route('communities.show', $community->slug) }}" class="inline-flex items-center space-x-2 px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    <span>←</span>
                    <span>Back to {{ $community->name }}</span>
                </a>
            </div>
        </div>
    </div>
</body>

</html>
