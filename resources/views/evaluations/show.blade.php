<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Evaluation Details - {{ $artwork->getTitle() }} - Acumen Craft</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .score-circle {
            width: 4rem;
            height: 4rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            font-weight: bold;
            color: white;
        }

        .score-excellent {
            background: linear-gradient(135deg, #10b981, #059669);
        }

        .score-good {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
        }

        .score-average {
            background: linear-gradient(135deg, #f59e0b, #d97706);
        }

        .score-poor {
            background: linear-gradient(135deg, #ef4444, #dc2626);
        }

        .criteria-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .criteria-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .progress-bar {
            width: 100%;
            height: 8px;
            background-color: #e5e7eb;
            border-radius: 4px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            border-radius: 4px;
            transition: width 0.3s ease;
        }
    </style>
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
                            <a href="{{ route('evaluations.index', $artwork) }}"
                                class="text-gray-600 hover:text-gray-900">All Evaluations</a>
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
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Evaluation Details</h1>
                <p class="text-gray-600">Assessment of "{{ $artwork->getTitle() }}" by {{ $artwork->user->name }}</p>
                <div class="flex items-center space-x-4 mt-2 text-sm text-gray-500">
                    <span>👤 Evaluated by
                        {{ $evaluation->evaluator ? $evaluation->evaluator->name : 'Anonymous' }}</span>
                    <span>📅 {{ $evaluation->created_at->format('M j, Y') }}</span>
                    <span>🕒 {{ $evaluation->created_at->diffForHumans() }}</span>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2">
                    <!-- Overall Score -->
                    <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xl font-semibold text-gray-900">Overall Rating</h2>
                            @php
                                $overallScore = $evaluation->overall_score ?: $evaluation->getAverageScoreAttribute();
                                $scoreClass =
                                    $overallScore >= 8
                                        ? 'score-excellent'
                                        : ($overallScore >= 6
                                            ? 'score-good'
                                            : ($overallScore >= 4
                                                ? 'score-average'
                                                : 'score-poor'));
                            @endphp
                            <div class="score-circle {{ $scoreClass }}">
                                {{ number_format($overallScore, 1) }}
                            </div>
                        </div>

                        <div class="progress-bar mb-4">
                            <div class="progress-fill {{ $scoreClass }}"
                                style="width: {{ ($overallScore / 10) * 100 }}%"></div>
                        </div>

                        <p class="text-sm text-gray-600">
                            @if ($overallScore >= 9)
                                Exceptional work with outstanding quality across all criteria.
                            @elseif($overallScore >= 8)
                                Excellent artwork with high-quality execution and strong impact.
                            @elseif($overallScore >= 7)
                                Very good work with solid technical and creative qualities.
                            @elseif($overallScore >= 6)
                                Good artwork with notable strengths and minor areas for improvement.
                            @elseif($overallScore >= 5)
                                Average work with both strengths and weaknesses present.
                            @else
                                Below average work with significant room for improvement.
                            @endif
                        </p>
                    </div>

                    <!-- Detailed Criteria Scores -->
                    <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-6">Detailed Assessment</h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Technique -->
                            <div class="criteria-card bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="font-medium text-gray-900">🎯 Technical Skill</h3>
                                    <span
                                        class="text-lg font-bold text-blue-600">{{ $evaluation->score_technique }}/10</span>
                                </div>
                                <div class="progress-bar mb-2">
                                    <div class="progress-fill bg-blue-500"
                                        style="width: {{ ($evaluation->score_technique / 10) * 100 }}%"></div>
                                </div>
                                <p class="text-xs text-gray-600">Craftsmanship and mastery of tools/medium</p>
                            </div>

                            <!-- Composition -->
                            <div class="criteria-card bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="font-medium text-gray-900">🎨 Composition</h3>
                                    <span
                                        class="text-lg font-bold text-green-600">{{ $evaluation->score_composition }}/10</span>
                                </div>
                                <div class="progress-bar mb-2">
                                    <div class="progress-fill bg-green-500"
                                        style="width: {{ ($evaluation->score_composition / 10) * 100 }}%"></div>
                                </div>
                                <p class="text-xs text-gray-600">Visual balance, color harmony, design principles</p>
                            </div>

                            <!-- Originality -->
                            <div class="criteria-card bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="font-medium text-gray-900">💡 Originality</h3>
                                    <span
                                        class="text-lg font-bold text-purple-600">{{ $evaluation->score_originality }}/10</span>
                                </div>
                                <div class="progress-bar mb-2">
                                    <div class="progress-fill bg-purple-500"
                                        style="width: {{ ($evaluation->score_originality / 10) * 100 }}%"></div>
                                </div>
                                <p class="text-xs text-gray-600">Uniqueness, innovation, creative approach</p>
                            </div>

                            <!-- Impact -->
                            <div class="criteria-card bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="font-medium text-gray-900">⚡ Emotional Impact</h3>
                                    <span
                                        class="text-lg font-bold text-orange-600">{{ $evaluation->score_impact }}/10</span>
                                </div>
                                <div class="progress-bar mb-2">
                                    <div class="progress-fill bg-orange-500"
                                        style="width: {{ ($evaluation->score_impact / 10) * 100 }}%"></div>
                                </div>
                                <p class="text-xs text-gray-600">Emotional response and visual appeal</p>
                            </div>
                        </div>
                    </div>

                    <!-- Written Feedback -->
                    @if ($evaluation->feedback_text)
                        <div class="bg-white rounded-lg shadow-sm border p-6">
                            <h2 class="text-xl font-semibold text-gray-900 mb-4">Written Feedback</h2>
                            <div class="prose prose-gray max-w-none">
                                <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-blue-500">
                                    <p class="text-gray-700 leading-relaxed">{{ $evaluation->feedback_text }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <!-- Artwork Preview -->
                    <div class="bg-white rounded-lg shadow-sm border p-4 mb-6">
                        <h3 class="font-semibold text-gray-900 mb-3">Artwork</h3>
                        <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden mb-3">
                            @if ($artwork->image_path)
                                <img src="{{ asset('storage/' . $artwork->image_path) }}"
                                    alt="{{ $artwork->getTitle() }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                    <span class="text-4xl">🎨</span>
                                </div>
                            @endif
                        </div>
                        <h4 class="font-medium text-gray-900">{{ $artwork->getTitle() }}</h4>
                        <p class="text-sm text-gray-600">by {{ $artwork->user->name }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $artwork->created_at->format('M j, Y') }}</p>
                    </div>

                    <!-- Evaluation Meta -->
                    <div class="bg-white rounded-lg shadow-sm border p-4 mb-6">
                        <h3 class="font-semibold text-gray-900 mb-3">Evaluation Info</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Status:</span>
                                <span
                                    class="font-medium capitalize
                                    {{ $evaluation->status === 'approved'
                                        ? 'text-green-600'
                                        : ($evaluation->status === 'pending'
                                            ? 'text-yellow-600'
                                            : 'text-red-600') }}">
                                    {{ $evaluation->status }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Source:</span>
                                <span class="font-medium capitalize">{{ $evaluation->source }}</span>
                            </div>
                            @if ($evaluation->created_at != $evaluation->updated_at)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Last Updated:</span>
                                    <span class="font-medium">{{ $evaluation->updated_at->diffForHumans() }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Actions -->
                    @auth
                        @if (auth()->id() === $evaluation->evaluator_id)
                            <div class="bg-white rounded-lg shadow-sm border p-4">
                                <h3 class="font-semibold text-gray-900 mb-3">Your Actions</h3>
                                <div class="space-y-2">
                                    <a href="{{ route('evaluations.edit', [$artwork, $evaluation]) }}"
                                        class="block w-full text-center bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition-colors">
                                        Edit Evaluation
                                    </a>
                                    <button onclick="confirmDelete()"
                                        class="block w-full text-center bg-red-600 text-white py-2 rounded-md hover:bg-red-700 transition-colors">
                                        Delete Evaluation
                                    </button>
                                </div>
                            </div>

                            <!-- Hidden Delete Form -->
                            <form id="delete-form" method="POST"
                                action="{{ route('evaluations.destroy', [$artwork, $evaluation]) }}"
                                style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete() {
            if (confirm('Are you sure you want to delete your evaluation? This action cannot be undone.')) {
                document.getElementById('delete-form').submit();
            }
        }
    </script>
</body>

</html>
