@extends('layouts.app')

@section('title', $category->name . ' - FAQs')

@section('content')
<div class="min-h-screen bg-primary">
    <div class="max-w-4xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <nav class="text-gray-400 mb-4">
                <a href="{{ route('support.faq.index') }}" class="hover:text-white">FAQs</a>
                <span class="mx-2">/</span>
                <span class="text-white">{{ $category->name }}</span>
            </nav>
            
            <h1 class="text-4xl font-bold text-secondary mb-4">{{ $category->name }}</h1>
            @if($category->description)
                <p class="text-white text-lg">{{ $category->description }}</p>
            @endif
        </div>

        <!-- FAQs -->
        @if($faqs->count() > 0)
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
                        <div class="mt-3">
                            <a href="{{ route('support.faq.show', $faq) }}" class="text-secondary hover:underline">
                                Read more →
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $faqs->links() }}
            </div>
        @else
            <div class="bg-secondary p-8 text-center">
                <h3 class="text-xl font-semibold text-white mb-2">No FAQs in This Category</h3>
                <p class="text-gray-300 mb-4">This category doesn't have any questions yet.</p>
                <a href="{{ route('support.faq.index') }}" class="btn-primary">
                    Browse All Categories
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
