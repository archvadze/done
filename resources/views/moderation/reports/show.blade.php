@extends('layouts.app')

@section('title', 'Report Details')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Report #{{ $report->id }}</h1>
                    <p class="text-gray-600">{{ ucfirst(str_replace('_', ' ', $report->reason)) }} •
                        {{ $report->created_at->format('M j, Y g:i A') }}</p>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="px-3 py-1 text-sm font-medium rounded-full {{ $report->getStatusBadgeColor() }}">
                        {{ ucfirst(str_replace('_', ' ', $report->status)) }}
                    </span>
                    <span class="px-3 py-1 text-sm font-medium rounded-full {{ $report->getPriorityBadgeColor() }}">
                        {{ ucfirst($report->priority) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Report Details -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Report Details</h3>
                    </div>
                    <div class="px-6 py-4 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <p class="text-gray-900">{{ $report->description }}</p>
                        </div>

                        @if ($report->evidence && count($report->evidence) > 0)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Evidence</label>
                                <div class="space-y-2">
                                    @foreach ($report->evidence as $evidence)
                                        <div class="p-3 bg-gray-50 rounded-md">
                                            <p class="text-sm text-gray-800">{{ $evidence }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Reporter</label>
                                <div class="flex items-center space-x-2">
                                    <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                                        <span
                                            class="text-xs font-medium text-gray-600">{{ substr($report->reporter->name, 0, 1) }}</span>
                                    </div>
                                    <span class="text-gray-900">{{ $report->reporter->name }}</span>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Reported User</label>
                                <div class="flex items-center space-x-2">
                                    <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                                        <span
                                            class="text-xs font-medium text-gray-600">{{ substr($report->reportedUser->name, 0, 1) }}</span>
                                    </div>
                                    <span class="text-gray-900">{{ $report->reportedUser->name }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reported Content -->
                @if ($report->reportable)
                    <div class="bg-white rounded-lg shadow">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Reported Content</h3>
                        </div>
                        <div class="px-6 py-4">
                            @if ($report->reportable instanceof App\Models\Artwork)
                                <div class="flex items-start space-x-4">
                                    @if ($report->reportable->thumbnail_path)
                                        <img src="{{ Storage::url($report->reportable->thumbnail_path) }}"
                                            alt="Artwork thumbnail" class="w-20 h-20 object-cover rounded-lg">
                                    @endif
                                    <div class="flex-1">
                                        <h4 class="font-medium text-gray-900">{{ $report->reportable->title }}</h4>
                                        <p class="text-sm text-gray-600 mt-1">
                                            {{ Str::limit($report->reportable->description, 100) }}</p>
                                        <a href="{{ route('artworks.show', $report->reportable) }}" target="_blank"
                                            class="text-blue-600 hover:text-blue-500 text-sm font-medium mt-2 inline-block">
                                            View Artwork →
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Actions Taken -->
                @if ($report->actions->count() > 0)
                    <div class="bg-white rounded-lg shadow">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Actions Taken</h3>
                        </div>
                        <div class="px-6 py-4">
                            <div class="space-y-4">
                                @foreach ($report->actions as $action)
                                    <div class="flex items-start space-x-3 p-4 bg-gray-50 rounded-lg">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-2 mb-1">
                                                <span
                                                    class="px-2 py-1 text-xs font-medium rounded-full {{ $action->getActionBadgeColor() }}">
                                                    {{ ucfirst(str_replace('_', ' ', $action->action_type)) }}
                                                </span>
                                                @if ($action->isActive())
                                                    <span
                                                        class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Active</span>
                                                @elseif($action->isReversed())
                                                    <span
                                                        class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">Reversed</span>
                                                @endif
                                            </div>
                                            <p class="text-sm text-gray-900 mb-1">{{ $action->reason }}</p>
                                            <p class="text-xs text-gray-500">
                                                by {{ $action->moderator->name }} •
                                                {{ $action->created_at->diffForHumans() }}
                                                @if ($action->expires_at)
                                                    • Expires {{ $action->expires_at->diffForHumans() }}
                                                @endif
                                            </p>
                                        </div>
                                        @if ($action->isActive() && !$action->isReversed())
                                            <button onclick="reverseAction({{ $action->id }})"
                                                class="text-red-600 hover:text-red-500 text-sm font-medium">
                                                Reverse
                                            </button>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Assignment -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Assignment</h3>
                    </div>
                    <div class="px-6 py-4">
                        @if ($report->assignedTo)
                            <div class="flex items-center space-x-2 mb-4">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <span
                                        class="text-xs font-medium text-blue-600">{{ substr($report->assignedTo->name, 0, 1) }}</span>
                                </div>
                                <span class="text-gray-900">{{ $report->assignedTo->name }}</span>
                            </div>
                        @else
                            <p class="text-gray-500 mb-4">Unassigned</p>
                        @endif

                        @if ($report->status === 'pending' || $report->status === 'under_review')
                            <form method="POST" action="{{ route('moderation.reports.assign', $report) }}">
                                @csrf
                                <div class="space-y-3">
                                    <select name="moderator_id" required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">Select Moderator</option>
                                        @foreach (App\Models\User::where('role', 'admin')->orWhere('role', 'moderator')->get() as $moderator)
                                            <option value="{{ $moderator->id }}"
                                                {{ $report->assigned_to == $moderator->id ? 'selected' : '' }}>
                                                {{ $moderator->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="submit"
                                        class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        {{ $report->assignedTo ? 'Reassign' : 'Assign' }}
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>

                <!-- Take Action -->
                @if ($report->status !== 'resolved' && $report->status !== 'dismissed')
                    <div class="bg-white rounded-lg shadow">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Take Action</h3>
                        </div>
                        <div class="px-6 py-4">
                            <form method="POST" action="{{ route('moderation.reports.action', $report) }}"
                                class="space-y-4">
                                @csrf
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Action Type</label>
                                    <select name="action_type" required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">Select Action</option>
                                        <option value="warning">Warning</option>
                                        <option value="hide_content">Hide Content</option>
                                        <option value="remove_content">Remove Content</option>
                                        <option value="suspend">Suspend User</option>
                                        <option value="ban">Ban User</option>
                                        <option value="copyright_takedown">Copyright Takedown</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Reason</label>
                                    <textarea name="reason" required rows="3"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        placeholder="Explanation for this action..."></textarea>
                                </div>

                                <div>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="is_permanent" value="1"
                                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span class="ml-2 text-sm text-gray-700">Permanent action</span>
                                    </label>
                                </div>

                                <div id="duration-section" class="hidden">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Duration (hours)</label>
                                    <input type="number" name="duration_hours" min="1" max="8760"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>

                                <button type="submit"
                                    class="w-full px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                                    Apply Action
                                </button>
                            </form>

                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <form method="POST" action="{{ route('moderation.reports.dismiss', $report) }}">
                                    @csrf
                                    <div class="space-y-3">
                                        <input type="text" name="reason" placeholder="Reason for dismissal..."
                                            required
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <button type="submit"
                                            class="w-full px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                            Dismiss Report
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const permanentCheckbox = document.querySelector('input[name="is_permanent"]');
            const durationSection = document.getElementById('duration-section');

            if (permanentCheckbox) {
                permanentCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        durationSection.classList.add('hidden');
                    } else {
                        durationSection.classList.remove('hidden');
                    }
                });
            }
        });

        function reverseAction(actionId) {
            const reason = prompt('Please provide a reason for reversing this action:');
            if (reason) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/moderation/actions/${actionId}/reverse`;

                form.innerHTML = `
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="reason" value="${reason}">
        `;

                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
@endsection
