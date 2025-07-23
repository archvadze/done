<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Evaluate Artwork - {{ $artwork->getTitle() }} - Acumen Craft</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .star-rating {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .star-rating input[type="range"] {
            flex: 1;
            margin: 0 0.5rem;
        }

        .score-display {
            font-weight: bold;
            font-size: 1.1rem;
            color: #3b82f6;
            min-width: 2rem;
            text-align: center;
        }

        .criteria-explanation {
            font-size: 0.875rem;
            color: #6b7280;
            margin-top: 0.5rem;
        }

        .artwork-preview {
            max-height: 300px;
            object-fit: contain;
            border-radius: 0.5rem;
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

        <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Evaluate Artwork</h1>
                <p class="text-gray-600">Provide your professional assessment of this artwork. Your evaluation will help
                    determine its ACQ (Acumen Craft Quotient) score.</p>
            </div>

            <!-- Artwork Preview -->
            <div class="bg-white rounded-lg shadow-sm border p-6 mb-8">
                <div class="flex flex-col md:flex-row gap-6">
                    <div class="md:w-1/2">
                        @if ($artwork->isImage())
                            <img src="{{ $artwork->getFileUrl() }}" alt="{{ $artwork->getTitle() }}"
                                class="artwork-preview w-full">
                        @else
                            <div
                                class="artwork-preview w-full bg-gray-100 border-2 border-dashed border-gray-300 flex items-center justify-center">
                                <div class="text-center">
                                    <span class="text-4xl">📄</span>
                                    <p class="text-gray-600 mt-2">{{ $artwork->getFileExtension() }} File</p>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="md:w-1/2">
                        <h2 class="text-xl font-semibold text-gray-900 mb-2">{{ $artwork->getTitle() }}</h2>
                        <p class="text-gray-600 mb-4">by {{ $artwork->user->name }}</p>

                        @if ($artwork->getDescription())
                            <div class="mb-4">
                                <h3 class="font-medium text-gray-900 mb-1">Description</h3>
                                <p class="text-gray-600 text-sm">{{ Str::limit($artwork->getDescription(), 200) }}</p>
                            </div>
                        @endif

                        @if ($artwork->category)
                            <p class="text-sm text-gray-500">Category: <span
                                    class="font-medium">{{ $artwork->category }}</span></p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Evaluation Form -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <form method="POST" action="{{ route('evaluations.store', $artwork) }}" id="evaluation-form">
                    @csrf

                    <!-- Error Messages -->
                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Scoring Criteria -->
                    <div class="space-y-6 mb-8">
                        <!-- Technique -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Technical Skill
                            </label>
                            <div class="star-rating">
                                <span class="text-sm text-gray-600 w-8">1</span>
                                <input type="range" id="score_technique" name="score_technique" min="1"
                                    max="10" value="5" class="flex-1"
                                    oninput="updateScore('technique', this.value)">
                                <span class="text-sm text-gray-600 w-8">10</span>
                                <div class="score-display" id="technique-score">5</div>
                            </div>
                            <p class="criteria-explanation">
                                Assess the technical execution, craftsmanship, and mastery of the medium/tools used.
                            </p>
                        </div>

                        <!-- Composition -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Composition & Design
                            </label>
                            <div class="star-rating">
                                <span class="text-sm text-gray-600 w-8">1</span>
                                <input type="range" id="score_composition" name="score_composition" min="1"
                                    max="10" value="5" class="flex-1"
                                    oninput="updateScore('composition', this.value)">
                                <span class="text-sm text-gray-600 w-8">10</span>
                                <div class="score-display" id="composition-score">5</div>
                            </div>
                            <p class="criteria-explanation">
                                Evaluate balance, color harmony, visual flow, and overall design principles.
                            </p>
                        </div>

                        <!-- Originality -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Originality & Creativity
                            </label>
                            <div class="star-rating">
                                <span class="text-sm text-gray-600 w-8">1</span>
                                <input type="range" id="score_originality" name="score_originality" min="1"
                                    max="10" value="5" class="flex-1"
                                    oninput="updateScore('originality', this.value)">
                                <span class="text-sm text-gray-600 w-8">10</span>
                                <div class="score-display" id="originality-score">5</div>
                            </div>
                            <p class="criteria-explanation">
                                Rate the uniqueness, innovation, and creative approach of the work.
                            </p>
                        </div>

                        <!-- Impact -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Emotional Impact
                            </label>
                            <div class="star-rating">
                                <span class="text-sm text-gray-600 w-8">1</span>
                                <input type="range" id="score_impact" name="score_impact" min="1"
                                    max="10" value="5" class="flex-1"
                                    oninput="updateScore('impact', this.value)">
                                <span class="text-sm text-gray-600 w-8">10</span>
                                <div class="score-display" id="impact-score">5</div>
                            </div>
                            <p class="criteria-explanation">
                                Consider the emotional response, visual appeal, and overall impact on the viewer.
                            </p>
                        </div>
                    </div>

                    <!-- Overall Score Display -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-blue-900">Calculated Average Score:</span>
                            <span class="text-2xl font-bold text-blue-600" id="overall-score">5.0</span>
                        </div>
                    </div>

                    <!-- Feedback -->
                    <div class="mb-6">
                        <label for="feedback_text" class="block text-sm font-medium text-gray-700 mb-2">
                            Written Feedback (Optional)
                        </label>
                        <textarea id="feedback_text" name="feedback_text" rows="4"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Provide constructive feedback, suggestions, or comments about this artwork...">{{ old('feedback_text') }}</textarea>
                        <p class="text-sm text-gray-500 mt-1">Your feedback will be visible to the artist and help them
                            improve.</p>
                    </div>

                    <!-- Submission -->
                    <div class="flex items-center justify-between">
                        <a href="{{ route('artworks.show', $artwork) }}"
                            class="text-gray-600 hover:text-gray-900">Cancel</a>

                        <button type="submit"
                            class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                            Submit Evaluation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function updateScore(criterion, value) {
            document.getElementById(criterion + '-score').textContent = value;
            calculateOverallScore();
        }

        function calculateOverallScore() {
            const technique = parseInt(document.getElementById('score_technique').value);
            const composition = parseInt(document.getElementById('score_composition').value);
            const originality = parseInt(document.getElementById('score_originality').value);
            const impact = parseInt(document.getElementById('score_impact').value);

            const average = (technique + composition + originality + impact) / 4;
            document.getElementById('overall-score').textContent = average.toFixed(1);
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            calculateOverallScore();
        });
    </script>
</body>

</html>
