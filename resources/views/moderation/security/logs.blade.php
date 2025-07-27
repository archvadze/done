@extends('layouts.app')

@section('title', 'Security Logs')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Security Logs</h1>
            <p class="text-gray-600">Monitor security events and audit trails</p>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="px-6 py-4">
                <form method="GET" action="{{ route('moderation.security.logs') }}"
                    class="grid grid-cols-1 md:grid-cols-6 gap-4">
                    <div>
                        <label for="severity" class="block text-sm font-medium text-gray-700 mb-1">Severity</label>
                        <select name="severity" id="severity"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All Severities</option>
                            <option value="info" {{ request('severity') === 'info' ? 'selected' : '' }}>Info</option>
                            <option value="warning" {{ request('severity') === 'warning' ? 'selected' : '' }}>Warning
                            </option>
                            <option value="high" {{ request('severity') === 'high' ? 'selected' : '' }}>High</option>
                            <option value="critical" {{ request('severity') === 'critical' ? 'selected' : '' }}>Critical
                            </option>
                        </select>
                    </div>

                    <div>
                        <label for="event_category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                        <select name="event_category" id="event_category"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All Categories</option>
                            <option value="authentication"
                                {{ request('event_category') === 'authentication' ? 'selected' : '' }}>Authentication
                            </option>
                            <option value="moderation" {{ request('event_category') === 'moderation' ? 'selected' : '' }}>
                                Moderation</option>
                            <option value="security" {{ request('event_category') === 'security' ? 'selected' : '' }}>
                                Security</option>
                            <option value="system" {{ request('event_category') === 'system' ? 'selected' : '' }}>System
                            </option>
                        </select>
                    </div>

                    <div>
                        <label for="event_type" class="block text-sm font-medium text-gray-700 mb-1">Event Type</label>
                        <input type="text" name="event_type" id="event_type" value="{{ request('event_type') }}"
                            placeholder="e.g., login_failed"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="ip_address" class="block text-sm font-medium text-gray-700 mb-1">IP Address</label>
                        <input type="text" name="ip_address" id="ip_address" value="{{ request('ip_address') }}"
                            placeholder="IP address"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                        <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
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

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-red-100 rounded-lg">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-600">Critical Events (24h)</p>
                        <p class="text-lg font-bold text-gray-900">
                            {{ App\Models\SecurityLog::critical()->recent(24)->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-600">Failed Logins (24h)</p>
                        <p class="text-lg font-bold text-gray-900">
                            {{ App\Models\SecurityLog::where('event_type', 'login_failed')->recent(24)->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-600">Moderation Actions (24h)</p>
                        <p class="text-lg font-bold text-gray-900">
                            {{ App\Models\SecurityLog::category('moderation')->recent(24)->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-600">Active Users (24h)</p>
                        <p class="text-lg font-bold text-gray-900">
                            {{ App\Models\SecurityLog::where('event_type', 'login_success')->recent(24)->distinct('user_id')->count('user_id') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Logs Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Timestamp</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Severity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Event</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP
                                Address</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Details</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($logs as $log)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $log->created_at->format('M j, Y g:i:s A') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 py-1 text-xs font-medium rounded-full {{ $log->getSeverityBadgeColor() }}">
                                        {{ ucfirst($log->severity) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span
                                            class="text-sm font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $log->event_type)) }}</span>
                                        <span class="text-xs text-gray-500">{{ ucfirst($log->event_category) }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($log->user)
                                        <div class="flex items-center space-x-2">
                                            <div class="w-6 h-6 bg-gray-300 rounded-full flex items-center justify-center">
                                                <span
                                                    class="text-xs font-medium text-gray-600">{{ substr($log->user->name, 0, 1) }}</span>
                                            </div>
                                            <span class="text-sm text-gray-900">{{ $log->user->name }}</span>
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-500">Guest</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $log->ip_address ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ $log->description }}</div>
                                    @if ($log->metadata && count($log->metadata) > 0)
                                        <button onclick="showMetadata({{ json_encode($log->metadata) }})"
                                            class="text-xs text-blue-600 hover:text-blue-500 mt-1">
                                            View Details
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($logs->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $logs->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Metadata Modal -->
    <div id="metadata-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-3/4 max-w-2xl shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Event Details</h3>
                    <button onclick="closeMetadataModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div id="metadata-content" class="bg-gray-50 rounded-md p-4">
                    <!-- Metadata will be displayed here -->
                </div>
            </div>
        </div>
    </div>

    <script>
        function showMetadata(metadata) {
            const modal = document.getElementById('metadata-modal');
            const content = document.getElementById('metadata-content');

            let html = '<pre class="text-sm text-gray-700 whitespace-pre-wrap">';
            html += JSON.stringify(metadata, null, 2);
            html += '</pre>';

            content.innerHTML = html;
            modal.classList.remove('hidden');
        }

        function closeMetadataModal() {
            const modal = document.getElementById('metadata-modal');
            modal.classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('metadata-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeMetadataModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeMetadataModal();
            }
        });
    </script>
@endsection
