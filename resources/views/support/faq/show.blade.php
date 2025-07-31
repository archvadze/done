@extends('layouts.app')

@section('title', $faq->question)

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <!-- Navigation -->
            <div class="mb-6">
                <nav class="flex text-sm text-gray-600 dark:text-gray-400">
                    <a href="{{ route('support.index') }}"
                        class="hover:text-blue-600 dark:hover:text-blue-400">{{ __('Support') }}</a>
                    <span class="mx-2">/</span>
                    <a href="{{ route('support.faq.index') }}"
                        class="hover:text-blue-600 dark:hover:text-blue-400">{{ __('FAQ') }}</a>
                    <span class="mx-2">/</span>
                    <a href="{{ route('support.faq.category', $faq->category) }}"
                        class="hover:text-blue-600 dark:hover:text-blue-400">{{ $faq->category->name }}</a>
                    <span class="mx-2">/</span>
                    <span class="text-gray-500 dark:text-gray-400">{{ Str::limit($faq->question, 30) }}</span>
                </nav>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <article class="lg:col-span-2">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-8">
                        <!-- FAQ Header -->
                        <header class="mb-8">
                            <div class="flex items-center mb-4">
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300 mr-3">
                                    {{ $faq->category->name }}
                                </span>
                                <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                    <i class="fas fa-eye mr-1"></i>
                                    <span class="mr-3">{{ number_format($faq->view_count) }} {{ __('views') }}</span>
                                    <i class="fas fa-thumbs-up mr-1"></i>
                                    <span>{{ number_format($faq->helpful_count) }} {{ __('helpful') }}</span>
                                </div>
                            </div>

                            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">
                                {{ $faq->question }}
                            </h1>

                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ __('Last updated') }}: {{ $faq->updated_at->format('F j, Y') }}
                            </div>
                        </header>

                        <!-- FAQ Answer -->
                        <div class="prose prose-lg dark:prose-invert max-w-none mb-8">
                            {!! $faq->answer !!}
                        </div>

                        <!-- Tags -->
                        @if ($faq->tags && count($faq->tags) > 0)
                            <div class="mb-8">
                                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">{{ __('Tags') }}
                                </h3>
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($faq->tags as $tag)
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                            <i class="fas fa-tag mr-1 text-xs"></i>{{ $tag }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Helpful Section -->
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                                        {{ __('Was this helpful?') }}</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ __('Let us know if this FAQ answered your question.') }}</p>
                                </div>

                                <div class="flex items-center space-x-3">
                                    <form action="{{ route('support.faq.helpful', $faq) }}" method="POST"
                                        class="inline-block">
                                        @csrf
                                        <button type="submit"
                                            class="flex items-center px-4 py-2 bg-green-100 text-green-700 hover:bg-green-200 dark:bg-green-900 dark:text-green-300 dark:hover:bg-green-800 rounded-lg transition-colors">
                                            <i class="fas fa-thumbs-up mr-2"></i>
                                            {{ __('Yes') }} ({{ $faq->helpful_count }})
                                        </button>
                                    </form>

                                    <form action="{{ route('support.faq.not-helpful', $faq) }}" method="POST"
                                        class="inline-block">
                                        @csrf
                                        <button type="submit"
                                            class="flex items-center px-4 py-2 bg-red-100 text-red-700 hover:bg-red-200 dark:bg-red-900 dark:text-red-300 dark:hover:bg-red-800 rounded-lg transition-colors">
                                            <i class="fas fa-thumbs-down mr-2"></i>
                                            {{ __('No') }} ({{ $faq->not_helpful_count }})
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>

                <!-- Sidebar -->
                <aside class="lg:col-span-1">
                    <div class="space-y-6">
                        <!-- Related FAQs -->
                        @if ($relatedFaqs && $relatedFaqs->count() > 0)
                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                    {{ __('Related Questions') }}
                                </h3>
                                <div class="space-y-3">
                                    @foreach ($relatedFaqs as $related)
                                        <a href="{{ route('support.faq.show', $related) }}" class="block group">
                                            <h4
                                                class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 mb-1">
                                                {{ $related->question }}
                                            </h4>
                                            <div class="flex items-center text-xs text-gray-500 dark:text-gray-400">
                                                <i class="fas fa-eye mr-1"></i>
                                                {{ number_format($related->view_count) }}
                                                <span class="mx-2">•</span>
                                                <i class="fas fa-thumbs-up mr-1"></i>
                                                {{ number_format($related->helpful_count) }}
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Category FAQs -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                {{ __('More in :category', ['category' => $faq->category->name]) }}
                            </h3>
                            <div class="space-y-2">
                                <a href="{{ route('support.faq.category', $faq->category) }}"
                                    class="block text-blue-500 hover:text-blue-600 dark:text-blue-400 dark:hover:text-blue-300 text-sm">
                                    <i class="fas fa-arrow-right mr-2"></i>{{ __('View all questions in this category') }}
                                </a>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                {{ __('Need More Help?') }}
                            </h3>
                            <div class="space-y-3">
                                <a href="{{ route('support.help.index') }}"
                                    class="block text-blue-500 hover:text-blue-600 dark:text-blue-400 dark:hover:text-blue-300 text-sm">
                                    <i class="fas fa-book mr-2"></i>{{ __('Browse Help Articles') }}
                                </a>
                                <a href="{{ route('support.contact') }}"
                                    class="block text-blue-500 hover:text-blue-600 dark:text-blue-400 dark:hover:text-blue-300 text-sm">
                                    <i class="fas fa-envelope mr-2"></i>{{ __('Contact Support') }}
                                </a>
                                @auth
                                    <a href="{{ route('support.tickets.create') }}"
                                        class="block text-blue-500 hover:text-blue-600 dark:text-blue-400 dark:hover:text-blue-300 text-sm">
                                        <i class="fas fa-ticket-alt mr-2"></i>{{ __('Create Support Ticket') }}
                                    </a>
                                @endauth
                            </div>
                        </div>

                        <!-- Search -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                {{ __('Search FAQs') }}
                            </h3>
                            <form action="{{ route('support.faq.index') }}" method="GET" class="relative">
                                <input type="text" name="search" placeholder="{{ __('Search questions...') }}"
                                    class="w-full px-4 py-2 pr-10 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm">
                                <button type="submit"
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                    <i class="fas fa-search"></i>
                                </button>
                            </form>
                        </div>

                        <!-- Still Have Questions -->
                        <div class="bg-blue-50 dark:bg-blue-900 rounded-lg p-6 text-center">
                            <div class="text-blue-500 dark:text-blue-400 text-3xl mb-3">
                                <i class="fas fa-question-circle"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-2">
                                {{ __('Still have questions?') }}
                            </h3>
                            <p class="text-blue-700 dark:text-blue-300 text-sm mb-4">
                                {{ __('Our support team is here to help you get the answers you need.') }}
                            </p>
                            <a href="{{ route('support.contact') }}"
                                class="inline-block bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                {{ __('Get in Touch') }}
                            </a>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add smooth scrolling for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth'
                        });
                    }
                });
            });

            // Show success message after voting
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('voted') === '1') {
                const message = document.createElement('div');
                message.className =
                    'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
                message.textContent = '{{ __('Thank you for your feedback!') }}';
                document.body.appendChild(message);

                setTimeout(() => {
                    message.remove();
                }, 3000);

                // Clean up URL
                const newUrl = window.location.pathname;
                window.history.replaceState({}, document.title, newUrl);
            }
        });
    </script>
@endpush
