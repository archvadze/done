/**
 * Profile Edit functionality
 * Handles avatar upload, preview, and form validation
 */

/**
 * Initialize profile edit functionality
 */
document.addEventListener('DOMContentLoaded', function() {
    const avatarInput = document.getElementById('avatar-input');
    const currentAvatar = document.getElementById('current-avatar');
    const removeButton = document.getElementById('remove-avatar');
    const form = document.querySelector('form');
    
    if (!avatarInput || !currentAvatar || !removeButton) return;
    
    let originalAvatarSrc = currentAvatar.src || null;
    let hasNewAvatar = false;
    
    // Show remove button if user has an avatar
    if (window.userHasAvatar) {
        removeButton.classList.remove('hidden');
    }
    
    /**
     * Handle avatar file selection
     */
    avatarInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;
        
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
    });
    
    /**
     * Handle avatar removal
     */
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
                defaultDiv.innerHTML = `<span class="text-xl font-medium text-gray-700">${window.userInitial || 'U'}</span>`;
                currentAvatar.parentNode.replaceChild(defaultDiv, currentAvatar);
            }
            hasNewAvatar = false;
            if (!window.userHasAvatar) {
                removeButton.classList.add('hidden');
            }
        } else {
            // TODO: Add remove current avatar functionality
            if (confirm('Are you sure you want to remove your profile picture?')) {
                alert('Avatar removal functionality will be implemented');
            }
        }
    });
    
    /**
     * Handle form submission
     */
    if (form) {
        form.addEventListener('submit', function(e) {
            const submitButton = form.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.textContent = 'Updating...';
                
                // Re-enable after 5 seconds to prevent permanent disable
                setTimeout(() => {
                    submitButton.disabled = false;
                    submitButton.textContent = 'Update Profile';
                }, 5000);
            }
        });
    }
    
    /**
     * Auto-hide error messages
     */
    const errorMessages = document.querySelectorAll('.text-red-600');
    errorMessages.forEach(function(message) {
        setTimeout(() => {
            message.style.opacity = '0.5';
        }, 5000);
    });
});
