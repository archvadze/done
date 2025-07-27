@extends('layouts.app')

@section('title', 'Moderation Reports')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Moderation Reports</h1>
            <p class="text-gray-600">Review and manage user-submitted reports</p>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="px-6 py-4">
                <form method="GET" action="{{ route('moderation.reports.index') }}"
                    class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" id="status"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="under_review" {{ request('status') === 'under_review' ? 'selected' : '' }}>Under
                                Review</option>
                            <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved
                            </option>
                            <option value="dismissed" {{ request('status') === 'dismissed' ? 'selected' : '' }}>Dismissed
                            </option>
                        </select>
                    </div>

                    <div>
                        <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                        <select name="priority" id="priority"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All Priorities</option>
                            <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
                            <option value="normal" {{ request('priority') === 'normal' ? 'selected' : '' }}>Normal</option>
                            <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                            <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                        </select>
                    </div>

                    <div>
                        <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-1">Assigned To</label>
                        <select name="assigned_to" id="assigned_to"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All Assignments</option>
                            <option value="unassigned" {{ request('assigned_to') === 'unassigned' ? 'selected' : '' }}>
                                Unassigned</option>
                            @foreach ($moderators as $moderator)
                                <option value="{{ $moderator->id }}"
                                    {{ request('assigned_to') == $moderator->id ? 'selected' : '' }}>
                                    {{ $moderator->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="reason" class="block text-sm font-medium text-gray-700 mb-1">Reason</label>
                        <select name="reason" id="reason"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All Reasons</option>
                            <option value="spam" {{ request('reason') === 'spam' ? 'selected' : '' }}>Spam</option>
                            <option value="harassment" {{ request('reason') === 'harassment' ? 'selected' : '' }}>
                                Harassment</option>
                            <option value="inappropriate_content"
                                {{ request('reason') === 'inappropriate_content' ? 'selected' : '' }}>Inappropriate Content
                            </option>
                            <option value="copyright_violation"
                                {{ request('reason') === 'copyright_violation' ? 'selected' : '' }}>Copyright Violation
                            </option>
                            <option value="hate_speech" {{ request('reason') === 'hate_speech' ? 'selected' : '' }}>Hate
                                Speech</option>
                            <option value="violence" {{ request('reason') === 'violence' ? 'selected' : '' }}>Violence
                            </option>
                            <option value="other" {{ request('reason') === 'other' ? 'selected' : '' }}>Other</option>
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

        <!-- Bulk Actions -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="px-6 py-4">
                <form id="bulk-action-form" method="POST" action="{{ route('moderation.reports.bulk') }}">
                    @csrf
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center">
                            <input type="checkbox" id="select-all"
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <label for="select-all" class="ml-2 text-sm text-gray-700">Select All</label>
                        </div>

                        <select name="action" required
                            class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Bulk Action</option>
                            <option value="assign">Assign to Moderator</option>
                            <option value="dismiss">Dismiss Reports</option>
                            <option value="change_priority">Change Priority</option>
                        </select>

                        <select name="moderator_id"
                            class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 hidden"
                            id="moderator-select">
                            <option value="">Select Moderator</option>
                            @foreach ($moderators as $moderator)
                                <option value="{{ $moderator->id }}">{{ $moderator->name }}</option>
                            @endforeach
                        </select>

                        <select name="priority"
                            class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 hidden"
                            id="priority-select">
                            <option value="">Select Priority</option>
                            <option value="low">Low</option>
                            <option value="normal">Normal</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>

                        <input type="text" name="reason" placeholder="Dismiss reason"
                            class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 hidden"
                            id="reason-input">

                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Apply
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Reports Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Report</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Priority</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Assigned To</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Created</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($reports as $report)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="checkbox" name="report_ids[]" value="{{ $report->id }}"
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 report-checkbox">
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-2 mb-1">
                                                <span
                                                    class="text-sm font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $report->reason)) }}</span>
                                            </div>
                                            <p class="text-sm text-gray-600 mb-1">
                                                {{ Str::limit($report->description, 80) }}</p>
                                            <p class="text-xs text-gray-500">
                                                Reported by <span class="font-medium">{{ $report->reporter->name }}</span>
                                                against <span class="font-medium">{{ $report->reportedUser->name }}</span>
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 py-1 text-xs font-medium rounded-full {{ $report->getStatusBadgeColor() }}">
                                        {{ ucfirst(str_replace('_', ' ', $report->status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 py-1 text-xs font-medium rounded-full {{ $report->getPriorityBadgeColor() }}">
                                        {{ ucfirst($report->priority) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $report->assignedTo ? $report->assignedTo->name : 'Unassigned' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $report->created_at->format('M j, Y g:i A') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('moderation.reports.show', $report) }}"
                                        class="text-blue-600 hover:text-blue-900">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($reports->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $reports->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAll = document.getElementById('select-all');
            const reportCheckboxes = document.querySelectorAll('.report-checkbox');
            const actionSelect = document.querySelector('select[name="action"]');
            const moderatorSelect = document.getElementById('moderator-select');
            const prioritySelect = document.getElementById('priority-select');
            const reasonInput = document.getElementById('reason-input');

            // Select all functionality
            selectAll.addEventListener('change', function() {
                reportCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
            });

            // Show/hide additional fields based on action
            actionSelect.addEventListener('change', function() {
                const action = this.value;

                // Hide all additional fields
                moderatorSelect.classList.add('hidden');
                prioritySelect.classList.add('hidden');
                reasonInput.classList.add('hidden');

                // Show relevant field
                if (action === 'assign') {
                    moderatorSelect.classList.remove('hidden');
                    moderatorSelect.required = true;
                } else if (action === 'change_priority') {
                    prioritySelect.classList.remove('hidden');
                    prioritySelect.required = true;
                } else if (action === 'dismiss') {
                    reasonInput.classList.remove('hidden');
                    reasonInput.required = true;
                }
            });

            // Form validation
            document.getElementById('bulk-action-form').addEventListener('submit', function(e) {
                const checkedBoxes = document.querySelectorAll('.report-checkbox:checked');
                if (checkedBoxes.length === 0) {
                    e.preventDefault();
                    alert('Please select at least one report.');
                    return;
                }

                if (!actionSelect.value) {
                    e.preventDefault();
                    alert('Please select an action.');
                    return;
                }
            });
        });
    </script>
@endsection
