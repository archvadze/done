@extends('layouts.app')

@section('title', 'Help Articles')

@section('content')
<div class="min-h-screen bg-primary">
    <div class="max-w-6xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-secondary mb-4">Help Center</h1>
            <p class="text-white text-lg">Find detailed guides and tutorials to help you get the most out of Acumen Craft</p>
        </div>

        <!-- Search -->
        <div class="mb-8">
            <form method="GET" action="{{ route('support.help.index') }}" class="max-w-md mx-auto">
                <div class="relative">
                    <input type="text" 
                           name="search" 
                           value="{{ $search ?? '' }}"
                           placeholder="Search help articles..." 
                           class="w-full px-4 py-3 bg-secondary text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-secondary">
                    <button type="submit" class="absolute right-3 top-3 text-white hover:text-secondary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                </div>
            </form>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-3">
                @if($search)
                    <h2 class="text-2xl font-bold text-white mb-6">Search Results for "{{ $search }}"</h2>
                @else
                    <h2 class="text-2xl font-bold text-white mb-6">All Help Articles</h2>
                @endif

                @if($articles->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($articles as $article)
                            <div class="bg-secondary p-6 hover:bg-opacity-80 transition-colors">
                                <h3 class="text-xl font-semibold text-white mb-3">
                                    <a href="{{ route('support.help.show', $article) }}" class="hover:text-secondary">
                                        {{ $article->title }}
                                    </a>
                                </h3>
                                
                                <p class="text-gray-300 mb-4">
                                    {{ Str::limit(strip_tags($article->content), 150) }}
                                </p>
                                
                                <div class="flex items-center justify-between text-sm text-gray-400">
                                    <span>{{ $article->published_at->format('M j, Y') }}</span>
                                    <span>{{ $article->estimated_read_time ?? '5' }} min read</span>
                                </div>
                                
                                @if($article->tags)
                                    <div class="mt-3 flex flex-wrap gap-1">
                                        @foreach(collect($article->tags)->take(3) as $tag)
                                            <span class="px-2 py-1 text-xs bg-primary text-secondary">{{ $tag }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-8">
                        {{ $articles->links() }}
                    </div>
                @else
                    <div class="bg-secondary p-8 text-center">
                        @if($search)
                            <h3 class="text-xl font-semibold text-white mb-2">No articles found</h3>
                            <p class="text-gray-300">Try different search terms or browse all articles.</p>
                        @else
                            <h3 class="text-xl font-semibold text-white mb-2">No Help Articles Available</h3>
                            <p class="text-gray-300">Check back soon for helpful guides and tutorials.</p>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Quick Links -->
                <div class="bg-secondary p-6 mb-6">
                    <h3 class="text-lg font-semibold text-white mb-4">Quick Links</h3>
                    <div class="space-y-2">
                        <a href="{{ route('support.faq.index') }}" class="block text-secondary hover:underline">
                            Frequently Asked Questions
                        </a>
                        <a href="{{ route('support.contact') }}" class="block text-secondary hover:underline">
                            Contact Support
                        </a>
                        <a href="{{ route('support.tickets.create') }}" class="block text-secondary hover:underline">
                            Submit a Ticket
                        </a>
                    </div>
                </div>

                <!-- Popular Articles -->
                @if($popularArticles->count() > 0)
                    <div class="bg-secondary p-6">
                        <h3 class="text-lg font-semibold text-white mb-4">Popular Articles</h3>
                        <div class="space-y-3">
                            @foreach($popularArticles as $article)
                                <div>
                                    <a href="{{ route('support.help.show', $article) }}" 
                                       class="text-white hover:text-secondary text-sm block font-medium">
                                        {{ $article->title }}
                                    </a>
                                    <p class="text-gray-400 text-xs mt-1">
                                        {{ $article->view_count ?? 0 }} views
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
