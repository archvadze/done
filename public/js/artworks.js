/**
 * Artworks Gallery functionality
 * Handles sorting, filtering, infinite scroll, and notifications
 */

// Global state
let loading = false;
let noMorePages = false;

/**
 * Update sort parameter in URL
 */
function updateSort(sortValue) {
    const url = new URL(window.location.href);
    if (sortValue) {
        url.searchParams.set('sort', sortValue);
    } else {
        url.searchParams.delete('sort');
    }
    window.location.href = url.toString();
}

/**
 * Toggle AI generated filter
 */
function toggleAIFilter(checked) {
    const url = new URL(window.location.href);
    if (checked) {
        url.searchParams.set('ai_generated', '1');
    } else {
        url.searchParams.delete('ai_generated');
    }
    window.location.href = url.toString();
}

/**
 * Load more artworks via infinite scroll
 */
function loadMore() {
    if (loading || noMorePages) return;

    const nextPageUrl = document.querySelector('a[rel="next"]')?.href;
    if (!nextPageUrl) {
        noMorePages = true;
        return;
    }

    loading = true;

    fetch(nextPageUrl)
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newArtworks = doc.querySelectorAll('.artwork-card');
            const grid = document.querySelector('.masonry-grid');

            if (newArtworks.length === 0) {
                noMorePages = true;
                loading = false;
                return;
            }

            newArtworks.forEach(artwork => {
                grid.appendChild(artwork);
            });

            // Update pagination
            const newPagination = doc.querySelector('.pagination');
            const currentPagination = document.querySelector('.pagination');
            if (newPagination && currentPagination) {
                currentPagination.innerHTML = newPagination.innerHTML;
            }

            // Check if there's still a next page
            const hasNextPage = doc.querySelector('a[rel="next"]');
            if (!hasNextPage) {
                noMorePages = true;
            }

            loading = false;
        })
        .catch(error => {
            console.error('Error loading more artworks:', error);
            loading = false;
        });
}

/**
 * Show notification message
 */
function showNotification(message, type = 'success') {
    const div = document.createElement('div');
    const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
    const icon = type === 'success' ? '✅' : '❌';
    
    div.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-md shadow-lg z-50`;
    div.textContent = `${icon} ${message}`;
    
    document.body.appendChild(div);
    setTimeout(() => div.remove(), 5000);
}

/**
 * Initialize artworks gallery functionality
 */
document.addEventListener('DOMContentLoaded', function() {
    // Auto-load more when scrolling near bottom
    window.addEventListener('scroll', () => {
        if (!loading && !noMorePages && (window.innerHeight + window.scrollY) >= document.body.offsetHeight - 1000) {
            loadMore();
        }
    });
    
    // Show messages from session data
    if (window.successMessage) {
        showNotification(window.successMessage, 'success');
    }
    
    if (window.errorMessage) {
        showNotification(window.errorMessage, 'error');
    }
});
