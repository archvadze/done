@extends('layouts.admin')

@section('title', 'Evaluations Management')
@section('subtitle', 'Monitor and manage artwork evaluations and ACQ scores')

@section('content')
    <!-- Search and Filters -->
    <div class="admin-stats-card mb-6">
        <form method="GET" action="{{ route('admin.evaluations') }}"
            class="space-y-4 md:space-y-0 md:flex md:items-end md:space-x-4">
            <!-- Source Filter -->
            <div>
                <label for="source" class="block text-sm font-medium text-gray-700 mb-1">Source</label>
                <select id="source" name="source"
                    class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Sources</option>
                    <option value="human" {{ request('source') === 'human' ? 'selected' : '' }}>Human</option>
                    <option value="ai" {{ request('source') === 'ai' ? 'selected' : '' }}>AI</option>
                    <option value="aggregate" {{ request('source') === 'aggregate' ? 'selected' : '' }}>Aggregate</option>
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="status" name="status"
                    class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>

            <!-- Submit -->
            <div>
                <button type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                    Filter
                </button>
                @if (request()->hasAny(['source', 'status']))
                    <a href="{{ route('admin.evaluations') }}" class="ml-2 text-gray-600 hover:text-gray-900 text-sm">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Evaluations Table -->
    <div class="admin-stats-card">
        <div class="mb-4 flex justify-between items-center">
            <h3 class="text-lg font-medium text-gray-900">
                Evaluations ({{ $evaluations->total() }} total)
            </h3>

            <div class="flex items-center space-x-4">
                <div class="text-sm text-gray-500">
                    Average Score:
                    <span class="font-medium">
                        {{ number_format($evaluations->avg('overall_score') ?? 0, 1) }}
                    </span>
                </div>
            </div>
        </div>

        @if ($evaluations->count() > 0)
            <div class="overflow-x-auto">
                <table class="admin-table">
                    <thead class="bg-gray-50">
                        <tr>
                            <th>Artwork</th>
                            <th>Evaluator</th>
                            <th>Scores</th>
                            <th>Overall</th>
                            <th>Source</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($evaluations as $evaluation)
                            <tr>
                                <!-- Artwork Info -->
                                <td>
                                    <div class="flex items-center space-x-3">
                                        <div class="w-12 h-12 bg-gray-200 rounded-lg overflow-hidden flex-shrink-0">
                                            @if ($evaluation->artwork->isImage())
                                                <img src="{{ $evaluation->artwork->getFileUrl() }}"
                                                    alt="{{ $evaluation->artwork->getTitle() }}"
                                                    class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center">
                                                    <span class="text-gray-400 text-xs">📄</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $evaluation->artwork->getTitle() }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                by {{ $evaluation->artwork->user->name }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Evaluator -->
                                <td>
                                    @if ($evaluation->evaluator)
                                        <div class="flex items-center space-x-2">
                                            <img src="{{ $evaluation->evaluator->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($evaluation->evaluator->name) }}"
                                                alt="{{ $evaluation->evaluator->name }}" class="w-8 h-8 rounded-full">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $evaluation->evaluator->name }}</div>
                                                <div class="text-xs text-gray-500">
                                                    {{ ucfirst($evaluation->evaluator->role ?? 'artist') }}</div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-400">System</span>
                                    @endif
                                </td>

                                <!-- Individual Scores -->
                                <td>
                                    <div class="text-xs space-y-1">
                                        @if ($evaluation->score_technique)
                                            <div>Tech: <span class="font-medium">{{ $evaluation->score_technique }}</span>
                                            </div>
                                        @endif
                                        @if ($evaluation->score_composition)
                                            <div>Comp: <span
                                                    class="font-medium">{{ $evaluation->score_composition }}</span></div>
                                        @endif
                                        @if ($evaluation->score_originality)
                                            <div>Orig: <span
                                                    class="font-medium">{{ $evaluation->score_originality }}</span></div>
                                        @endif
                                        @if ($evaluation->score_impact)
                                            <div>Impact: <span class="font-medium">{{ $evaluation->score_impact }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                <!-- Overall Score -->
                                <td>
                                    <span class="admin-badge admin-badge-info text-lg">
                                        {{ number_format($evaluation->overall_score, 1) }}
                                    </span>
                                </td>

                                <!-- Source -->
                                <td>
                                    <span
                                        class="admin-badge {{ $evaluation->source === 'human' ? 'admin-badge-success' : ($evaluation->source === 'ai' ? 'admin-badge-warning' : 'admin-badge-info') }}">
                                        {{ ucfirst($evaluation->source) }}
                                    </span>
                                </td>

                                <!-- Status -->
                                <td>
                                    <span
                                        class="admin-badge {{ $evaluation->status === 'approved' ? 'admin-badge-success' : ($evaluation->status === 'rejected' ? 'admin-badge-danger' : 'admin-badge-warning') }}">
                                        {{ ucfirst($evaluation->status ?? 'pending') }}
                                    </span>
                                </td>

                                <!-- Date -->
                                <td>
                                    <span
                                        class="text-sm text-gray-500">{{ $evaluation->created_at->format('M j, Y') }}</span>
                                    <div class="text-xs text-gray-400">{{ $evaluation->created_at->format('H:i') }}</div>
                                </td>

                                <!-- Actions -->
                                <td>
                                    <div class="flex items-center space-x-2">
                                        <button class="text-blue-600 hover:text-blue-700 text-sm"
                                            onclick="viewEvaluation({{ $evaluation->id }})">
                                            View
                                        </button>

                                        @if ($evaluation->status !== 'approved')
                                            <button class="text-green-600 hover:text-green-700 text-sm"
                                                onclick="approveEvaluation({{ $evaluation->id }})">
                                                Approve
                                            </button>
                                        @endif

                                        @if ($evaluation->status !== 'rejected')
                                            <button class="text-red-600 hover:text-red-700 text-sm"
                                                onclick="rejectEvaluation({{ $evaluation->id }})">
                                                Reject
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $evaluations->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-8">
                <div class="text-gray-400 text-6xl mb-4">⭐</div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No evaluations found</h3>
                <p class="text-gray-500">
                    @if (request()->hasAny(['source', 'status']))
                        Try adjusting your filter criteria.
                    @else
                        No evaluations have been submitted yet.
                    @endif
                </p>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        function viewEvaluation(evaluationId) {
            // This would open a modal or navigate to detailed view
            alert(`Detailed evaluation view for ID ${evaluationId} will be implemented soon.`);
        }

        function approveEvaluation(evaluationId) {
            if (confirm('Are you sure you want to approve this evaluation?')) {
                updateEvaluationStatus(evaluationId, 'approved');
            }
        }

        function rejectEvaluation(evaluationId) {
            if (confirm('Are you sure you want to reject this evaluation?')) {
                updateEvaluationStatus(evaluationId, 'rejected');
            }
        }

        function updateEvaluationStatus(evaluationId, status) {
            // Create and submit form
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/evaluations/${evaluationId}/status`;

            // Add CSRF token
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            form.appendChild(csrfInput);

            // Add status
            const statusInput = document.createElement('input');
            statusInput.type = 'hidden';
            statusInput.name = 'status';
            statusInput.value = status;
            form.appendChild(statusInput);

            document.body.appendChild(form);
            form.submit();
        }
    </script>
@endpush
