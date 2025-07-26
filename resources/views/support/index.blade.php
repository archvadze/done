@extends('layouts.app')

@section('title', __('Support Center'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Hero Section -->
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">{{ __('How can we help you?') }}</h1>
        <p class="text-xl text-gray-600 dark:text-gray-400 mb-8">{{ __('Search our knowledge base or get in touch with support') }}</p>
        
        <!-- Search Bar -->
        <div class="max-w-2xl mx-auto">
            <form action="{{ route('support.search') }}" method="GET" class="relative">
                <input type="text" 
                       name="q" 
                       placeholder="{{ __('Search for help...') }}"
                       class="w-full px-6 py-4 text-lg border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                <button type="submit" class="absolute right-3 top-1/2 transform -translate-y-1/2 bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg transition-colors">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        <a href="{{ route('support.faq.index') }}" class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow text-center">
            <div class="text-blue-500 text-4xl mb-4">
                <i class="fas fa-question-circle"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">{{ __('FAQ') }}</h3>
            <p class="text-gray-600 dark:text-gray-400">{{ __('Find answers to commonly asked questions') }}</p>
        </a>

        <a href="{{ route('support.help.index') }}" class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow text-center">
            <div class="text-green-500 text-4xl mb-4">
                <i class="fas fa-book"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">{{ __('Help Articles') }}</h3>
            <p class="text-gray-600 dark:text-gray-400">{{ __('Browse detailed guides and tutorials') }}</p>
        </a>

        @auth
            <a href="{{ route('support.tickets.create') }}" class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow text-center">
                <div class="text-purple-500 text-4xl mb-4">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">{{ __('Create Ticket') }}</h3>
                <p class="text-gray-600 dark:text-gray-400">{{ __('Get personalized help from our support team') }}</p>
            </a>
        @else
            <a href="{{ route('support.contact') }}" class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow text-center">
                <div class="text-purple-500 text-4xl mb-4">
                    <i class="fas fa-envelope"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">{{ __('Contact Us') }}</h3>
                <p class="text-gray-600 dark:text-gray-400">{{ __('Send us a message and we\'ll get back to you') }}</p>
            </a>
        @endauth
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Popular FAQs -->
        <div class="lg:col-span-2">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">{{ __('Popular Questions') }}</h2>
            
            @if($popularFaqs->count() > 0)
                <div class="space-y-4">
                    @foreach($popularFaqs as $faq)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                                        <a href="{{ route('support.faq.show', $faq) }}" class="hover:text-blue-500">
                                            {{ $faq->question }}
                                        </a>
                                    </h3>
                                    <p class="text-gray-600 dark:text-gray-400 mb-3">
                                        {{ Str::limit(strip_tags($faq->answer), 150) }}
                                    </p>
                                    <div class="flex items-center gap-4 text-sm text-gray-500">
                                        <span class="bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 px-2 py-1 rounded">
                                            {{ $faq->category->name }}
                                        </span>
                                        <span>
                                            <i class="fas fa-eye mr-1"></i>
                                            {{ number_format($faq->view_count) }} {{ __('views') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="mt-6 text-center">
                    <a href="{{ route('support.faq.index') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                        {{ __('View All FAQs') }}
                    </a>
                </div>
            @else
                <div class="text-center py-8">
                    <p class="text-gray-600 dark:text-gray-400">{{ __('No FAQs available yet.') }}</p>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-8">
            <!-- Recent Help Articles -->
            @if($recentArticles->count() > 0)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('Recent Articles') }}</h3>
                    <div class="space-y-4">
                        @foreach($recentArticles as $article)
                            <div>
                                <h4 class="font-medium text-gray-900 dark:text-white mb-1">
                                    <a href="{{ route('support.help.show', $article) }}" class="hover:text-blue-500">
                                        {{ $article->title }}
                                    </a>
                                </h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $article->reading_time }} {{ __('min read') }} • {{ $article->published_at->diffForHumans() }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                    <a href="{{ route('support.help.index') }}" class="block mt-4 text-blue-500 hover:text-blue-600 text-sm">
                        {{ __('View all articles') }} →
                    </a>
                </div>
            @endif

            <!-- FAQ Categories -->
            @if($faqCategories->count() > 0)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('FAQ Categories') }}</h3>
                    <div class="space-y-3">
                        @foreach($faqCategories as $category)
                            <a href="{{ route('support.faq.category', $category) }}" 
                               class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <div class="flex items-center gap-3">
                                    @if($category->icon)
                                        <i class="{{ $category->icon }} text-gray-500"></i>
                                    @endif
                                    <span class="text-gray-900 dark:text-white">{{ $category->name }}</span>
                                </div>
                                <span class="text-sm text-gray-500">{{ $category->active_faqs_count }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Recent Tickets (for authenticated users) -->
            @auth
                @if($recentTickets->count() > 0)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('Your Recent Tickets') }}</h3>
                        <div class="space-y-3">
                            @foreach($recentTickets as $ticket)
                                <div class="border-l-4 {{ $ticket->isOpen() ? 'border-blue-500' : 'border-green-500' }} pl-3">
                                    <h4 class="font-medium text-gray-900 dark:text-white mb-1">
                                        <a href="{{ route('support.tickets.show', $ticket) }}" class="hover:text-blue-500">
                                            {{ $ticket->subject }}
                                        </a>
                                    </h4>
                                    <div class="flex items-center gap-2 text-sm">
                                        <span class="px-2 py-1 rounded text-xs {{ $ticket->getStatusBadgeColor() }}">
                                            {{ ucfirst($ticket->status) }}
                                        </span>
                                        <span class="text-gray-500">{{ $ticket->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <a href="{{ route('support.tickets.index') }}" class="block mt-4 text-blue-500 hover:text-blue-600 text-sm">
                            {{ __('View all tickets') }} →
                        </a>
                    </div>
                @endif
            @endauth
        </div>
    </div>
</div>
@endsection
