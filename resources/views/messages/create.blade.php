@extends('layouts.app')

@section('title', __('New Message'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm p-8">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ __('Start New Conversation') }}</h1>
                <p class="text-gray-600">{{ __('Send a message to another user to start a conversation.') }}</p>
            </div>

            @if(session('success'))
                <div class="bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('messages.store') }}" class="space-y-6">
                @csrf

                <!-- Recipient -->
                <div>
                    <label for="recipient" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('Send to') }} <span class="text-red-500">*</span>
                    </label>
                    <select name="recipient_id" 
                            id="recipient" 
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('recipient_id') border-red-500 @enderror">
                        <option value="">{{ __('Select a user...') }}</option>
                        @if(isset($users))
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('recipient_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        @endif
                    </select>
                    @error('recipient_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Subject -->
                <div>
                    <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('Subject') }}
                    </label>
                    <input type="text" 
                           name="subject" 
                           id="subject" 
                           value="{{ old('subject') }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('subject') border-red-500 @enderror"
                           placeholder="{{ __('Enter message subject...') }}">
                    @error('subject')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Message -->
                <div>
                    <label for="message" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('Message') }} <span class="text-red-500">*</span>
                    </label>
                    <textarea name="content" 
                              id="message" 
                              required
                              rows="6"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('content') border-red-500 @enderror"
                              placeholder="{{ __('Type your message here...') }}">{{ old('content') }}</textarea>
                    @error('content')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-between pt-4">
                    <a href="{{ route('messages.index') }}" 
                       class="text-gray-600 hover:text-gray-800 font-medium">
                        <i class="fas fa-arrow-left mr-2"></i>{{ __('Back to Messages') }}
                    </a>
                    
                    <div class="flex space-x-3">
                        <button type="button" 
                                onclick="history.back()"
                                class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium">
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit" 
                                class="px-6 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-medium transition-colors">
                            <i class="fas fa-paper-plane mr-2"></i>{{ __('Send Message') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Quick User Search -->
        <div class="mt-6 bg-blue-50 rounded-lg p-4">
            <h3 class="text-lg font-medium text-blue-900 mb-2">{{ __('Quick User Search') }}</h3>
            <p class="text-blue-700 text-sm mb-3">{{ __('Search for users by name or email to send them a message.') }}</p>
            <div class="relative">
                <input type="text" 
                       id="user-search" 
                       placeholder="{{ __('Search users...') }}"
                       class="w-full px-4 py-2 pr-10 border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white">
                <div class="absolute right-3 top-1/2 transform -translate-y-1/2 text-blue-400">
                    <i class="fas fa-search"></i>
                </div>
            </div>
            <div id="search-results" class="mt-2 hidden">
                <!-- Search results will appear here -->
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('user-search');
    const searchResults = document.getElementById('search-results');
    const recipientSelect = document.getElementById('recipient');
    
    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        
        if (query.length < 2) {
            searchResults.classList.add('hidden');
            return;
        }
        
        // Simulate search results (in real implementation, this would be an AJAX call)
        setTimeout(() => {
            searchResults.innerHTML = `
                <div class="bg-white border border-blue-200 rounded-lg p-2 space-y-1">
                    <div class="text-sm text-blue-600 font-medium">${query.length > 0 ? 'Search results for "' + query + '"' : ''}</div>
                    <div class="text-xs text-blue-500">Search functionality will be implemented with AJAX</div>
                </div>
            `;
            searchResults.classList.remove('hidden');
        }, 300);
    });
});
</script>
@endpush
@endsection
