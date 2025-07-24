@extends('layouts.admin')

@section('title', 'Dashboard')
@section('subtitle', 'Overview of your platform statistics and recent activity')

@section('content')
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Total Users -->
        <div class="admin-stats-card">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <span class="text-blue-600 text-xl">👥</span>
                    </div>
                </div>
                <div class="ml-4">
                    <dt class="text-sm font-medium text-gray-500 truncate">Total Users</dt>
                    <dd class="text-3xl font-semibold text-gray-900">{{ number_format($stats['total_users']) }}</dd>
                </div>
            </div>
        </div>

        <!-- Total Artworks -->
        <div class="admin-stats-card">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <span class="text-green-600 text-xl">🎨</span>
                    </div>
                </div>
                <div class="ml-4">
                    <dt class="text-sm font-medium text-gray-500 truncate">Total Artworks</dt>
                    <dd class="text-3xl font-semibold text-gray-900">{{ number_format($stats['total_artworks']) }}</dd>
                </div>
            </div>
        </div>

        <!-- Total Evaluations -->
        <div class="admin-stats-card">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <span class="text-yellow-600 text-xl">⭐</span>
                    </div>
                </div>
                <div class="ml-4">
                    <dt class="text-sm font-medium text-gray-500 truncate">Total Evaluations</dt>
                    <dd class="text-3xl font-semibold text-gray-900">{{ number_format($stats['total_evaluations']) }}</dd>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Users -->
        <div class="admin-stats-card">
            <div class="mb-4">
                <h3 class="text-lg font-medium text-gray-900">Recent Users</h3>
                <p class="text-sm text-gray-500">Latest registered users</p>
            </div>
            
            @if($stats['recent_users']->count() > 0)
                <div class="space-y-3">
                    @foreach($stats['recent_users'] as $user)
                        <div class="flex items-center space-x-3">
                            <img src="{{ $user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) }}" 
                                 alt="{{ $user->name }}" 
                                 class="w-8 h-8 rounded-full">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $user->name }}</p>
                                <p class="text-sm text-gray-500 truncate">{{ $user->email }}</p>
                            </div>
                            <div class="flex-shrink-0">
                                <span class="admin-badge admin-badge-info">{{ ucfirst($user->role ?? 'artist') }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <a href="{{ route('admin.users') }}" class="text-sm text-blue-600 hover:text-blue-700">
                        View all users →
                    </a>
                </div>
            @else
                <p class="text-gray-500 text-sm">No users registered yet.</p>
            @endif
        </div>

        <!-- Recent Artworks -->
        <div class="admin-stats-card">
            <div class="mb-4">
                <h3 class="text-lg font-medium text-gray-900">Recent Artworks</h3>
                <p class="text-sm text-gray-500">Latest uploaded artworks</p>
            </div>
            
            @if($stats['recent_artworks']->count() > 0)
                <div class="space-y-3">
                    @foreach($stats['recent_artworks'] as $artwork)
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-gray-200 rounded-lg overflow-hidden flex-shrink-0">
                                @if($artwork->isImage())
                                    <img src="{{ $artwork->getFileUrl() }}" alt="{{ $artwork->getTitle() }}" 
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <span class="text-gray-400 text-xs">📄</span>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $artwork->getTitle() }}</p>
                                <p class="text-sm text-gray-500 truncate">by {{ $artwork->user->name }}</p>
                            </div>
                            <div class="flex-shrink-0">
                                @if($artwork->category)
                                    <span class="admin-badge admin-badge-success">{{ $artwork->category }}</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <a href="{{ route('admin.artworks') }}" class="text-sm text-blue-600 hover:text-blue-700">
                        View all artworks →
                    </a>
                </div>
            @else
                <p class="text-gray-500 text-sm">No artworks uploaded yet.</p>
            @endif
        </div>
    </div>

    <!-- Recent Evaluations -->
    @if($stats['recent_evaluations']->count() > 0)
        <div class="mt-8">
            <div class="admin-stats-card">
                <div class="mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Recent Evaluations</h3>
                    <p class="text-sm text-gray-500">Latest artwork evaluations</p>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="admin-table">
                        <thead class="bg-gray-50">
                            <tr>
                                <th>Artwork</th>
                                <th>Evaluator</th>
                                <th>Score</th>
                                <th>Source</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($stats['recent_evaluations'] as $evaluation)
                                <tr>
                                    <td>
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $evaluation->artwork->getTitle() }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            by {{ $evaluation->artwork->user->name }}
                                        </div>
                                    </td>
                                    <td>
                                        @if($evaluation->evaluator)
                                            <div class="text-sm text-gray-900">{{ $evaluation->evaluator->name }}</div>
                                        @else
                                            <span class="text-sm text-gray-400">System</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="admin-badge admin-badge-info">
                                            {{ number_format($evaluation->overall_score, 1) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="admin-badge {{ $evaluation->source === 'human' ? 'admin-badge-success' : 'admin-badge-warning' }}">
                                            {{ ucfirst($evaluation->source) }}
                                        </span>
                                    </td>
                                    <td class="text-sm text-gray-500">
                                        {{ $evaluation->created_at->format('M j, Y') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <a href="{{ route('admin.evaluations') }}" class="text-sm text-blue-600 hover:text-blue-700">
                        View all evaluations →
                    </a>
                </div>
            </div>
        </div>
    @endif
@endsection
