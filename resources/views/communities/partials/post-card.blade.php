<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 {{ $post->is_pinned ? 'border-l-4 border-blue-500' : '' }}">
    <!-- Post Header -->
    <div class="flex items-start justify-between mb-4">
        <div class="flex items-start gap-3">
            @if($post->user->avatar_url)
                <img src="{{ $post->user->avatar_url }}" alt="{{ $post->user->name }}" class="w-10 h-10 rounded-full object-cover">
            @else
                <div class="w-10 h-10 bg-gray-300 dark:bg-gray-600 rounded-full flex items-center justify-center">
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">
                        {{ strtoupper(substr($post->user->name, 0, 1)) }}
                    </span>
                </div>
            @endif
            
            <div>
                <div class="flex items-center gap-2">
                    <span class="font-medium text-gray-900 dark:text-white">{{ $post->user->name }}</span>
                    @if($post->is_pinned)
                        <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 text-xs rounded">
                            <i class="fas fa-thumbtack mr-1"></i>{{ __('Pinned') }}
                        </span>
                    @endif
                    <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 text-xs rounded">
                        {{ ucfirst($post->type) }}
                    </span>
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    {{ $post->created_at->diffForHumans() }}
                </div>
            </div>
        </div>

        <!-- Post Actions -->
        @auth
            @if($community->canModerate(auth()->user()) || $post->user_id === auth()->id())
                <div class="relative group">
                    <button class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <div class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-700 rounded-md shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-10">
                        <div class="py-1">
                            @if($post->user_id === auth()->id())
                                <a href="{{ route('communities.posts.edit', [$community->slug, $post->id]) }}" 
                                   class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                    {{ __('Edit') }}
                                </a>
                            @endif
                            @if($community->canModerate(auth()->user()))
                                <form method="POST" action="{{ route('communities.posts.pin', [$community->slug, $post->id]) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                        {{ $post->is_pinned ? __('Unpin') : __('Pin') }}
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('communities.posts.lock', [$community->slug, $post->id]) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                        {{ $post->is_locked ? __('Unlock') : __('Lock') }}
                                    </button>
                                </form>
                            @endif
                            @if($post->user_id === auth()->id() || $community->canModerate(auth()->user()))
                                <form method="POST" action="{{ route('communities.posts.destroy', [$community->slug, $post->id]) }}" 
                                      onsubmit="return confirm('{{ __('Are you sure you want to delete this post?') }}')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 dark:hover:bg-gray-600">
                                        {{ __('Delete') }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        @endauth
    </div>

    <!-- Post Content -->
    <div class="mb-4">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
            <a href="{{ route('communities.posts.show', [$community->slug, $post->id]) }}" class="hover:text-blue-500">
                {{ $post->title }}
            </a>
        </h3>
        <div class="text-gray-700 dark:text-gray-300 prose dark:prose-invert max-w-none">
            {!! nl2br(e($post->content)) !!}
        </div>
    </div>

    <!-- Attachments -->
    @if($post->attachments && count($post->attachments) > 0)
        <div class="mb-4">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
                @foreach($post->attachments as $attachment)
                    @if(str_starts_with($attachment['mime_type'], 'image/'))
                        <div class="relative group">
                            <img src="{{ asset('storage/' . $attachment['path']) }}" 
                                 alt="{{ $attachment['name'] }}" 
                                 class="w-full h-32 object-cover rounded-lg">
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all duration-200 rounded-lg flex items-center justify-center">
                                <a href="{{ asset('storage/' . $attachment['path']) }}" 
                                   target="_blank"
                                   class="text-white opacity-0 group-hover:opacity-100 transition-opacity">
                                    <i class="fas fa-expand text-xl"></i>
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-lg">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-file text-gray-500"></i>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                        {{ $attachment['name'] }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ number_format($attachment['size'] / 1024, 1) }} KB
                                    </p>
                                </div>
                                <a href="{{ asset('storage/' . $attachment['path']) }}" 
                                   download="{{ $attachment['name'] }}"
                                   class="text-blue-500 hover:text-blue-600">
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    <!-- Post Stats -->
    <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
        <div class="flex items-center gap-6">
            <button onclick="likePost({{ $post->id }})" 
                    class="flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-blue-500 transition-colors">
                <i class="far fa-heart"></i>
                <span id="like-count-{{ $post->id }}">{{ $post->like_count }}</span>
            </button>
            <a href="{{ route('communities.posts.show', [$community->slug, $post->id]) }}" 
               class="flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-blue-500 transition-colors">
                <i class="far fa-comment"></i>
                <span>{{ $post->comment_count }}</span>
            </a>
            <span class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                <i class="far fa-eye"></i>
                <span>{{ $post->view_count }}</span>
            </span>
        </div>
        
        @if($post->is_locked)
            <span class="text-gray-500 dark:text-gray-400">
                <i class="fas fa-lock mr-1"></i>{{ __('Locked') }}
            </span>
        @endif
    </div>
</div>

@push('scripts')
<script>
function likePost(postId) {
    fetch(`/communities/{{ $community->slug }}/posts/${postId}/like`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById(`like-count-${postId}`).textContent = data.like_count;
        }
    })
    .catch(error => console.error('Error:', error));
}
</script>
@endpush
