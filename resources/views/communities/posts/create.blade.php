@extends('layouts.app')

@section('title', 'Create Post - ' . $community->name)

@section('content')
<div class="min-h-screen bg-primary">
    <div class="max-w-4xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <nav class="text-gray-400 mb-4">
                <a href="{{ route('communities.index') }}" class="hover:text-white">Communities</a>
                <span class="mx-2">/</span>
                <a href="{{ route('communities.show', $community) }}" class="hover:text-white">{{ $community->name }}</a>
                <span class="mx-2">/</span>
                <span class="text-white">Create Post</span>
            </nav>
            
            <h1 class="text-4xl font-bold text-secondary mb-4">Create New Post</h1>
            <p class="text-white">Share with the {{ $community->name }} community</p>
        </div>

        <!-- Create Post Form -->
        <div class="bg-secondary p-8">
            <form method="POST" action="{{ route('communities.posts.store', $community) }}" enctype="multipart/form-data">
                @csrf

                <!-- Post Type -->
                <div class="mb-6">
                    <label for="type" class="block text-sm font-medium text-white mb-2">Post Type</label>
                    <select name="type" id="type" required 
                            class="w-full px-3 py-2 bg-primary text-white focus:outline-none focus:ring-2 focus:ring-secondary @error('type') border-red-500 @enderror">
                        <option value="">Select post type...</option>
                        <option value="discussion" {{ old('type') === 'discussion' ? 'selected' : '' }}>Discussion</option>
                        <option value="question" {{ old('type') === 'question' ? 'selected' : '' }}>Question</option>
                        <option value="showcase" {{ old('type') === 'showcase' ? 'selected' : '' }}>Showcase</option>
                        <option value="announcement" {{ old('type') === 'announcement' ? 'selected' : '' }}>Announcement</option>
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Title -->
                <div class="mb-6">
                    <label for="title" class="block text-sm font-medium text-white mb-2">Title</label>
                    <input type="text" 
                           name="title" 
                           id="title" 
                           value="{{ old('title') }}" 
                           required
                           maxlength="255"
                           class="w-full px-3 py-2 bg-primary text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-secondary @error('title') border-red-500 @enderror"
                           placeholder="What's your post about?">
                    @error('title')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Content -->
                <div class="mb-6">
                    <label for="content" class="block text-sm font-medium text-white mb-2">Content</label>
                    <textarea name="content" 
                              id="content" 
                              rows="8" 
                              required
                              maxlength="10000"
                              class="w-full px-3 py-2 bg-primary text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-secondary @error('content') border-red-500 @enderror"
                              placeholder="Share your thoughts, ask questions, or showcase your work...">{{ old('content') }}</textarea>
                    @error('content')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                    <div class="mt-1 text-sm text-gray-400">
                        <span id="char-count">0</span>/10,000 characters
                    </div>
                </div>

                <!-- Attachments -->
                <div class="mb-6">
                    <label for="attachments" class="block text-sm font-medium text-white mb-2">Attachments (Optional)</label>
                    <input type="file" 
                           name="attachments[]" 
                           id="attachments" 
                           multiple
                           accept="image/*,video/*,.pdf,.doc,.docx,.txt"
                           class="w-full px-3 py-2 bg-primary text-white file:mr-4 file:py-2 file:px-4 file:border-0 file:text-sm file:font-semibold file:bg-secondary file:text-white hover:file:bg-opacity-80">
                    <p class="mt-1 text-sm text-gray-400">
                        Upload images, videos, or documents. Max 10MB per file.
                    </p>
                    @error('attachments')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                    @error('attachments.*')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Guidelines -->
                <div class="mb-6 p-4 bg-primary bg-opacity-50">
                    <h3 class="text-white font-semibold mb-2">Community Guidelines</h3>
                    <ul class="text-gray-300 text-sm space-y-1">
                        <li>• Be respectful and constructive in your posts</li>
                        <li>• Stay on topic for this community</li>
                        <li>• Use appropriate post types (discussion, question, showcase, announcement)</li>
                        <li>• Avoid spam and duplicate content</li>
                        <li>• Credit other artists and sources when applicable</li>
                    </ul>
                </div>

                <!-- Actions -->
                <div class="flex justify-between items-center">
                    <a href="{{ route('communities.show', $community) }}" 
                       class="px-6 py-2 bg-gray-600 text-white hover:bg-gray-700 transition-colors">
                        Cancel
                    </a>
                    
                    <button type="submit" class="btn-primary px-8 py-2">
                        Create Post
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Character counter
document.getElementById('content').addEventListener('input', function() {
    const charCount = this.value.length;
    document.getElementById('char-count').textContent = charCount;
    
    if (charCount > 9500) {
        document.getElementById('char-count').classList.add('text-yellow-400');
    } else {
        document.getElementById('char-count').classList.remove('text-yellow-400');
    }
    
    if (charCount > 10000) {
        document.getElementById('char-count').classList.add('text-red-400');
        document.getElementById('char-count').classList.remove('text-yellow-400');
    } else {
        document.getElementById('char-count').classList.remove('text-red-400');
    }
});

// Preview selected files
document.getElementById('attachments').addEventListener('change', function() {
    const files = Array.from(this.files);
    if (files.length > 0) {
        console.log('Selected files:', files.map(f => f.name));
    }
});
</script>
@endsection
