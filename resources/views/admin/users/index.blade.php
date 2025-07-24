@extends('layouts.admin')

@section('title', 'Users Management')
@section('subtitle', 'Manage user accounts, roles, and permissions')

@section('content')
    <!-- Search and Filters -->
    <div class="admin-stats-card mb-6">
        <form method="GET" action="{{ route('admin.users') }}" class="space-y-4 md:space-y-0 md:flex md:items-end md:space-x-4">
            <!-- Search -->
            <div class="flex-1">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search Users</label>
                <input type="text" 
                       id="search" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Search by name or email..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <!-- Role Filter -->
            <div>
                <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                <select id="role" 
                        name="role"
                        class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Roles</option>
                    <option value="artist" {{ request('role') === 'artist' ? 'selected' : '' }}>Artist</option>
                    <option value="moderator" {{ request('role') === 'moderator' ? 'selected' : '' }}>Moderator</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="status" 
                        name="status"
                        class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="blocked" {{ request('status') === 'blocked' ? 'selected' : '' }}>Blocked</option>
                </select>
            </div>

            <!-- Submit -->
            <div>
                <button type="submit" 
                        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                    Search
                </button>
                @if(request()->hasAny(['search', 'role', 'status']))
                    <a href="{{ route('admin.users') }}" 
                       class="ml-2 text-gray-600 hover:text-gray-900 text-sm">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="admin-stats-card">
        <div class="mb-4 flex justify-between items-center">
            <h3 class="text-lg font-medium text-gray-900">
                Users ({{ $users->total() }} total)
            </h3>
        </div>

        @if($users->count() > 0)
            <div class="overflow-x-auto">
                <table class="admin-table">
                    <thead class="bg-gray-50">
                        <tr>
                            <th>User</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Artworks</th>
                            <th>Evaluations</th>
                            <th>Joined</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($users as $user)
                            <tr>
                                <!-- User Info -->
                                <td>
                                    <div class="flex items-center space-x-3">
                                        <img src="{{ $user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) }}" 
                                             alt="{{ $user->name }}" 
                                             class="w-10 h-10 rounded-full">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                            <div class="text-sm text-gray-500">ID: {{ $user->id }}</div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Email -->
                                <td>
                                    <div class="text-sm text-gray-900">{{ $user->email }}</div>
                                    @if($user->email_verified_at)
                                        <div class="text-xs text-green-600">✓ Verified</div>
                                    @else
                                        <div class="text-xs text-orange-600">⚠ Unverified</div>
                                    @endif
                                </td>

                                <!-- Role -->
                                <td>
                                    <span class="admin-badge {{ $user->role === 'admin' ? 'admin-badge-danger' : ($user->role === 'moderator' ? 'admin-badge-warning' : 'admin-badge-info') }}">
                                        {{ ucfirst($user->role ?? 'artist') }}
                                    </span>
                                </td>

                                <!-- Artworks Count -->
                                <td>
                                    <span class="text-sm text-gray-900">{{ $user->artworks_count }}</span>
                                </td>

                                <!-- Evaluations Count -->
                                <td>
                                    <span class="text-sm text-gray-900">{{ $user->evaluations_count }}</span>
                                </td>

                                <!-- Join Date -->
                                <td>
                                    <span class="text-sm text-gray-500">{{ $user->created_at->format('M j, Y') }}</span>
                                </td>

                                <!-- Status -->
                                <td>
                                    @if($user->deleted_at)
                                        <span class="admin-badge admin-badge-danger">Blocked</span>
                                    @else
                                        <span class="admin-badge admin-badge-success">Active</span>
                                    @endif
                                </td>

                                <!-- Actions -->
                                <td>
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('users.show', $user) }}" 
                                           class="text-blue-600 hover:text-blue-700 text-sm"
                                           target="_blank">
                                            View
                                        </a>
                                        
                                        @if(Auth::id() !== $user->id)
                                            @if(!$user->deleted_at)
                                                <button class="text-red-600 hover:text-red-700 text-sm"
                                                        onclick="confirmAction('block', {{ $user->id }}, '{{ $user->name }}')">
                                                    Block
                                                </button>
                                            @else
                                                <button class="text-green-600 hover:text-green-700 text-sm"
                                                        onclick="confirmAction('unblock', {{ $user->id }}, '{{ $user->name }}')">
                                                    Unblock
                                                </button>
                                            @endif
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
                {{ $users->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-8">
                <div class="text-gray-400 text-6xl mb-4">👥</div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No users found</h3>
                <p class="text-gray-500">
                    @if(request()->hasAny(['search', 'role', 'status']))
                        Try adjusting your search criteria.
                    @else
                        No users have registered yet.
                    @endif
                </p>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
<script>
function confirmAction(action, userId, userName) {
    const actionText = action === 'block' ? 'block' : 'unblock';
    const message = `Are you sure you want to ${actionText} user "${userName}"?`;
    
    if (confirm(message)) {
        // Create and submit form
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/users/${userId}/${action}`;
        
        // Add CSRF token
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        form.appendChild(csrfInput);
        
        // Add method spoofing if needed
        if (action === 'block') {
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            form.appendChild(methodInput);
        }
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush
