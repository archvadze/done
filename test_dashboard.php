<?php

// Test dashboard access with authentication
$loginUrl = 'http://done.ddev.site:33000/login';
$dashboardUrl = 'http://done.ddev.site:33000/dashboard';

$ch = curl_init();
curl_setopt($ch, CURLOPT_COOKIEJAR, '/tmp/cookies.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/cookies.txt');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

// Get login page to extract CSRF token
curl_setopt($ch, CURLOPT_URL, $loginUrl);
$loginPage = curl_exec($ch);

// Extract CSRF token
if (preg_match('/<input type="hidden" name="_token" value="([^"]*)"/', $loginPage, $matches)) {
    $token = $matches[1];
    echo "CSRF Token: $token\n";
    
    // Attempt login
    curl_setopt($ch, CURLOPT_URL, $loginUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        '_token' => $token,
        'email' => 'anna.weber@artcraft.ge',
        'password' => 'password'
    ]));
    
    $loginResult = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    echo "Login HTTP Code: $httpCode\n";
    
    // Now access dashboard
    curl_setopt($ch, CURLOPT_URL, $dashboardUrl);
    curl_setopt($ch, CURLOPT_POST, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, '');
    
    $dashboardContent = curl_exec($ch);
    $dashboardCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    echo "Dashboard HTTP Code: $dashboardCode\n";
    
    // Check for errors in the content
    if (strpos($dashboardContent, 'Error') !== false || strpos($dashboardContent, 'Exception') !== false) {
        echo "ERRORS FOUND IN DASHBOARD:\n";
        echo substr($dashboardContent, 0, 1000) . "\n";
    } else {
        echo "Dashboard loaded successfully!\n";
    }
} else {
    echo "Could not extract CSRF token\n";
}

curl_close($ch);
