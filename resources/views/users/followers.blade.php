@extends('layouts.app')

@section('title', $user->name . "'s Followers - Acumen Craft")
@section('description', 'View the followers of ' . $user->name . ' on Acumen Craft. See who follows this artist and discover new connections in the creative community.')
@section('keywords', 'followers, ' . $user->name . ', artist network, community, social connections')

@section('content')
<div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Header -->
    <header class="mb-6">
        <nav class="flex items-center space-x-4 mb-4" aria-label="Breadcrumb">
            <a href="{{ route('users.show', $user) }}" class="text-blue-600 hover:text-blue-800">
                ← Back to {{ $user->name }}'s Profile
            </a>
        </nav>
        <h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}'s Followers</h1>
        <p class="text-gray-600">{{ $followers->total() }} {{ Str::plural('follower', $followers->total()) }}</p>
    </header>

    @if ($followers->count() > 0)
        <div class="bg-white shadow rounded-lg">
            <div class="divide-y divide-gray-200">
                @foreach ($followers as $follower)
                    <div class="p-6 flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <!-- Avatar -->
                                <div class="flex-shrink-0">
                                    @if ($follower->avatar_url)
                                        <img class="h-12 w-12 rounded-full" src="{{ $follower->avatar_url }}"
                                            alt="{{ $follower->name }}">
                                    @else
                                        <div
                                            class="h-12 w-12 rounded-full bg-gray-300 flex items-center justify-center">
                                            <span
                                                class="text-lg font-medium text-gray-700">{{ substr($follower->name, 0, 1) }}</span>
                                        </div>
                                    @endif
                                </div>

                                <!-- User Info -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('users.show', $follower) }}"
                                            class="text-lg font-medium text-gray-900 hover:text-blue-600">
                                            {{ $follower->name }}
                                        </a>
                                    </div>
                                    @if ($follower->bio)
                                        <p class="text-sm text-gray-600 mt-1 truncate">{{ $follower->bio }}</p>
                                    @endif
                                    <div class="flex items-center space-x-4 mt-2 text-sm text-gray-500">
                                        <span>{{ $follower->artworks()->where('status', 'published')->count() }}
                                            artworks</span>
                                        <span>{{ $follower->followers_count }} followers</span>
                                        <span>Following since {{ $follower->pivot->created_at->format('M Y') }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex-shrink-0">
                                @auth
                                    @if (auth()->id() !== $follower->id)
                                        @if (auth()->user()->isFollowing($follower))
                                            <button
                                                class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md text-sm hover:bg-gray-300 transition-colors">
                                                Following
                                            </button>
                                        @else
                                            <button
                                                class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700 transition-colors">
                                                Follow
                                            </button>
                                        @endif
                                    @endif
                                @else
                                    <a href="/login"
                                        class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700 transition-colors">
                                        Follow
                                    </a>
                                @endauth
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $followers->links() }}
            </div>
        @else
            <div class="bg-white rounded-lg shadow-sm border">
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No followers yet</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ $user->name }} hasn't gained any followers yet.</p>
                </div>
            </div>
        @endif
    </div>
@endsection
