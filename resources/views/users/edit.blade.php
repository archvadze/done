<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="/" class="text-xl font-bold text-gray-900">ArtGallery</a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="/artworks" class="text-gray-600 hover:text-gray-900">Artworks</a>
                    <a href="/leaderboard" class="text-gray-600 hover:text-gray-900">Leaderboard</a>
                    <a href="/profile" class="text-gray-600 hover:text-gray-900">Profile</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-3xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-6">Edit Profile</h3>
                
                <form action="/profile" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Profile Picture -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Profile Picture</label>
                        <div class="flex items-center space-x-6">
                            <div class="shrink-0">
                                <div id="avatar-preview" class="relative">
                                    @if($user->avatar_url)
                                        <img id="current-avatar" class="h-20 w-20 object-cover rounded-full border-2 border-gray-200" src="{{ $user->avatar_url }}" alt="Current profile photo">
                                    @else
                                        <div id="current-avatar" class="h-20 w-20 rounded-full bg-gray-300 flex items-center justify-center border-2 border-gray-200">
                                            <span class="text-xl font-medium text-gray-700">{{ substr($user->name, 0, 1) }}</span>
                                        </div>
                                    @endif
                                    <div id="upload-overlay" class="absolute inset-0 bg-black bg-opacity-50 rounded-full flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity cursor-pointer" onclick="document.getElementById('avatar-input').click()">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-1">
                                <label class="block">
                                    <span class="sr-only">Choose profile photo</span>
                                    <input type="file" id="avatar-input" name="avatar" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                                </label>
                                <p class="mt-2 text-xs text-gray-500">
                                    JPG, PNG, GIF up to 2MB. Recommended: 400x400px square image.
                                </p>
                                <button type="button" id="remove-avatar" class="mt-2 text-sm text-red-600 hover:text-red-800 hidden">Remove current photo</button>
                            </div>
                        </div>
                        @error('avatar')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-300 @enderror">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-300 @enderror">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Bio -->
                    <div>
                        <label for="bio" class="block text-sm font-medium text-gray-700">Bio</label>
                        <textarea name="bio" id="bio" rows="3" 
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('bio') border-red-300 @enderror"
                                  placeholder="Tell us about yourself...">{{ old('bio', $user->bio) }}</textarea>
                        @error('bio')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Location -->
                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
                        <input type="text" name="location" id="location" value="{{ old('location', $user->location) }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('location') border-red-300 @enderror"
                               placeholder="City, Country">
                        @error('location')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Website -->
                    <div>
                        <label for="website" class="block text-sm font-medium text-gray-700">Website</label>
                        <input type="url" name="website" id="website" value="{{ old('website', $user->website) }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('website') border-red-300 @enderror"
                               placeholder="https://example.com">
                        @error('website')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Change Section -->
                    <div class="border-t pt-6">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Change Password</h4>
                        
                        <!-- Current Password -->
                        <div class="mb-4">
                            <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
                            <input type="password" name="current_password" id="current_password" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('current_password') border-red-300 @enderror">
                            @error('current_password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- New Password -->
                        <div class="mb-4">
                            <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                            <input type="password" name="password" id="password" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-300 @enderror">
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <p class="mt-2 text-sm text-gray-600">
                            Leave password fields empty if you don't want to change your password.
                        </p>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex justify-between pt-6">
                        <a href="/profile" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                            Cancel
                        </a>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Update Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg">
            Please fix the errors above
        </div>
    @endif

    <script>
        // Avatar upload preview functionality
        document.addEventListener('DOMContentLoaded', function() {
            const avatarInput = document.getElementById('avatar-input');
            const currentAvatar = document.getElementById('current-avatar');
            const removeButton = document.getElementById('remove-avatar');
            let originalAvatarSrc = currentAvatar.src || null;
            let hasNewAvatar = false;

            // Show remove button if user has an avatar
            @if($user->avatar_url)
                removeButton.classList.remove('hidden');
            @endif

            avatarInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    // Validate file size (2MB)
                    if (file.size > 2 * 1024 * 1024) {
                        alert('File size must be less than 2MB');
                        e.target.value = '';
                        return;
                    }

                    // Validate file type
                    if (!file.type.match(/^image\/(jpeg|jpg|png|gif)$/)) {
                        alert('Please select a valid image file (JPG, PNG, GIF)');
                        e.target.value = '';
                        return;
                    }

                    // Create preview
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        if (currentAvatar.tagName === 'IMG') {
                            currentAvatar.src = e.target.result;
                        } else {
                            // Replace div with img
                            const newImg = document.createElement('img');
                            newImg.id = 'current-avatar';
                            newImg.className = 'h-20 w-20 object-cover rounded-full border-2 border-gray-200';
                            newImg.src = e.target.result;
                            newImg.alt = 'Profile photo preview';
                            currentAvatar.parentNode.replaceChild(newImg, currentAvatar);
                        }
                        hasNewAvatar = true;
                        removeButton.classList.remove('hidden');
                    };
                    reader.readAsDataURL(file);
                }
            });

            removeButton.addEventListener('click', function() {
                if (hasNewAvatar) {
                    // Reset to original or default
                    avatarInput.value = '';
                    if (originalAvatarSrc) {
                        if (currentAvatar.tagName === 'IMG') {
                            currentAvatar.src = originalAvatarSrc;
                        }
                    } else {
                        // Replace with default div
                        const defaultDiv = document.createElement('div');
                        defaultDiv.id = 'current-avatar';
                        defaultDiv.className = 'h-20 w-20 rounded-full bg-gray-300 flex items-center justify-center border-2 border-gray-200';
                        defaultDiv.innerHTML = '<span class="text-xl font-medium text-gray-700">{{ substr($user->name, 0, 1) }}</span>';
                        currentAvatar.parentNode.replaceChild(defaultDiv, currentAvatar);
                    }
                    hasNewAvatar = false;
                    @if(!$user->avatar_url)
                        removeButton.classList.add('hidden');
                    @endif
                } else {
                    // TODO: Add remove current avatar functionality
                    if (confirm('Are you sure you want to remove your profile picture?')) {
                        // This would require a separate endpoint to remove avatar
                        alert('Avatar removal functionality will be implemented');
                    }
                }
            });

            // Form validation
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                const submitButton = form.querySelector('button[type="submit"]');
                submitButton.disabled = true;
                submitButton.textContent = 'Updating...';
                
                // Re-enable after 5 seconds to prevent permanent disable
                setTimeout(() => {
                    submitButton.disabled = false;
                    submitButton.textContent = 'Update Profile';
                }, 5000);
            });

            // Auto-hide error messages
            const errorMessages = document.querySelectorAll('.text-red-600');
            errorMessages.forEach(function(message) {
                setTimeout(() => {
                    message.style.opacity = '0.5';
                }, 5000);
            });
        });
    </script>
</body>
</html>
