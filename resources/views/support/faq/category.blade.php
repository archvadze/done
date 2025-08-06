@extends('layouts.app')

@section('title', $category->name . ' - FAQ')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="flex mb-8" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('support.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                    <svg class="w-3 h-3 mr-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/>
                    </svg>
                    Support
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                    </svg>
                    <a href="{{ route('support.faq.index') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2 dark:text-gray-400 dark:hover:text-white">FAQ</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2 dark:text-gray-400">{{ $category->name }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Category Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">{{ $category->name }}</h1>
        @if($category->description)
            <p class="text-lg text-gray-600 dark:text-gray-400">{{ $category->description }}</p>
        @endif
    </div>

    <!-- FAQs List -->
    @if($faqs->count() > 0)
        <div class="space-y-6">
            @foreach($faqs as $faq)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">
                        <a href="{{ route('support.faq.show', $faq) }}" class="hover:text-blue-500">
                            {{ $faq->question }}
                        </a>
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                        {{ Str::limit(strip_tags($faq->answer), 200) }}
                    </p>
                    <div class="flex items-center gap-4 text-sm text-gray-500">
                        <span>
                            <i class="fas fa-eye mr-1"></i>
                            {{ number_format($faq->view_count) }} {{ __('views') }}
                        </span>
                        <span>
                            <i class="fas fa-thumbs-up mr-1"></i>
                            {{ number_format($faq->helpful_count) }} {{ __('helpful') }}
                        </span>
                        @if($faq->updated_at)
                            <span>
                                <i class="fas fa-clock mr-1"></i>
                                {{ __('Updated') }} {{ $faq->updated_at->diffForHumans() }}
                            </span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($faqs->hasPages())
            <div class="mt-8">
                {{ $faqs->links() }}
            </div>
        @endif
    @else
        <div class="text-center py-12">
            <div class="max-w-md mx-auto">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.44-.827-6.072-2.208C7.557 11.169 9.67 10 12 10s4.443 1.169 6.072 2.792zM6.72 6.72L17.28 17.28" />
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">{{ __('No FAQs Found') }}</h3>
                <p class="mt-2 text-gray-500 dark:text-gray-400">{{ __('There are no frequently asked questions in this category yet.') }}</p>
                <div class="mt-6">
                    <a href="{{ route('support.faq.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        <i class="fas fa-arrow-left mr-2"></i>
                        {{ __('Back to FAQ') }}
                    </a>
                </div>
            </div>
        </div>
    @endif

    <!-- Back to categories -->
    <div class="mt-8 pt-8 border-t border-gray-200 dark:border-gray-700">
        <a href="{{ route('support.faq.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-500">
            <i class="fas fa-arrow-left mr-2"></i>
            {{ __('Back to all FAQ categories') }}
        </a>
    </div>
</div>
@endsection