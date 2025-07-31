@extends('layouts.app')

@section('title', __('Help Articles'))

@section('content')
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">{{ __('Help Articles') }}</h1>
                    <p class="text-gray-600 dark:text-gray-400">{{ __('Browse our comprehensive guides and tutorials') }}</p>
                </div>
                <a href="{{ route('support.index') }}" class="text-blue-500 hover:text-blue-600 font-medium">
                    <i class="fas fa-arrow-left mr-2"></i>{{ __('Back to Support') }}
                </a>
            </div>

            <!-- Search -->
            <div class="max-w-md">
                <form action="{{ route('support.help.index') }}" method="GET" class="relative">
                    <input type="text" name="search" value="{{ $search }}"
                        placeholder="{{ __('Search help articles...') }}"
                        class="w-full px-4 py-2 pl-10 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                        <i class="fas fa-search"></i>
                    </div>
                    @if ($search)
                        <a href="{{ route('support.help.index') }}"
                            class="absolute right-10 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                    <button type="submit"
                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-blue-500 hover:text-blue-600">
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                @if ($search)
                    <div class="mb-6">
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ trans_choice('Showing :count result(s) for ":search"', $articles->total(), ['count' => $articles->total(), 'search' => $search]) }}
                        </p>
                    </div>
                @endif

                @if ($articles->count() > 0)
                    <div class="space-y-6">
                        @foreach ($articles as $article)
                            <article
                                class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex-1">
                                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                                            <a href="{{ route('support.help.show', $article) }}"
                                                class="hover:text-blue-600 dark:hover:text-blue-400">
                                                {{ $article->title }}
                                            </a>
                                        </h2>
                                        <p class="text-gray-600 dark:text-gray-400 mb-3">{{ $article->excerpt }}</p>
                                    </div>
                                    <div class="ml-4 text-sm text-gray-500 dark:text-gray-400">
                                        <div class="flex items-center mb-1">
                                            <i class="fas fa-eye mr-1"></i>
                                            {{ $article->view_count }}
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-thumbs-up mr-1"></i>
                                            {{ $article->helpful_count }}
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between">
                                    <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                        <img src="{{ $article->author->avatar ?? asset('images/default-avatar.png') }}"
                                            alt="{{ $article->author->name }}" class="w-6 h-6 rounded-full mr-2">
                                        <span>{{ $article->author->name }}</span>
                                        <span class="mx-2">•</span>
                                        <span>{{ $article->published_at->format('M j, Y') }}</span>
                                    </div>

                                    @if ($article->tags)
                                        <div class="flex flex-wrap gap-1">
                                            @foreach (array_slice($article->tags, 0, 3) as $tag)
                                                <span
                                                    class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 text-xs rounded">
                                                    {{ $tag }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-8">
                        {{ $articles->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="text-gray-400 text-6xl mb-4">
                            <i class="fas fa-book-open"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                            @if ($search)
                                {{ __('No articles found') }}
                            @else
                                {{ __('No help articles available') }}
                            @endif
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">
                            @if ($search)
                                {{ __('Try adjusting your search terms or browse all articles.') }}
                            @else
                                {{ __('Help articles will be available soon.') }}
                            @endif
                        </p>
                        @if ($search)
                            <a href="{{ route('support.help.index') }}"
                                class="text-blue-500 hover:text-blue-600 font-medium">
                                {{ __('View all articles') }}
                            </a>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <div class="space-y-6">
                    <!-- Popular Articles -->
                    @if ($popularArticles->count() > 0)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                {{ __('Popular Articles') }}
                            </h3>
                            <div class="space-y-3">
                                @foreach ($popularArticles as $popular)
                                    <a href="{{ route('support.help.show', $popular) }}" class="block group">
                                        <h4
                                            class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 mb-1">
                                            {{ $popular->title }}
                                        </h4>
                                        <div class="flex items-center text-xs text-gray-500 dark:text-gray-400">
                                            <i class="fas fa-eye mr-1"></i>
                                            {{ $popular->view_count }}
                                            <span class="mx-2">•</span>
                                            <i class="fas fa-thumbs-up mr-1"></i>
                                            {{ $popular->helpful_count }}
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Quick Links -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            {{ __('Quick Links') }}
                        </h3>
                        <div class="space-y-2">
                            <a href="{{ route('support.faq.index') }}"
                                class="block text-blue-500 hover:text-blue-600 text-sm">
                                <i class="fas fa-question-circle mr-2"></i>{{ __('FAQ') }}
                            </a>
                            <a href="{{ route('support.contact') }}"
                                class="block text-blue-500 hover:text-blue-600 text-sm">
                                <i class="fas fa-envelope mr-2"></i>{{ __('Contact Support') }}
                            </a>
                            @auth
                                <a href="{{ route('support.tickets.create') }}"
                                    class="block text-blue-500 hover:text-blue-600 text-sm">
                                    <i class="fas fa-ticket-alt mr-2"></i>{{ __('Create Ticket') }}
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
