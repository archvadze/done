<?php
echo "Testing support page routes...\n";

// Test artisan route:list for support routes
$output = shell_exec('cd /var/www/done && ddev exec php artisan route:list --name=support 2>&1');
echo "Support routes:\n" . $output . "\n";

// Test if we can reach support page
$status = shell_exec('curl -s -w "%{http_code}" -o /dev/null http://done.ddev.site:33000/support 2>/dev/null');
echo "Support page status: " . trim($status) . "\n";
