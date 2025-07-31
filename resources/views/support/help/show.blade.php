@extends('layouts.app')

@section('title', $article->title)

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <!-- Navigation -->
            <div class="mb-6">
                <a href="{{ route('support.help.index') }}" class="text-blue-500 hover:text-blue-600 font-medium">
                    <i class="fas fa-arrow-left mr-2"></i>{{ __('Back to Help Articles') }}
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <article class="lg:col-span-2">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-8">
                        <!-- Article Header -->
                        <header class="mb-8">
                            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">
                                {{ $article->title }}
                            </h1>

                            <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400 mb-4">
                                <div class="flex items-center">
                                    <img src="{{ $article->author->avatar ?? asset('images/default-avatar.png') }}"
                                        alt="{{ $article->author->name }}" class="w-8 h-8 rounded-full mr-3">
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-white">{{ $article->author->name }}
                                        </div>
                                        <div>{{ __('Published') }} {{ $article->published_at->format('F j, Y') }}</div>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-4">
                                    <div class="flex items-center">
                                        <i class="fas fa-eye mr-1"></i>
                                        <span>{{ $article->view_count }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-thumbs-up mr-1"></i>
                                        <span>{{ $article->helpful_count }}</span>
                                    </div>
                                </div>
                            </div>

                            @if ($article->tags)
                                <div class="flex flex-wrap gap-2 mb-6">
                                    @foreach ($article->tags as $tag)
                                        <span
                                            class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 text-sm rounded-full">
                                            {{ $tag }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif

                            @if ($article->excerpt)
                                <div
                                    class="text-lg text-gray-600 dark:text-gray-400 mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    {{ $article->excerpt }}
                                </div>
                            @endif
                        </header>

                        <!-- Article Content -->
                        <div class="prose prose-lg dark:prose-invert max-w-none">
                            {!! $article->content !!}
                        </div>

                        <!-- Article Footer -->
                        <footer class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ __('Last updated') }}: {{ $article->updated_at->format('F j, Y') }}
                                </div>

                                <!-- Helpful Buttons -->
                                <div class="flex items-center space-x-2">
                                    <span
                                        class="text-sm text-gray-600 dark:text-gray-400">{{ __('Was this helpful?') }}</span>
                                    <form action="{{ route('support.help.helpful', $article) }}" method="POST"
                                        class="inline-block">
                                        @csrf
                                        <button type="submit"
                                            class="px-3 py-1 text-sm bg-green-100 text-green-600 hover:bg-green-200 dark:bg-green-900 dark:text-green-400 dark:hover:bg-green-800 rounded-lg transition-colors">
                                            <i class="fas fa-thumbs-up mr-1"></i>{{ __('Yes') }}
                                        </button>
                                    </form>
                                    <form action="{{ route('support.help.not-helpful', $article) }}" method="POST"
                                        class="inline-block">
                                        @csrf
                                        <button type="submit"
                                            class="px-3 py-1 text-sm bg-red-100 text-red-600 hover:bg-red-200 dark:bg-red-900 dark:text-red-400 dark:hover:bg-red-800 rounded-lg transition-colors">
                                            <i class="fas fa-thumbs-down mr-1"></i>{{ __('No') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </footer>
                    </div>
                </article>

                <!-- Sidebar -->
                <aside class="lg:col-span-1">
                    <div class="space-y-6">
                        <!-- Table of Contents (if the article is long enough) -->
                        @if (str_word_count(strip_tags($article->content)) > 300)
                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                    {{ __('Table of Contents') }}
                                </h3>
                                <div id="table-of-contents" class="space-y-2 text-sm">
                                    <!-- JavaScript will populate this -->
                                </div>
                            </div>
                        @endif

                        <!-- Related Articles -->
                        @if ($relatedArticles->count() > 0)
                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                    {{ __('Related Articles') }}
                                </h3>
                                <div class="space-y-3">
                                    @foreach ($relatedArticles as $related)
                                        <a href="{{ route('support.help.show', $related) }}" class="block group">
                                            <h4
                                                class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 mb-1">
                                                {{ $related->title }}
                                            </h4>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                                                {{ Str::limit($related->excerpt, 100) }}
                                            </p>
                                            <div class="flex items-center text-xs text-gray-500 dark:text-gray-400">
                                                <i class="fas fa-eye mr-1"></i>
                                                {{ $related->view_count }}
                                                <span class="mx-2">•</span>
                                                <i class="fas fa-thumbs-up mr-1"></i>
                                                {{ $related->helpful_count }}
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Contact Support -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                {{ __('Need More Help?') }}
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                {{ __('If this article didn\'t answer your question, we\'re here to help.') }}
                            </p>
                            <div class="space-y-2">
                                <a href="{{ route('support.contact') }}"
                                    class="block w-full text-center px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors text-sm">
                                    {{ __('Contact Support') }}
                                </a>
                                @auth
                                    <a href="{{ route('support.tickets.create') }}"
                                        class="block w-full text-center px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors text-sm">
                                        {{ __('Create Support Ticket') }}
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Generate table of contents from h2 and h3 tags
        document.addEventListener('DOMContentLoaded', function() {
            const tocContainer = document.getElementById('table-of-contents');
            if (!tocContainer) return;

            const headings = document.querySelectorAll('.prose h2, .prose h3');
            if (headings.length === 0) {
                tocContainer.parentElement.style.display = 'none';
                return;
            }

            headings.forEach((heading, index) => {
                // Add an ID to the heading if it doesn't have one
                if (!heading.id) {
                    heading.id = 'heading-' + index;
                }

                // Create TOC link
                const link = document.createElement('a');
                link.href = '#' + heading.id;
                link.textContent = heading.textContent;
                link.className = 'block text-blue-500 hover:text-blue-600 py-1';

                if (heading.tagName === 'H3') {
                    link.className += ' ml-4';
                }

                tocContainer.appendChild(link);
            });
        });
    </script>
@endpush
