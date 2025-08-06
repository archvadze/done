<!-- Navigation toggle scripts -->
<script>
    function toggleMobileMenu() {
        const mobileMenu = document.getElementById('mobile-menu');
        mobileMenu.classList.toggle('hidden');
    }

    function toggleUserMenu() {
        const userMenu = document.getElementById('user-menu');
        userMenu.classList.toggle('hidden');
    }

    // Close user menu when clicking outside
    document.addEventListener('click', function(event) {
        const userMenu = document.getElementById('user-menu');
        const userButton = event.target.closest('button[onclick="toggleUserMenu()"]');

        if (!userButton && userMenu && !userMenu.contains(event.target)) {
            userMenu.classList.add('hidden');
        }
    });
</script>
