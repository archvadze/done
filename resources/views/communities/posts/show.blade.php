@extends('layouts.app')

@section('title', $post->title . ' - ' . $community->name)

@section('content')
<div class="min-h-screen bg-primary">
    <div class="max-w-4xl mx-auto px-4 py-8">
        <!-- Navigation -->
        <nav class="text-gray-400 mb-8">
            <a href="{{ route('communities.index') }}" class="hover:text-white">Communities</a>
            <span class="mx-2">/</span>
            <a href="{{ route('communities.show', $community) }}" class="hover:text-white">{{ $community->name }}</a>
            <span class="mx-2">/</span>
            <span class="text-white">{{ $post->title }}</span>
        </nav>

        <!-- Post Content -->
        <div class="bg-secondary p-8 mb-6">
            <!-- Post Header -->
            <div class="flex items-start justify-between mb-6">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-primary rounded-full flex items-center justify-center">
                        <span class="text-white font-bold">{{ substr($post->user->name, 0, 1) }}</span>
                    </div>
                    <div>
                        <h3 class="text-white font-semibold">{{ $post->user->name }}</h3>
                        <p class="text-gray-400 text-sm">{{ $post->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                
                <!-- Post Type Badge -->
                <span class="px-3 py-1 text-xs font-semibold bg-secondary text-white rounded-full">
                    {{ ucfirst($post->type) }}
                </span>
            </div>

            <!-- Post Title -->
            <h1 class="text-3xl font-bold text-white mb-4">{{ $post->title }}</h1>

            <!-- Post Content -->
            <div class="prose prose-invert prose-lg max-w-none mb-6">
                <div class="text-gray-300 whitespace-pre-line">{{ $post->content }}</div>
            </div>

            <!-- Attachments -->
            @if($post->attachments && count($post->attachments) > 0)
                <div class="mb-6">
                    <h3 class="text-white font-semibold mb-3">Attachments</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($post->attachments as $attachment)
                            <div class="bg-primary p-4">
                                @if(str_contains($attachment['name'], '.jpg') || str_contains($attachment['name'], '.png') || str_contains($attachment['name'], '.gif'))
                                    <img src="{{ asset('storage/' . $attachment['path']) }}" 
                                         alt="{{ $attachment['name'] }}"
                                         class="w-full h-32 object-cover mb-2">
                                @endif
                                <p class="text-white text-sm">{{ $attachment['name'] }}</p>
                                <a href="{{ asset('storage/' . $attachment['path']) }}" 
                                   download="{{ $attachment['name'] }}"
                                   class="text-secondary text-xs hover:underline">Download</a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Post Actions -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-600">
                <div class="flex items-center space-x-6">
                    <!-- Like Button -->
                    @auth
                        <form method="POST" action="{{ route('communities.posts.like', [$community, $post]) }}" class="inline">
                            @csrf
                            <button type="submit" class="flex items-center space-x-2 text-gray-400 hover:text-white transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                                <span>{{ $post->likes_count ?? 0 }} Likes</span>
                            </button>
                        </form>
                    @else
                        <div class="flex items-center space-x-2 text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                            <span>{{ $post->likes_count ?? 0 }} Likes</span>
                        </div>
                    @endauth

                    <!-- Comments Count -->
                    <div class="flex items-center space-x-2 text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        <span>{{ $post->comments_count ?? 0 }} Comments</span>
                    </div>

                    <!-- Views -->
                    <div class="flex items-center space-x-2 text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        <span>{{ $post->view_count ?? 0 }} Views</span>
                    </div>
                </div>

                <!-- Edit/Delete Actions for Post Owner -->
                @auth
                    @if(auth()->user()->id === $post->user_id)
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('communities.posts.edit', [$community, $post]) }}" 
                               class="text-secondary hover:underline text-sm">Edit</a>
                            <form method="POST" action="{{ route('communities.posts.destroy', [$community, $post]) }}" 
                                  class="inline" 
                                  onsubmit="return confirm('Are you sure you want to delete this post?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-400 hover:underline text-sm">Delete</button>
                            </form>
                        </div>
                    @endif
                @endauth
            </div>
        </div>

        <!-- Comments Section -->
        <div class="bg-secondary p-6">
            <h2 class="text-xl font-bold text-white mb-6">Comments</h2>
            
            <!-- Add Comment Form -->
            @auth
                <form method="POST" action="#" class="mb-6">
                    @csrf
                    <div class="mb-4">
                        <textarea name="content" 
                                  rows="3" 
                                  placeholder="Add your comment..."
                                  class="w-full px-3 py-2 bg-primary text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-secondary"></textarea>
                    </div>
                    <button type="submit" class="btn-primary px-4 py-2">
                        Post Comment
                    </button>
                </form>
            @else
                <div class="text-center py-6 text-gray-400">
                    <p><a href="{{ route('login') }}" class="text-secondary hover:underline">Login</a> to post comments</p>
                </div>
            @endauth

            <!-- Comments List -->
            <div class="space-y-4">
                <!-- Example comment - this would be populated from database -->
                <div class="bg-primary p-4">
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 bg-secondary rounded-full flex items-center justify-center">
                            <span class="text-white text-sm font-bold">U</span>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-1">
                                <span class="text-white font-semibold text-sm">User Name</span>
                                <span class="text-gray-400 text-xs">2 hours ago</span>
                            </div>
                            <p class="text-gray-300 text-sm">This is a sample comment. Comments functionality would be implemented here.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Posts -->
        <div class="mt-8">
            <h2 class="text-xl font-bold text-white mb-6">More from {{ $community->name }}</h2>
            <div class="text-gray-400 text-center py-8">
                <p>Related posts would appear here</p>
            </div>
        </div>
    </div>
</div>
@endsection
