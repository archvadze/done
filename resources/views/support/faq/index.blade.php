@extends('layouts.app')

@section('title', __('Frequently Asked Questions'))

@section('content')
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">{{ __('Frequently Asked Questions') }}
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400">{{ __('Find quick answers to common questions') }}</p>
                </div>
                <a href="{{ route('support.index') }}" class="text-blue-500 hover:text-blue-600 font-medium">
                    <i class="fas fa-arrow-left mr-2"></i>{{ __('Back to Support') }}
                </a>
            </div>

            <!-- Search -->
            <div class="max-w-md">
                <form action="{{ route('support.faq.index') }}" method="GET" class="relative">
                    <input type="text" name="search" value="{{ $search }}"
                        placeholder="{{ __('Search FAQs...') }}"
                        class="w-full px-4 py-2 pl-10 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                        <i class="fas fa-search"></i>
                    </div>
                    @if ($search)
                        <a href="{{ route('support.faq.index') }}"
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

        @if ($search)
            <!-- Search Results -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                    {{ trans_choice('Search Results (:count)', $faqs->count(), ['count' => $faqs->count()]) }}
                </h2>
                @if ($faqs->count() > 0)
                    <div class="space-y-4">
                        @foreach ($faqs as $faq)
                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                                            <a href="{{ route('support.faq.show', $faq) }}"
                                                class="hover:text-blue-600 dark:hover:text-blue-400">
                                                {{ $faq->question }}
                                            </a>
                                        </h3>
                                        <p class="text-gray-600 dark:text-gray-400 mb-3">
                                            {{ Str::limit(strip_tags($faq->answer), 200) }}
                                        </p>
                                        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                            <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded text-xs mr-3">
                                                {{ $faq->category->name }}
                                            </span>
                                            <i class="fas fa-eye mr-1"></i>
                                            <span class="mr-3">{{ $faq->view_count }}</span>
                                            <i class="fas fa-thumbs-up mr-1"></i>
                                            <span>{{ $faq->helpful_count }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="text-gray-400 text-4xl mb-4">
                            <i class="fas fa-search"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ __('No results found') }}
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">{{ __('Try adjusting your search terms.') }}</p>
                        <a href="{{ route('support.faq.index') }}" class="text-blue-500 hover:text-blue-600 font-medium">
                            {{ __('Browse all FAQs') }}
                        </a>
                    </div>
                @endif
            </div>
        @else
            <!-- Categories and Popular FAQs -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Categories -->
                <div class="lg:col-span-2">
                    @if ($categories->count() > 0)
                        <div class="mb-8">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                                {{ __('Browse by Category') }}</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach ($categories as $category)
                                    <a href="{{ route('support.faq.category', $category) }}"
                                        class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow">
                                        <div class="flex items-center justify-between mb-2">
                                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                                {{ $category->name }}</h3>
                                            <span
                                                class="text-sm text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">
                                                {{ $category->active_faqs_count }}
                                            </span>
                                        </div>
                                        <p class="text-gray-600 dark:text-gray-400 text-sm">{{ $category->description }}
                                        </p>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Popular FAQs -->
                    @if ($faqs->count() > 0)
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                                {{ __('Popular Questions') }}</h2>
                            <div class="space-y-4">
                                @foreach ($faqs as $faq)
                                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                                        <button
                                            class="w-full text-left p-6 focus:outline-none focus:ring-2 focus:ring-blue-500 faq-toggle"
                                            onclick="toggleFaq(this)">
                                            <div class="flex items-center justify-between">
                                                <h3 class="text-lg font-medium text-gray-900 dark:text-white pr-4">
                                                    {{ $faq->question }}
                                                </h3>
                                                <div class="flex items-center">
                                                    <span
                                                        class="text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded mr-2">
                                                        {{ $faq->category->name }}
                                                    </span>
                                                    <i
                                                        class="fas fa-chevron-down text-gray-400 faq-icon transition-transform"></i>
                                                </div>
                                            </div>
                                        </button>
                                        <div class="faq-content hidden px-6 pb-6">
                                            <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                                                <div class="prose dark:prose-invert text-gray-600 dark:text-gray-400">
                                                    {!! $faq->answer !!}
                                                </div>
                                                <div
                                                    class="flex items-center justify-between mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                                    <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                                        <i class="fas fa-eye mr-1"></i>
                                                        <span class="mr-3">{{ $faq->view_count }}</span>
                                                        <i class="fas fa-thumbs-up mr-1"></i>
                                                        <span>{{ $faq->helpful_count }}</span>
                                                    </div>
                                                    <div class="flex items-center space-x-2">
                                                        <span
                                                            class="text-sm text-gray-600 dark:text-gray-400">{{ __('Helpful?') }}</span>
                                                        <form action="{{ route('support.faq.helpful', $faq) }}"
                                                            method="POST" class="inline-block">
                                                            @csrf
                                                            <button type="submit"
                                                                class="px-2 py-1 text-xs bg-green-100 text-green-600 hover:bg-green-200 dark:bg-green-900 dark:text-green-400 dark:hover:bg-green-800 rounded transition-colors">
                                                                <i class="fas fa-thumbs-up"></i>
                                                            </button>
                                                        </form>
                                                        <form action="{{ route('support.faq.not-helpful', $faq) }}"
                                                            method="POST" class="inline-block">
                                                            @csrf
                                                            <button type="submit"
                                                                class="px-2 py-1 text-xs bg-red-100 text-red-600 hover:bg-red-200 dark:bg-red-900 dark:text-red-400 dark:hover:bg-red-800 rounded transition-colors">
                                                                <i class="fas fa-thumbs-down"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <div class="space-y-6">
                        <!-- Quick Links -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                {{ __('Quick Links') }}
                            </h3>
                            <div class="space-y-2">
                                <a href="{{ route('support.help.index') }}"
                                    class="block text-blue-500 hover:text-blue-600 text-sm">
                                    <i class="fas fa-book mr-2"></i>{{ __('Help Articles') }}
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

                        <!-- Need Help -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                {{ __('Still Need Help?') }}
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                {{ __('Can\'t find what you\'re looking for? Our support team is here to help.') }}
                            </p>
                            <a href="{{ route('support.contact') }}"
                                class="block w-full text-center px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors text-sm">
                                {{ __('Contact Support') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        function toggleFaq(button) {
            const content = button.nextElementSibling;
            const icon = button.querySelector('.faq-icon');

            content.classList.toggle('hidden');
            icon.classList.toggle('rotate-180');
        }
    </script>
@endpush
