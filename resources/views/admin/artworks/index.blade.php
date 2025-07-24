@extends('layouts.admin')

@section('title', 'Artworks Management')
@section('subtitle', 'Manage artworks, categories, and content moderation')

@section('content')
    <!-- Search and Filters -->
    <div class="admin-stats-card mb-6">
        <form method="GET" action="{{ route('admin.artworks') }}" class="space-y-4 md:space-y-0 md:flex md:items-end md:space-x-4">
            <!-- Search -->
            <div class="flex-1">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search Artworks</label>
                <input type="text" 
                       id="search" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Search by title, description, or artist name..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <!-- Category Filter -->
            <div>
                <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <select id="category" 
                        name="category"
                        class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Categories</option>
                    <option value="Digital Art" {{ request('category') === 'Digital Art' ? 'selected' : '' }}>Digital Art</option>
                    <option value="Traditional" {{ request('category') === 'Traditional' ? 'selected' : '' }}>Traditional</option>
                    <option value="Photography" {{ request('category') === 'Photography' ? 'selected' : '' }}>Photography</option>
                    <option value="3D Art" {{ request('category') === '3D Art' ? 'selected' : '' }}>3D Art</option>
                    <option value="Logo Design" {{ request('category') === 'Logo Design' ? 'selected' : '' }}>Logo Design</option>
                </select>
            </div>

            <!-- Submit -->
            <div>
                <button type="submit" 
                        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                    Search
                </button>
                @if(request()->hasAny(['search', 'category']))
                    <a href="{{ route('admin.artworks') }}" 
                       class="ml-2 text-gray-600 hover:text-gray-900 text-sm">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Artworks Table -->
    <div class="admin-stats-card">
        <div class="mb-4 flex justify-between items-center">
            <h3 class="text-lg font-medium text-gray-900">
                Artworks ({{ $artworks->total() }} total)
            </h3>
        </div>

        @if($artworks->count() > 0)
            <div class="overflow-x-auto">
                <table class="admin-table">
                    <thead class="bg-gray-50">
                        <tr>
                            <th>Artwork</th>
                            <th>Artist</th>
                            <th>Category</th>
                            <th>ACQ Score</th>
                            <th>Evaluations</th>
                            <th>Views</th>
                            <th>Uploaded</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($artworks as $artwork)
                            <tr>
                                <!-- Artwork Info -->
                                <td>
                                    <div class="flex items-center space-x-3">
                                        <div class="w-16 h-16 bg-gray-200 rounded-lg overflow-hidden flex-shrink-0">
                                            @if($artwork->isImage())
                                                <img src="{{ $artwork->getFileUrl() }}" alt="{{ $artwork->getTitle() }}" 
                                                     class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center">
                                                    <span class="text-gray-400">📄</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $artwork->getTitle() }}</div>
                                            <div class="text-sm text-gray-500">
                                                {{ Str::limit($artwork->getDescription(), 50) }}
                                            </div>
                                            <div class="text-xs text-gray-400">ID: {{ $artwork->id }}</div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Artist -->
                                <td>
                                    <div class="flex items-center space-x-2">
                                        <img src="{{ $artwork->user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($artwork->user->name) }}" 
                                             alt="{{ $artwork->user->name }}" 
                                             class="w-8 h-8 rounded-full">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $artwork->user->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $artwork->user->email }}</div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Category -->
                                <td>
                                    @if($artwork->category)
                                        <span class="admin-badge admin-badge-info">{{ $artwork->category }}</span>
                                    @else
                                        <span class="text-gray-400 text-sm">Uncategorized</span>
                                    @endif
                                </td>

                                <!-- ACQ Score -->
                                <td>
                                    @if($artwork->acq_score)
                                        <span class="admin-badge admin-badge-success">
                                            {{ number_format($artwork->acq_score, 1) }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-sm">Not rated</span>
                                    @endif
                                </td>

                                <!-- Evaluations Count -->
                                <td>
                                    <span class="text-sm text-gray-900">{{ $artwork->evaluations_count }}</span>
                                </td>

                                <!-- Views -->
                                <td>
                                    <span class="text-sm text-gray-900">{{ number_format($artwork->views ?? 0) }}</span>
                                </td>

                                <!-- Upload Date -->
                                <td>
                                    <span class="text-sm text-gray-500">{{ $artwork->created_at->format('M j, Y') }}</span>
                                </td>

                                <!-- Actions -->
                                <td>
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('artworks.show', $artwork) }}" 
                                           class="text-blue-600 hover:text-blue-700 text-sm"
                                           target="_blank">
                                            View
                                        </a>
                                        
                                        <button class="text-orange-600 hover:text-orange-700 text-sm"
                                                onclick="moderateArtwork({{ $artwork->id }}, '{{ $artwork->getTitle() }}')">
                                            Moderate
                                        </button>
                                        
                                        <button class="text-red-600 hover:text-red-700 text-sm"
                                                onclick="confirmDelete({{ $artwork->id }}, '{{ $artwork->getTitle() }}')">
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $artworks->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-8">
                <div class="text-gray-400 text-6xl mb-4">🎨</div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No artworks found</h3>
                <p class="text-gray-500">
                    @if(request()->hasAny(['search', 'category']))
                        Try adjusting your search criteria.
                    @else
                        No artworks have been uploaded yet.
                    @endif
                </p>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
<script>
function moderateArtwork(artworkId, artworkTitle) {
    alert(`Moderation feature for "${artworkTitle}" will be implemented soon.`);
}

function confirmDelete(artworkId, artworkTitle) {
    if (confirm(`Are you sure you want to delete artwork "${artworkTitle}"? This action cannot be undone.`)) {
        // Create and submit delete form
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/artworks/${artworkId}`;
        
        // Add CSRF token
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        form.appendChild(csrfInput);
        
        // Add method spoofing
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush
