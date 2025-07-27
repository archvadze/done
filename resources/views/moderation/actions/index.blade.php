@extends('layouts.app')

@section('title', 'Moderation Actions')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Moderation Actions</h1>
            <p class="text-gray-600">Review and manage all moderation actions</p>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="px-6 py-4">
                <form method="GET" action="{{ route('moderation.actions.index') }}"
                    class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="action_type" class="block text-sm font-medium text-gray-700 mb-1">Action Type</label>
                        <select name="action_type" id="action_type"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All Actions</option>
                            <option value="warning" {{ request('action_type') === 'warning' ? 'selected' : '' }}>Warning
                            </option>
                            <option value="hide_content" {{ request('action_type') === 'hide_content' ? 'selected' : '' }}>
                                Hide Content</option>
                            <option value="remove_content"
                                {{ request('action_type') === 'remove_content' ? 'selected' : '' }}>Remove Content</option>
                            <option value="suspend" {{ request('action_type') === 'suspend' ? 'selected' : '' }}>Suspend
                            </option>
                            <option value="ban" {{ request('action_type') === 'ban' ? 'selected' : '' }}>Ban</option>
                            <option value="copyright_takedown"
                                {{ request('action_type') === 'copyright_takedown' ? 'selected' : '' }}>Copyright Takedown
                            </option>
                        </select>
                    </div>

                    <div>
                        <label for="moderator_id" class="block text-sm font-medium text-gray-700 mb-1">Moderator</label>
                        <select name="moderator_id" id="moderator_id"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All Moderators</option>
                            @foreach ($moderators as $moderator)
                                <option value="{{ $moderator->id }}"
                                    {{ request('moderator_id') == $moderator->id ? 'selected' : '' }}>
                                    {{ $moderator->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="is_active" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="is_active" id="is_active"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All Statuses</option>
                            <option value="true" {{ request('is_active') === 'true' ? 'selected' : '' }}>Active</option>
                            <option value="false" {{ request('is_active') === 'false' ? 'selected' : '' }}>Inactive
                            </option>
                        </select>
                    </div>

                    <div class="flex items-end">
                        <button type="submit"
                            class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Actions Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Action</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Target</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Moderator</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Duration</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Created</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($actions as $action)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col">
                                        <span
                                            class="px-2 py-1 text-xs font-medium rounded-full {{ $action->getActionBadgeColor() }} mb-1">
                                            {{ ucfirst(str_replace('_', ' ', $action->action_type)) }}
                                        </span>
                                        <p class="text-sm text-gray-600">{{ Str::limit($action->reason, 50) }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                                            <span
                                                class="text-xs font-medium text-gray-600">{{ substr($action->targetUser->name, 0, 1) }}</span>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $action->targetUser->name }}
                                            </div>
                                            @if ($action->target)
                                                <div class="text-sm text-gray-500">
                                                    {{ class_basename($action->target_type) }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                            <span
                                                class="text-xs font-medium text-blue-600">{{ substr($action->moderator->name, 0, 1) }}</span>
                                        </div>
                                        <span class="text-sm text-gray-900">{{ $action->moderator->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col space-y-1">
                                        @if ($action->isActive())
                                            <span
                                                class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Active</span>
                                        @elseif($action->isReversed())
                                            <span
                                                class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">Reversed</span>
                                        @elseif($action->hasExpired())
                                            <span
                                                class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">Expired</span>
                                        @else
                                            <span
                                                class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">Inactive</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if ($action->expires_at)
                                        @if ($action->hasExpired())
                                            <span class="text-red-600">Expired</span>
                                        @else
                                            {{ $action->getTimeRemaining() }}
                                        @endif
                                    @else
                                        <span class="text-gray-500">Permanent</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $action->created_at->format('M j, Y g:i A') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        @if ($action->report)
                                            <a href="{{ route('moderation.reports.show', $action->report) }}"
                                                class="text-blue-600 hover:text-blue-900">
                                                View Report
                                            </a>
                                        @endif
                                        @if ($action->isActive() && !$action->isReversed())
                                            <button onclick="reverseAction({{ $action->id }})"
                                                class="text-red-600 hover:text-red-900">
                                                Reverse
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($actions->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $actions->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Reverse Action Modal -->
    <div id="reverse-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Reverse Action</h3>
                <form id="reverse-form" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Reason for reversal</label>
                        <textarea name="reason" required rows="3"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Please explain why this action is being reversed..."></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeReverseModal()"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                            Reverse Action
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function reverseAction(actionId) {
            const modal = document.getElementById('reverse-modal');
            const form = document.getElementById('reverse-form');

            form.action = `/moderation/actions/${actionId}/reverse`;
            modal.classList.remove('hidden');
        }

        function closeReverseModal() {
            const modal = document.getElementById('reverse-modal');
            modal.classList.add('hidden');

            // Clear form
            document.getElementById('reverse-form').reset();
        }

        // Close modal when clicking outside
        document.getElementById('reverse-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeReverseModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeReverseModal();
            }
        });
    </script>
@endsection
