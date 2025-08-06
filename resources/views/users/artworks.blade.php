@extends('layouts.app')

@section('title', $user->name . "'s Artworks - Acumen Craft")
@section('description', 'Browse the complete artwork collection by ' . $user->name . '. Discover their creative portfolio and artistic journey on Acumen Craft.')
@section('keywords', 'artworks, ' . $user->name . ', artist portfolio, digital art collection, creative works')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- User Header -->
    <header class="bg-white overflow-hidden shadow rounded-lg mb-6">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    @if ($user->avatar_url)
                        <img class="h-16 w-16 rounded-full" src="{{ $user->avatar_url }}" alt="{{ $user->name }}'s profile picture">
                    @else
                        <div class="h-16 w-16 rounded-full bg-gray-300 flex items-center justify-center">
                            <span class="text-xl font-medium text-gray-700">{{ substr($user->name, 0, 1) }}</span>
                        </div>
                    @endif
                </div>
                <div class="ml-5 flex-1">
                        <div class="flex items-center justify-between">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}'s Artworks</h1>
                                @if ($user->bio)
                                    <p class="text-gray-600 mt-1">{{ $user->bio }}</p>
                                @endif
                            </div>
                            <div class="text-right text-sm text-gray-500">
                                <div>{{ $stats['artworks_count'] }}
                                    {{ Str::plural('artwork', $stats['artworks_count']) }}</div>
                                @if ($stats['avg_acq_score'])
                                    <div>Avg ACQ: {{ number_format($stats['avg_acq_score'], 1) }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <form method="GET" class="flex flex-wrap items-center gap-4">
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                        <select name="category" id="category"
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                            <option value="">All Categories</option>
                            <option value="digital-art" {{ request('category') === 'digital-art' ? 'selected' : '' }}>
                                Digital Art</option>
                            <option value="painting" {{ request('category') === 'painting' ? 'selected' : '' }}>
                                Painting</option>
                            <option value="photography" {{ request('category') === 'photography' ? 'selected' : '' }}>
                                Photography</option>
                            <option value="sculpture" {{ request('category') === 'sculpture' ? 'selected' : '' }}>
                                Sculpture</option>
                            <option value="music" {{ request('category') === 'music' ? 'selected' : '' }}>Music
                            </option>
                            <option value="video" {{ request('category') === 'video' ? 'selected' : '' }}>Video
                            </option>
                            <option value="mixed-media" {{ request('category') === 'mixed-media' ? 'selected' : '' }}>
                                Mixed Media</option>
                        </select>
                    </div>
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                            placeholder="Search artworks..."
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    <div class="flex items-end">
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                            Filter
                        </button>
                        @if (request()->hasAny(['category', 'search']))
                            <a href="{{ route('users.artworks', $user) }}"
                                class="ml-2 text-gray-600 hover:text-gray-900 text-sm">Clear</a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Artworks Grid -->
        @if ($artworks->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach ($artworks as $artwork)
                    <div class="bg-white rounded-lg shadow-sm border overflow-hidden hover:shadow-md transition-shadow">
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
                            <div class="flex items-start justify-between">
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-lg font-medium text-gray-900 truncate">
                                        {{ $artwork->getTitle() ?? 'Untitled' }}
                                    </h3>
                                    <p class="text-sm text-gray-500 mt-1">
                                        {{ ucfirst($artwork->category ?? 'uncategorized') }}</p>
                                </div>
                                <div class="flex items-center space-x-1 text-sm text-gray-500">
                                    @if ($artwork->status === 'published')
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Published
                                        </span>
                                    @elseif($artwork->status === 'draft')
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Draft
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center justify-between mt-4 text-sm text-gray-500">
                                <div class="flex items-center space-x-4">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        {{ $artwork->view_count }}
                                    </span>
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                        </svg>
                                        {{ $artwork->like_count }}
                                    </span>
                                </div>
                                @if ($artwork->acq_score)
                                    <span class="font-medium text-blue-600">ACQ:
                                        {{ number_format($artwork->acq_score, 1) }}</span>
                                @endif
                            </div>

                            <div class="mt-4 flex space-x-2">
                                <a href="/artworks/{{ $artwork->id }}"
                                    class="flex-1 text-center bg-blue-600 text-white py-2 px-3 rounded text-sm hover:bg-blue-700 transition-colors">
                                    View
                                </a>
                                @auth
                                    @if (Auth::id() === $user->id)
                                        <a href="/artworks/{{ $artwork->id }}/edit"
                                            class="text-center bg-gray-200 text-gray-700 py-2 px-3 rounded text-sm hover:bg-gray-300 transition-colors">
                                            Edit
                                        </a>
                                    @endif
                                @endauth
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
            <div class="bg-white rounded-lg shadow-sm border">
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No artworks found</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        @if (request()->hasAny(['category', 'search']))
                            Try adjusting your filters or search terms.
                        @else
                            {{ $user->name }} hasn't uploaded any artworks yet.
                        @endif
                    </p>
                    @auth
                        @if (Auth::id() === $user->id)
                            <div class="mt-6">
                                <a href="/artworks/create"
                                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                    Upload Your First Artwork
                                </a>
                            </div>
                        @endif
                    @endauth
                </div>
            </div>
        @endif
    </div>
@endsection
