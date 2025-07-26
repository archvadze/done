@extends('layouts.app')

@section('title', $community->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Community Header -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm mb-8 overflow-hidden">
        <!-- Cover Image -->
        @if($community->cover_image)
            <div class="h-48 bg-gradient-to-r from-blue-400 to-purple-500 relative overflow-hidden">
                <img src="{{ asset('storage/' . $community->cover_image) }}" 
                     alt="{{ $community->name }}" 
                     class="w-full h-full object-cover">
            </div>
        @else
            <div class="h-48 bg-gradient-to-r from-blue-400 to-purple-500"></div>
        @endif

        <div class="p-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-start gap-4">
                    <!-- Avatar -->
                    @if($community->avatar)
                        <img src="{{ asset('storage/' . $community->avatar) }}" 
                             alt="{{ $community->name }}" 
                             class="w-20 h-20 rounded-full object-cover -mt-10 border-4 border-white dark:border-gray-800">
                    @else
                        <div class="w-20 h-20 bg-gray-300 dark:bg-gray-600 rounded-full flex items-center justify-center -mt-10 border-4 border-white dark:border-gray-800">
                            <span class="text-gray-600 dark:text-gray-400 font-bold text-2xl">
                                {{ strtoupper(substr($community->name, 0, 1)) }}
                            </span>
                        </div>
                    @endif
                    
                    <div class="flex-1">
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $community->name }}</h1>
                        <p class="text-gray-600 dark:text-gray-400 mt-1">
                            {{ __('Created by') }} {{ $community->creator->name }}
                        </p>
                        <div class="flex items-center gap-4 mt-2 text-sm text-gray-600 dark:text-gray-400">
                            <span>
                                <i class="fas fa-users mr-1"></i>
                                {{ number_format($community->member_count) }} {{ __('members') }}
                            </span>
                            <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded text-xs">
                                {{ ucfirst($community->privacy) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-3">
                    @auth
                        @if($community->isMember(auth()->user()))
                            <form method="POST" action="{{ route('communities.leave', $community->slug) }}">
                                @csrf
                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-colors">
                                    {{ __('Leave') }}
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('communities.join', $community->slug) }}">
                                @csrf
                                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
                                    {{ __('Join') }}
                                </button>
                            </form>
                        @endif

                        @if($community->canModerate(auth()->user()))
                            <a href="{{ route('communities.edit', $community->slug) }}" 
                               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                                {{ __('Edit') }}
                            </a>
                        @endif
                    @endauth
                </div>
            </div>

            <!-- Description -->
            <div class="mt-6">
                <p class="text-gray-700 dark:text-gray-300">{{ $community->description }}</p>
            </div>

            <!-- Rules -->
            @if($community->rules && count($community->rules) > 0)
                <div class="mt-6">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-3">{{ __('Community Rules') }}</h3>
                    <ul class="list-decimal list-inside space-y-1 text-gray-700 dark:text-gray-300">
                        @foreach($community->rules as $rule)
                            <li>{{ $rule }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-3">
            <!-- Post Creation -->
            @auth
                @if($community->isMember(auth()->user()))
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
                        <a href="{{ route('communities.posts.create', $community->slug) }}" 
                           class="block w-full text-left p-4 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg hover:border-blue-500 transition-colors">
                            <span class="text-gray-600 dark:text-gray-400">{{ __('What\'s on your mind?') }}</span>
                        </a>
                    </div>
                @endif
            @endauth

            <!-- Post Filters -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 mb-6">
                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('communities.show', $community->slug) }}" 
                       class="px-4 py-2 rounded-lg {{ !request('type') || request('type') === 'all' ? 'bg-blue-500 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                        {{ __('All Posts') }}
                    </a>
                    <a href="{{ route('communities.show', $community->slug) }}?type=discussion" 
                       class="px-4 py-2 rounded-lg {{ request('type') === 'discussion' ? 'bg-blue-500 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                        {{ __('Discussions') }}
                    </a>
                    <a href="{{ route('communities.show', $community->slug) }}?type=showcase" 
                       class="px-4 py-2 rounded-lg {{ request('type') === 'showcase' ? 'bg-blue-500 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                        {{ __('Showcase') }}
                    </a>
                    <a href="{{ route('communities.show', $community->slug) }}?type=question" 
                       class="px-4 py-2 rounded-lg {{ request('type') === 'question' ? 'bg-blue-500 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                        {{ __('Questions') }}
                    </a>
                </div>
            </div>

            <!-- Pinned Posts -->
            @if($pinnedPosts->count() > 0)
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('Pinned Posts') }}</h3>
                    @foreach($pinnedPosts as $post)
                        @include('communities.partials.post-card', ['post' => $post, 'community' => $community])
                    @endforeach
                </div>
            @endif

            <!-- Posts -->
            @if($posts->count() > 0)
                <div class="space-y-6">
                    @foreach($posts as $post)
                        @include('communities.partials.post-card', ['post' => $post, 'community' => $community])
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $posts->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <div class="text-gray-500 dark:text-gray-400 mb-4">
                        <i class="fas fa-comments text-6xl"></i>
                    </div>
                    <h3 class="text-xl font-medium text-gray-900 dark:text-white mb-2">{{ __('No posts yet') }}</h3>
                    <p class="text-gray-600 dark:text-gray-400">{{ __('Be the first to start a conversation!') }}</p>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <!-- Recent Members -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-4">{{ __('Recent Members') }}</h3>
                <div class="space-y-3">
                    @foreach($community->activeMembers->take(6) as $member)
                        <div class="flex items-center gap-3">
                            @if($member->avatar_url)
                                <img src="{{ $member->avatar_url }}" alt="{{ $member->name }}" class="w-8 h-8 rounded-full object-cover">
                            @else
                                <div class="w-8 h-8 bg-gray-300 dark:bg-gray-600 rounded-full flex items-center justify-center">
                                    <span class="text-xs font-medium text-gray-600 dark:text-gray-400">
                                        {{ strtoupper(substr($member->name, 0, 1)) }}
                                    </span>
                                </div>
                            @endif
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $member->name }}</p>
                                <p class="text-xs text-gray-600 dark:text-gray-400">{{ $member->pivot->role }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
                <a href="{{ route('communities.members', $community->slug) }}" 
                   class="block mt-4 text-sm text-blue-500 hover:text-blue-600">
                    {{ __('View all members') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
