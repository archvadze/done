<?php

// Test all the user-requested routes
$routes_to_test = [
    'http://done.ddev.site:33000/dashboard',
    'http://done.ddev.site:33000/community/art-critique-circle',
    'http://done.ddev.site:33000/support/faq/',
    'http://done.ddev.site:33000/admin',
    'http://done.ddev.site:33000/communities',
    'http://done.ddev.site:33000/',
    'http://done.ddev.site:33000/community',
    'http://done.ddev.site:33000/login',
    'http://done.ddev.site:33000/register'
];

echo "Testing all user-requested routes:\n\n";

foreach ($routes_to_test as $url) {
    echo "Testing: $url\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "Status: $http_code";
    
    if ($http_code == 200) {
        echo " ✅ OK";
    } elseif ($http_code == 302) {
        echo " ↗️ Redirect (expected for protected routes)";
    } elseif ($http_code == 404) {
        echo " ❌ Not Found";
    } elseif ($http_code == 500) {
        echo " ⚠️ Server Error";
    } else {
        echo " ⚠️ Other";
    }
    
    echo "\n\n";
}

echo "Route testing completed!\n";
