@extends('layouts.app')

@section('title', 'Moderation Dashboard')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Moderation Dashboard</h1>
            <p class="text-gray-600">Monitor and manage platform content and user behavior</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Pending Reports</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['pending_reports'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Unassigned Reports</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['unassigned_reports'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Active Actions</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['active_actions'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
                <div class="flex items-center">
                    <div class="p-2 bg-red-100 rounded-lg">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Security Alerts (24h)</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['recent_security_events'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Recent Reports -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium text-gray-900">Recent Reports</h3>
                            <a href="{{ route('moderation.reports.index') }}"
                                class="text-blue-600 hover:text-blue-500 text-sm font-medium">View All</a>
                        </div>
                    </div>
                    <div class="px-6 py-4">
                        @if ($recent_reports->count() > 0)
                            <div class="space-y-4">
                                @foreach ($recent_reports as $report)
                                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-2">
                                                <span
                                                    class="px-2 py-1 text-xs font-medium rounded-full {{ $report->getStatusBadgeColor() }}">
                                                    {{ ucfirst($report->status) }}
                                                </span>
                                                <span
                                                    class="px-2 py-1 text-xs font-medium rounded-full {{ $report->getPriorityBadgeColor() }}">
                                                    {{ ucfirst($report->priority) }}
                                                </span>
                                            </div>
                                            <p class="text-sm font-medium text-gray-900 mt-1">{{ $report->reason }}</p>
                                            <p class="text-xs text-gray-500">
                                                Reported by {{ $report->reporter->name }} •
                                                {{ $report->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                        <a href="{{ route('moderation.reports.show', $report) }}"
                                            class="text-blue-600 hover:text-blue-500 text-sm font-medium">View</a>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-8">No recent reports</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recent Actions & Security Events -->
            <div class="space-y-8">
                <!-- Recent Actions -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium text-gray-900">Recent Actions</h3>
                            <a href="{{ route('moderation.actions.index') }}"
                                class="text-blue-600 hover:text-blue-500 text-sm font-medium">View All</a>
                        </div>
                    </div>
                    <div class="px-6 py-4">
                        @if ($recent_actions->count() > 0)
                            <div class="space-y-3">
                                @foreach ($recent_actions->take(5) as $action)
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <span
                                                class="px-2 py-1 text-xs font-medium rounded-full {{ $action->getActionBadgeColor() }}">
                                                {{ ucfirst(str_replace('_', ' ', $action->action_type)) }}
                                            </span>
                                            <p class="text-xs text-gray-500 mt-1">
                                                by {{ $action->moderator->name }} •
                                                {{ $action->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-4">No recent actions</p>
                        @endif
                    </div>
                </div>

                <!-- Security Events -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium text-gray-900">Security Events</h3>
                            <a href="{{ route('moderation.security.logs') }}"
                                class="text-blue-600 hover:text-blue-500 text-sm font-medium">View All</a>
                        </div>
                    </div>
                    <div class="px-6 py-4">
                        @if ($recent_security->count() > 0)
                            <div class="space-y-3">
                                @foreach ($recent_security->take(5) as $log)
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <span
                                                class="px-2 py-1 text-xs font-medium rounded-full {{ $log->getSeverityBadgeColor() }}">
                                                {{ ucfirst($log->severity) }}
                                            </span>
                                            <p class="text-xs text-gray-900 mt-1">{{ $log->description }}</p>
                                            <p class="text-xs text-gray-500">{{ $log->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-4">No recent security events</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
