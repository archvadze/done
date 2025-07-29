@extends('layouts.app')

@section('title', $faq->question)

@section('content')
<div class="min-h-screen bg-primary">
    <div class="max-w-4xl mx-auto px-4 py-8">
        <!-- Navigation -->
        <nav class="text-gray-400 mb-8">
            <a href="{{ route('support.faq.index') }}" class="hover:text-white">FAQs</a>
            <span class="mx-2">/</span>
            <a href="{{ route('support.faq.category', $faq->category) }}" class="hover:text-white">{{ $faq->category->name }}</a>
            <span class="mx-2">/</span>
            <span class="text-white">Question</span>
        </nav>

        <!-- FAQ Content -->
        <div class="bg-secondary p-8 mb-8">
            <h1 class="text-3xl font-bold text-white mb-6">{{ $faq->question }}</h1>
            
            <div class="prose prose-invert prose-lg max-w-none text-gray-300">
                {!! nl2br(e($faq->answer)) !!}
            </div>

            <!-- Helpful/Not Helpful -->
            <div class="mt-8 pt-6 border-t border-gray-600">
                <p class="text-gray-400 mb-3">Was this helpful?</p>
                <div class="flex space-x-4">
                    <button onclick="markHelpful({{ $faq->id }}, true)" 
                            class="flex items-center space-x-2 px-4 py-2 bg-green-600 text-white hover:bg-green-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L9 7v13m-7-10h2m5-9h2.5a2 2 0 012 2v2a2 2 0 01-2 2H9"></path>
                        </svg>
                        <span>Yes</span>
                    </button>
                    
                    <button onclick="markHelpful({{ $faq->id }}, false)" 
                            class="flex items-center space-x-2 px-4 py-2 bg-red-600 text-white hover:bg-red-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14H5.236a2 2 0 01-1.789-2.894l3.5-7A2 2 0 018.736 3h4.018c.163 0 .326.02.485.06L17 4m-7 10v2a2 2 0 002 2h.095c.5 0 .905-.405.905-.905 0-.714.211-1.412.608-2.006L15 17V4m-7 10H6m7 9h-2.5a2 2 0 01-2-2v-2a2 2 0 012-2H15"></path>
                        </svg>
                        <span>No</span>
                    </button>
                </div>
                <div id="feedback-message" class="mt-3 text-sm"></div>
            </div>
        </div>

        <!-- Related FAQs -->
        @if($relatedFaqs->count() > 0)
            <div>
                <h2 class="text-2xl font-bold text-white mb-6">Related Questions</h2>
                <div class="space-y-4">
                    @foreach($relatedFaqs as $relatedFaq)
                        <div class="bg-secondary p-4">
                            <h3 class="text-lg font-semibold text-white mb-2">
                                <a href="{{ route('support.faq.show', $relatedFaq) }}" class="hover:text-secondary">
                                    {{ $relatedFaq->question }}
                                </a>
                            </h3>
                            <div class="text-gray-300 text-sm">
                                {{ Str::limit(strip_tags($relatedFaq->answer), 150) }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

<script>
function markHelpful(faqId, helpful) {
    const url = helpful ? `/support/faq/${faqId}/helpful` : `/support/faq/${faqId}/not-helpful`;
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        const messageDiv = document.getElementById('feedback-message');
        if (data.success) {
            messageDiv.innerHTML = '<span class="text-green-400">Thank you for your feedback!</span>';
        } else {
            messageDiv.innerHTML = '<span class="text-red-400">Error submitting feedback. Please try again.</span>';
        }
    })
    .catch(error => {
        const messageDiv = document.getElementById('feedback-message');
        messageDiv.innerHTML = '<span class="text-red-400">Error submitting feedback. Please try again.</span>';
    });
}
</script>
@endsection
