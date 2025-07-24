@extends('layouts.admin')

@section('title', 'System Logs')
@section('subtitle', 'Monitor system activity and debug information')

@section('content')
    <div class="space-y-6">
        <!-- Log Filters -->
        <div class="admin-stats-card">
            <div class="flex flex-wrap items-center gap-4">
                <div>
                    <label for="log-level" class="block text-sm font-medium text-gray-700 mb-1">Log Level</label>
                    <select id="log-level"
                        class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Levels</option>
                        <option value="debug">Debug</option>
                        <option value="info">Info</option>
                        <option value="warning">Warning</option>
                        <option value="error">Error</option>
                        <option value="critical">Critical</option>
                    </select>
                </div>

                <div>
                    <label for="log-category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select id="log-category"
                        class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Categories</option>
                        <option value="auth">Authentication</option>
                        <option value="evaluation">Evaluations</option>
                        <option value="upload">File Uploads</option>
                        <option value="user">User Actions</option>
                        <option value="system">System</option>
                    </select>
                </div>

                <div>
                    <label for="log-date" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                    <input type="date" id="log-date" value="{{ date('Y-m-d') }}"
                        class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div class="pt-6">
                    <button type="button"
                        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
                        onclick="filterLogs()">
                        Filter Logs
                    </button>
                </div>
            </div>
        </div>

        <!-- System Status -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="admin-stats-card">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                            <span class="text-green-600 text-xl">🟢</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <dt class="text-sm font-medium text-gray-500 truncate">System Status</dt>
                        <dd class="text-lg font-semibold text-green-600">Online</dd>
                    </div>
                </div>
            </div>

            <div class="admin-stats-card">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                            <span class="text-blue-600 text-xl">💾</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <dt class="text-sm font-medium text-gray-500 truncate">Database</dt>
                        <dd class="text-lg font-semibold text-green-600">Connected</dd>
                    </div>
                </div>
            </div>

            <div class="admin-stats-card">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                            <span class="text-orange-600 text-xl">📊</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <dt class="text-sm font-medium text-gray-500 truncate">Memory Usage</dt>
                        <dd class="text-lg font-semibold text-gray-900">
                            {{ round(memory_get_usage(true) / 1024 / 1024, 1) }}MB</dd>
                    </div>
                </div>
            </div>

            <div class="admin-stats-card">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                            <span class="text-purple-600 text-xl">⏱️</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <dt class="text-sm font-medium text-gray-500 truncate">Uptime</dt>
                        <dd class="text-lg font-semibold text-gray-900">{{ app()->version() }}</dd>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Logs -->
        <div class="admin-stats-card">
            <div class="mb-4 flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">Recent System Logs</h3>
                <button type="button" class="text-sm text-blue-600 hover:text-blue-700" onclick="refreshLogs()">
                    Refresh
                </button>
            </div>

            <div class="space-y-2" id="logs-container">
                <!-- Sample logs -->
                <div class="log-entry border-l-4 border-green-400 bg-green-50 p-3 rounded-r">
                    <div class="flex justify-between items-start">
                        <div>
                            <span class="admin-badge admin-badge-success">INFO</span>
                            <span class="text-sm text-gray-900 ml-2">User authentication successful</span>
                        </div>
                        <span class="text-xs text-gray-500">{{ now()->format('H:i:s') }}</span>
                    </div>
                    <div class="text-xs text-gray-600 mt-1">User ID: {{ Auth::id() }}, IP: 127.0.0.1</div>
                </div>

                <div class="log-entry border-l-4 border-blue-400 bg-blue-50 p-3 rounded-r">
                    <div class="flex justify-between items-start">
                        <div>
                            <span class="admin-badge admin-badge-info">DEBUG</span>
                            <span class="text-sm text-gray-900 ml-2">Admin panel accessed</span>
                        </div>
                        <span class="text-xs text-gray-500">{{ now()->subMinutes(2)->format('H:i:s') }}</span>
                    </div>
                    <div class="text-xs text-gray-600 mt-1">Route: admin.dashboard, User: {{ Auth::user()->name }}</div>
                </div>

                <div class="log-entry border-l-4 border-yellow-400 bg-yellow-50 p-3 rounded-r">
                    <div class="flex justify-between items-start">
                        <div>
                            <span class="admin-badge admin-badge-warning">WARNING</span>
                            <span class="text-sm text-gray-900 ml-2">High memory usage detected</span>
                        </div>
                        <span class="text-xs text-gray-500">{{ now()->subMinutes(5)->format('H:i:s') }}</span>
                    </div>
                    <div class="text-xs text-gray-600 mt-1">Memory: {{ round(memory_get_usage(true) / 1024 / 1024, 1) }}MB /
                        128MB</div>
                </div>

                <div class="log-entry border-l-4 border-green-400 bg-green-50 p-3 rounded-r">
                    <div class="flex justify-between items-start">
                        <div>
                            <span class="admin-badge admin-badge-success">INFO</span>
                            <span class="text-sm text-gray-900 ml-2">Evaluation submitted successfully</span>
                        </div>
                        <span class="text-xs text-gray-500">{{ now()->subMinutes(10)->format('H:i:s') }}</span>
                    </div>
                    <div class="text-xs text-gray-600 mt-1">Artwork ID: 30, Evaluator: Bob Creator, Score: 7.5</div>
                </div>

                <div class="log-entry border-l-4 border-blue-400 bg-blue-50 p-3 rounded-r">
                    <div class="flex justify-between items-start">
                        <div>
                            <span class="admin-badge admin-badge-info">INFO</span>
                            <span class="text-sm text-gray-900 ml-2">Artwork uploaded</span>
                        </div>
                        <span class="text-xs text-gray-500">{{ now()->subHour()->format('H:i:s') }}</span>
                    </div>
                    <div class="text-xs text-gray-600 mt-1">User: Alice Artist, File: logo.png, Size: 2.3MB</div>
                </div>
            </div>

            <!-- Load More -->
            <div class="mt-4 text-center">
                <button type="button" class="text-sm text-blue-600 hover:text-blue-700" onclick="loadMoreLogs()">
                    Load More Logs
                </button>
            </div>
        </div>

        <!-- Error Summary -->
        <div class="admin-stats-card">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Error Summary (Last 24 Hours)</h3>

            <div class="space-y-3">
                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                    <span class="text-sm font-medium text-gray-900">Critical Errors</span>
                    <span class="admin-badge admin-badge-danger">0</span>
                </div>

                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                    <span class="text-sm font-medium text-gray-900">Errors</span>
                    <span class="admin-badge admin-badge-warning">2</span>
                </div>

                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                    <span class="text-sm font-medium text-gray-900">Warnings</span>
                    <span class="admin-badge admin-badge-info">5</span>
                </div>

                <div class="flex justify-between items-center py-2">
                    <span class="text-sm font-medium text-gray-900">Info Messages</span>
                    <span class="admin-badge admin-badge-success">142</span>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function filterLogs() {
            alert('Log filtering functionality will be implemented soon.');
        }

        function refreshLogs() {
            alert('Log refresh functionality will be implemented soon.');
        }

        function loadMoreLogs() {
            alert('Load more logs functionality will be implemented soon.');
        }
    </script>
@endpush
