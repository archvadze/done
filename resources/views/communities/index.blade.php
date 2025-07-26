@extends('layouts.app')

@section('title', __('Communities'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ __('Communities') }}</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">{{ __('Discover and join creative communities') }}</p>
        </div>
        @auth
            <a href="{{ route('communities.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                {{ __('Create Community') }}
            </a>
        @endauth
    </div>

    <!-- Search and Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-8">
        <form method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="{{ __('Search communities...') }}"
                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
            </div>
            <div>
                <select name="sort" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    <option value="popular" {{ request('sort') === 'popular' ? 'selected' : '' }}>{{ __('Most Popular') }}</option>
                    <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>{{ __('Newest') }}</option>
                    <option value="alphabetical" {{ request('sort') === 'alphabetical' ? 'selected' : '' }}>{{ __('A-Z') }}</option>
                </select>
            </div>
            <button type="submit" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition-colors">
                {{ __('Filter') }}
            </button>
        </form>
    </div>

    <!-- Communities Grid -->
    @if($communities->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @foreach($communities as $community)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-shadow">
                    <!-- Cover Image -->
                    @if($community->cover_image)
                        <div class="h-32 bg-gradient-to-r from-blue-400 to-purple-500 rounded-t-lg relative overflow-hidden">
                            <img src="{{ asset('storage/' . $community->cover_image) }}" 
                                 alt="{{ $community->name }}" 
                                 class="w-full h-full object-cover">
                        </div>
                    @else
                        <div class="h-32 bg-gradient-to-r from-blue-400 to-purple-500 rounded-t-lg"></div>
                    @endif

                    <div class="p-6">
                        <!-- Avatar and Title -->
                        <div class="flex items-start gap-4 mb-4">
                            @if($community->avatar)
                                <img src="{{ asset('storage/' . $community->avatar) }}" 
                                     alt="{{ $community->name }}" 
                                     class="w-12 h-12 rounded-full object-cover">
                            @else
                                <div class="w-12 h-12 bg-gray-300 dark:bg-gray-600 rounded-full flex items-center justify-center">
                                    <span class="text-gray-600 dark:text-gray-400 font-bold">
                                        {{ strtoupper(substr($community->name, 0, 1)) }}
                                    </span>
                                </div>
                            @endif
                            
                            <div class="flex-1">
                                <h3 class="font-bold text-lg text-gray-900 dark:text-white">
                                    <a href="{{ route('communities.show', $community->slug) }}" class="hover:text-blue-500">
                                        {{ $community->name }}
                                    </a>
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ __('by') }} {{ $community->creator->name }}
                                </p>
                            </div>
                        </div>

                        <!-- Description -->
                        <p class="text-gray-700 dark:text-gray-300 text-sm mb-4 line-clamp-3">
                            {{ $community->description }}
                        </p>

                        <!-- Stats -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400">
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
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        {{ $communities->links() }}
    @else
        <div class="text-center py-12">
            <div class="text-gray-500 dark:text-gray-400 mb-4">
                <i class="fas fa-users text-6xl"></i>
            </div>
            <h3 class="text-xl font-medium text-gray-900 dark:text-white mb-2">{{ __('No communities found') }}</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-6">
                @if(request('search'))
                    {{ __('No communities match your search criteria.') }}
                @else
                    {{ __('Be the first to create a community!') }}
                @endif
            </p>
            @auth
                <a href="{{ route('communities.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                    {{ __('Create Community') }}
                </a>
            @endauth
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
.line-clamp-3 {
    overflow: hidden;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 3;
}
</style>
@endpush
