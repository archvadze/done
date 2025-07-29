@extends('layouts.app')

@section('title', 'Frequently Asked Questions')

@section('content')
<div class="min-h-screen bg-primary">
    <div class="max-w-4xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-secondary mb-4">Frequently Asked Questions</h1>
            <p class="text-white text-lg">Find answers to common questions about Acumen Craft</p>
        </div>

        <!-- Search -->
        <div class="mb-8">
            <form method="GET" action="{{ route('support.faq.index') }}" class="max-w-md mx-auto">
                <div class="relative">
                    <input type="text" 
                           name="search" 
                           value="{{ $search ?? '' }}"
                           placeholder="Search FAQs..." 
                           class="w-full px-4 py-3 bg-secondary text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-secondary">
                    <button type="submit" class="absolute right-3 top-3 text-white hover:text-secondary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                </div>
            </form>
        </div>

        @if($search)
            <!-- Search Results -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-white mb-4">Search Results for "{{ $search }}"</h2>
                @if($faqs->count() > 0)
                    <div class="space-y-4">
                        @foreach($faqs as $faq)
                            <div class="bg-secondary p-6">
                                <h3 class="text-xl font-semibold text-white mb-2">
                                    <a href="{{ route('support.faq.show', $faq) }}" class="hover:text-secondary">
                                        {{ $faq->question }}
                                    </a>
                                </h3>
                                <div class="text-gray-300 mb-2">
                                    {{ Str::limit(strip_tags($faq->answer), 200) }}
                                </div>
                                <div class="text-sm text-gray-400">
                                    Category: {{ $faq->category->name }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                    {{ $faqs->links() }}
                @else
                    <div class="bg-secondary p-6 text-center">
                        <p class="text-white">No FAQs found matching your search.</p>
                    </div>
                @endif
            </div>
        @else
            <!-- FAQ Categories -->
            @if($categories->count() > 0)
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-white mb-6">Browse by Category</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($categories as $category)
                            <a href="{{ route('support.faq.category', $category) }}" 
                               class="bg-secondary p-6 hover:bg-opacity-80 transition-colors">
                                <h3 class="text-xl font-semibold text-white mb-2">{{ $category->name }}</h3>
                                <p class="text-gray-300 mb-3">{{ $category->description }}</p>
                                <div class="text-sm text-secondary">
                                    {{ $category->active_faqs_count }} question{{ $category->active_faqs_count !== 1 ? 's' : '' }}
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Popular FAQs -->
            @if($faqs->count() > 0)
                <div>
                    <h2 class="text-2xl font-bold text-white mb-6">Popular Questions</h2>
                    <div class="space-y-4">
                        @foreach($faqs as $faq)
                            <div class="bg-secondary p-6">
                                <h3 class="text-xl font-semibold text-white mb-3">
                                    <a href="{{ route('support.faq.show', $faq) }}" class="hover:text-secondary">
                                        {{ $faq->question }}
                                    </a>
                                </h3>
                                <div class="text-gray-300">
                                    {{ Str::limit(strip_tags($faq->answer), 300) }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endif

        @if($categories->count() === 0 && $faqs->count() === 0)
            <div class="bg-secondary p-8 text-center">
                <h3 class="text-xl font-semibold text-white mb-2">No FAQs Available</h3>
                <p class="text-gray-300">Check back soon for helpful answers to common questions.</p>
            </div>
        @endif
    </div>
</div>
@endsection
