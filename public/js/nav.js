/**
 * Navigation functionality for the Art Gallery application
 * Handles mobile menu toggling and user dropdown functionality
 */

// Global navigation state
let userMenuOpen = false;
let mobileMenuOpen = false;

/**
 * Toggle user dropdown menu
 */
function toggleUserMenu() {
    const userMenu = document.getElementById('user-menu');
    const userMenuButton = document.getElementById('user-menu-button');
    
    if (!userMenu || !userMenuButton) return;
    
    userMenuOpen = !userMenuOpen;
    
    if (userMenuOpen) {
        userMenu.classList.remove('hidden');
        userMenu.classList.add('block');
        userMenuButton.setAttribute('aria-expanded', 'true');
    } else {
        userMenu.classList.add('hidden');
        userMenu.classList.remove('block');
        userMenuButton.setAttribute('aria-expanded', 'false');
    }
}

/**
 * Toggle mobile menu
 */
function toggleMobileMenu() {
    const mobileMenu = document.getElementById('mobile-menu');
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    
    if (!mobileMenu || !mobileMenuButton) return;
    
    mobileMenuOpen = !mobileMenuOpen;
    
    if (mobileMenuOpen) {
        mobileMenu.classList.remove('hidden');
        mobileMenu.classList.add('block');
        mobileMenuButton.setAttribute('aria-expanded', 'true');
    } else {
        mobileMenu.classList.add('hidden');
        mobileMenu.classList.remove('block');
        mobileMenuButton.setAttribute('aria-expanded', 'false');
    }
}

/**
 * Close dropdowns when clicking outside
 */
function closeDropdownsOnOutsideClick(event) {
    const userMenu = document.getElementById('user-menu');
    const userMenuButton = document.getElementById('user-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    
    // Close user menu if clicking outside
    if (userMenuOpen && userMenu && userMenuButton) {
        if (!userMenu.contains(event.target) && !userMenuButton.contains(event.target)) {
            toggleUserMenu();
        }
    }
    
    // Close mobile menu if clicking outside
    if (mobileMenuOpen && mobileMenu && mobileMenuButton) {
        if (!mobileMenu.contains(event.target) && !mobileMenuButton.contains(event.target)) {
            toggleMobileMenu();
        }
    }
}

/**
 * Handle escape key press to close dropdowns
 */
function handleEscapeKey(event) {
    if (event.key === 'Escape') {
        if (userMenuOpen) toggleUserMenu();
        if (mobileMenuOpen) toggleMobileMenu();
    }
}

/**
 * Initialize navigation functionality when DOM is loaded
 */
document.addEventListener('DOMContentLoaded', function() {
    // Add event listeners
    document.addEventListener('click', closeDropdownsOnOutsideClick);
    document.addEventListener('keydown', handleEscapeKey);
    
    // Initialize ARIA attributes
    const userMenuButton = document.getElementById('user-menu-button');
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    
    if (userMenuButton) {
        userMenuButton.setAttribute('aria-expanded', 'false');
        userMenuButton.setAttribute('aria-haspopup', 'true');
    }
    
    if (mobileMenuButton) {
        mobileMenuButton.setAttribute('aria-expanded', 'false');
        mobileMenuButton.setAttribute('aria-controls', 'mobile-menu');
    }
});
