/**
 * User Profile functionality
 * Handles follow/unfollow actions and notifications
 */

/**
 * Toggle follow/unfollow for a user
 */
async function toggleFollow(userId) {
    const followBtn = document.getElementById('follow-btn');
    const followText = document.getElementById('follow-text');
    const followersCount = document.getElementById('followers-count');
    const csrfToken = document.querySelector('meta[name="csrf-token"]');

    if (!followBtn || !followText || !csrfToken) {
        console.error('Required elements not found');
        return;
    }

    // Disable button during request
    followBtn.disabled = true;

    try {
        const response = await fetch(`/users/${userId}/follow`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken.getAttribute('content')
            }
        });

        const data = await response.json();

        if (data.success) {
            // Update button text and style
            if (data.action === 'followed') {
                followText.textContent = 'Following';
                followBtn.className = 'px-4 py-2 rounded-md text-sm font-medium transition-colors bg-gray-200 text-gray-700 hover:bg-gray-300';
            } else {
                followText.textContent = 'Follow';
                followBtn.className = 'px-4 py-2 rounded-md text-sm font-medium transition-colors bg-blue-600 text-white hover:bg-blue-700';
            }

            // Update followers count if element exists
            if (followersCount) {
                followersCount.textContent = data.followers_count;
            }

            // Show success message
            showMessage(`Successfully ${data.action} user!`, 'success');
        } else {
            showMessage(data.message || 'Failed to toggle follow', 'error');
        }
    } catch (error) {
        console.error('Follow error:', error);
        showMessage('Failed to toggle follow. Please try again.', 'error');
    } finally {
        followBtn.disabled = false;
    }
}

/**
 * Show notification message
 */
function showMessage(message, type = 'success') {
    const messageDiv = document.createElement('div');
    const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
    
    messageDiv.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 ${bgColor} text-white`;
    messageDiv.textContent = message;
    messageDiv.setAttribute('role', 'alert');
    messageDiv.setAttribute('aria-live', 'polite');

    document.body.appendChild(messageDiv);

    setTimeout(() => {
        messageDiv.remove();
    }, 3000);
}

/**
 * Initialize user profile functionality
 */
document.addEventListener('DOMContentLoaded', function() {
    // Add any additional initialization if needed
    console.log('User profile functionality loaded');
});
