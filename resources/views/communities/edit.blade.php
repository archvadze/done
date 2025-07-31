@extends('layouts.app')

@section('title', __('Edit Community') . ' - ' . $community->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-8">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">{{ __('Edit Community') }}</h1>

            <form method="POST" action="{{ route('communities.update', $community) }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PATCH')

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('Community Name') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="name" 
                           id="name" 
                           value="{{ old('name', $community->name) }}" 
                           required
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('Description') }} <span class="text-red-500">*</span>
                    </label>
                    <textarea name="description" 
                              id="description" 
                              rows="4" 
                              required
                              placeholder="{{ __('Describe what your community is about...') }}"
                              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('description') border-red-500 @enderror">{{ old('description', $community->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Privacy -->
                <div>
                    <label for="privacy" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('Privacy') }} <span class="text-red-500">*</span>
                    </label>
                    <select name="privacy" 
                            id="privacy" 
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('privacy') border-red-500 @enderror">
                        <option value="public" {{ old('privacy', $community->privacy) === 'public' ? 'selected' : '' }}>
                            {{ __('Public - Anyone can view and join') }}
                        </option>
                        <option value="private" {{ old('privacy', $community->privacy) === 'private' ? 'selected' : '' }}>
                            {{ __('Private - Members can view, joining requires approval') }}
                        </option>
                        <option value="hidden" {{ old('privacy', $community->privacy) === 'hidden' ? 'selected' : '' }}>
                            {{ __('Hidden - Only moderators can view and invite') }}
                        </option>
                    </select>
                    @error('privacy')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Requires Approval -->
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="requires_approval" 
                               value="1" 
                               {{ old('requires_approval', $community->requires_approval) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                            {{ __('Require approval to join (for public communities)') }}
                        </span>
                    </label>
                </div>

                <!-- Current Cover Image -->
                @if($community->cover_image)
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('Current Cover Image') }}
                    </label>
                    <div class="mb-2">
                        <img src="{{ Storage::url($community->cover_image) }}" alt="{{ $community->name }} cover" class="w-full h-32 object-cover rounded-lg">
                    </div>
                </div>
                @endif

                <!-- Cover Image -->
                <div>
                    <label for="cover_image" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('Cover Image') }} @if($community->cover_image) <span class="text-sm text-gray-500">({{ __('Upload new to replace') }})</span> @endif
                    </label>
                    <input type="file" 
                           name="cover_image" 
                           id="cover_image" 
                           accept="image/*"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('cover_image') border-red-500 @enderror">
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('Max 2MB. Recommended size: 1200x400px') }}</p>
                    @error('cover_image')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Current Avatar -->
                @if($community->avatar)
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('Current Avatar') }}
                    </label>
                    <div class="mb-2">
                        <img src="{{ Storage::url($community->avatar) }}" alt="{{ $community->name }} avatar" class="w-16 h-16 object-cover rounded-full">
                    </div>
                </div>
                @endif

                <!-- Avatar -->
                <div>
                    <label for="avatar" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('Community Avatar') }} @if($community->avatar) <span class="text-sm text-gray-500">({{ __('Upload new to replace') }})</span> @endif
                    </label>
                    <input type="file" 
                           name="avatar" 
                           id="avatar" 
                           accept="image/*"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('avatar') border-red-500 @enderror">
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('Max 1MB. Recommended size: 200x200px') }}</p>
                    @error('avatar')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Rules -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('Community Rules') }}
                    </label>
                    <div id="rules-container">
                        @php
                            $rules = old('rules', $community->rules ?? []);
                            $rules = is_array($rules) ? $rules : [];
                        @endphp
                        @if(count($rules) > 0)
                            @foreach($rules as $index => $rule)
                                <div class="flex gap-2 mb-2 rule-item">
                                    <input type="text" 
                                           name="rules[]" 
                                           value="{{ $rule }}"
                                           placeholder="{{ __('Enter a rule...') }}"
                                           class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                    <button type="button" onclick="removeRule(this)" class="px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            @endforeach
                        @else
                            <div class="flex gap-2 mb-2 rule-item">
                                <input type="text" 
                                       name="rules[]" 
                                       placeholder="{{ __('Enter a rule...') }}"
                                       class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                <button type="button" onclick="removeRule(this)" class="px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        @endif
                    </div>
                    <button type="button" onclick="addRule()" class="mt-2 px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                        <i class="fas fa-plus mr-2"></i>{{ __('Add Rule') }}
                    </button>
                </div>

                <!-- Submit Buttons -->
                <div class="flex gap-4 pt-4">
                    <button type="submit" class="flex-1 bg-blue-500 hover:bg-blue-600 text-white py-3 px-6 rounded-lg font-medium transition-colors">
                        {{ __('Update Community') }}
                    </button>
                    <a href="{{ route('communities.show', $community) }}" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white py-3 px-6 rounded-lg font-medium text-center transition-colors">
                        {{ __('Cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function addRule() {
    const container = document.getElementById('rules-container');
    const ruleItem = document.createElement('div');
    ruleItem.className = 'flex gap-2 mb-2 rule-item';
    ruleItem.innerHTML = `
        <input type="text" 
               name="rules[]" 
               placeholder="{{ __('Enter a rule...') }}"
               class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
        <button type="button" onclick="removeRule(this)" class="px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
            <i class="fas fa-times"></i>
        </button>
    `;
    container.appendChild(ruleItem);
}

function removeRule(button) {
    const container = document.getElementById('rules-container');
    if (container.children.length > 1) {
        button.parentElement.remove();
    }
}
</script>
@endpush
