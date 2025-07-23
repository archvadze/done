<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Evaluations - {{ $artwork->getTitle() }} - Acumen Craft</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-white shadow-sm border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center space-x-4">
                        <a href="{{ url('/') }}" class="text-xl font-bold text-gray-900">
                            🎨 Acumen Craft
                        </a>
                        <nav class="hidden md:flex space-x-4">
                            <a href="{{ route('artworks.show', $artwork) }}" class="text-blue-600 hover:text-blue-700">←
                                Back to Artwork</a>
                        </nav>
                    </div>
                    <div class="flex items-center space-x-4">
                        @auth
                            <a href="{{ route('users.profile') }}" class="text-gray-600 hover:text-gray-900">My Profile</a>
                            <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-900">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900">Login</a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <div class="max-w-6xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Artwork Evaluations</h1>
                <p class="text-gray-600">Professional assessments for "{{ $artwork->getTitle() }}" by
                    {{ $artwork->user->name }}</p>
            </div>

            <!-- ACQ Score Summary -->
            <div class="bg-white rounded-lg shadow-sm border p-6 mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 mb-2">Current ACQ Score</h2>
                        <p class="text-gray-600">Based on {{ $evaluations->total() }} evaluations</p>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-bold text-blue-600 mb-2">
                            {{ $artwork->acq_score ? number_format($artwork->acq_score, 1) : 'N/A' }}
                        </div>
                        <div class="text-sm text-gray-500">out of 10.0</div>
                    </div>
                </div>

                @if ($artwork->acq_breakdown)
                    <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach ($artwork->acq_breakdown['average_scores'] ?? [] as $criterion => $score)
                            <div class="text-center">
                                <div class="text-lg font-semibold text-gray-900">{{ $score ?: 'N/A' }}</div>
                                <div class="text-sm text-gray-600">{{ ucfirst($criterion) }}</div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Actions -->
            @auth
                @if (auth()->id() !== $artwork->user_id)
                    <div class="mb-6">
                        <a href="{{ route('evaluations.create', $artwork) }}"
                            class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                            Add Your Evaluation
                        </a>
                    </div>
                @endif
            @else
                <div class="mb-6">
                    <p class="text-gray-600">
                        <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-700">Login</a>
                        to add your evaluation of this artwork.
                    </p>
                </div>
            @endauth

            <!-- Evaluations List -->
            @if ($evaluations->count() > 0)
                <div class="space-y-6">
                    @foreach ($evaluations as $evaluation)
                        <div class="bg-white rounded-lg shadow-sm border p-6">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center">
                                        @if ($evaluation->evaluator->avatar_url)
                                            <img src="{{ $evaluation->evaluator->avatar_url }}"
                                                alt="{{ $evaluation->evaluator->name }}"
                                                class="w-10 h-10 rounded-full object-cover">
                                        @else
                                            <span class="text-gray-600 font-medium">
                                                {{ substr($evaluation->evaluator->name, 0, 1) }}
                                            </span>
                                        @endif
                                    </div>
                                    <div>
                                        <h3 class="font-medium text-gray-900">{{ $evaluation->evaluator->name }}</h3>
                                        <p class="text-sm text-gray-500">{{ $evaluation->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>

                                <div class="text-right">
                                    <div class="text-2xl font-bold text-blue-600">
                                        {{ number_format($evaluation->overall_score ?: $evaluation->getAverageScoreAttribute(), 1) }}
                                    </div>
                                    <div class="text-sm text-gray-500">Overall Score</div>
                                </div>
                            </div>

                            <!-- Individual Scores -->
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                                @if ($evaluation->score_technique)
                                    <div class="text-center p-3 bg-gray-50 rounded">
                                        <div class="font-semibold text-gray-900">{{ $evaluation->score_technique }}
                                        </div>
                                        <div class="text-sm text-gray-600">Technique</div>
                                    </div>
                                @endif
                                @if ($evaluation->score_composition)
                                    <div class="text-center p-3 bg-gray-50 rounded">
                                        <div class="font-semibold text-gray-900">{{ $evaluation->score_composition }}
                                        </div>
                                        <div class="text-sm text-gray-600">Composition</div>
                                    </div>
                                @endif
                                @if ($evaluation->score_originality)
                                    <div class="text-center p-3 bg-gray-50 rounded">
                                        <div class="font-semibold text-gray-900">{{ $evaluation->score_originality }}
                                        </div>
                                        <div class="text-sm text-gray-600">Originality</div>
                                    </div>
                                @endif
                                @if ($evaluation->score_impact)
                                    <div class="text-center p-3 bg-gray-50 rounded">
                                        <div class="font-semibold text-gray-900">{{ $evaluation->score_impact }}</div>
                                        <div class="text-sm text-gray-600">Impact</div>
                                    </div>
                                @endif
                            </div>

                            <!-- Feedback -->
                            @if ($evaluation->feedback_text)
                                <div class="border-t pt-4">
                                    <h4 class="font-medium text-gray-900 mb-2">Feedback</h4>
                                    <p class="text-gray-700">{{ $evaluation->feedback_text }}</p>
                                </div>
                            @endif

                            <!-- Actions for own evaluation -->
                            @auth
                                @if (auth()->id() === $evaluation->evaluator_id)
                                    <div class="border-t pt-4 flex space-x-4">
                                        <a href="{{ route('evaluations.edit', [$artwork, $evaluation]) }}"
                                            class="text-blue-600 hover:text-blue-700 text-sm">Edit</a>
                                        <form method="POST"
                                            action="{{ route('evaluations.destroy', [$artwork, $evaluation]) }}"
                                            class="inline"
                                            onsubmit="return confirm('Are you sure you want to delete this evaluation?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-red-600 hover:text-red-700 text-sm">Delete</button>
                                        </form>
                                    </div>
                                @endif
                            @endauth
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $evaluations->links() }}
                </div>
            @else
                <div class="bg-white rounded-lg shadow-sm border p-8 text-center">
                    <div class="text-gray-400 text-6xl mb-4">🏆</div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Evaluations Yet</h3>
                    <p class="text-gray-600 mb-4">This artwork hasn't been evaluated by the community yet.</p>
                    @auth
                        @if (auth()->id() !== $artwork->user_id)
                            <a href="{{ route('evaluations.create', $artwork) }}"
                                class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                                Be the First to Evaluate
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}"
                            class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                            Login to Evaluate
                        </a>
                    @endauth
                </div>
            @endif
        </div>
    </div>
</body>

</html>
