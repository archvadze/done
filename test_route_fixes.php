<?php
// Test script to verify route fixes
require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Route;

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Check if routes are registered
$routes = Route::getRoutes();

echo "=== Testing Route Fixes ===\n\n";

// Test 1: Check payments.show route
echo "1. Testing payments.show route:\n";
try {
    $paymentsRoute = $routes->getByName('payments.show');
    if ($paymentsRoute) {
        echo "   ✓ Route exists: " . $paymentsRoute->uri() . "\n";
        echo "   ✓ Methods: " . implode('|', $paymentsRoute->methods()) . "\n";
        echo "   ✓ No parameters required: " . (empty($paymentsRoute->parameterNames()) ? 'YES' : 'NO') . "\n";
    } else {
        echo "   ✗ Route not found\n";
    }
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 2: Check support.help.helpful route
echo "2. Testing support.help.helpful route:\n";
try {
    $helpfulRoute = $routes->getByName('support.help.helpful');
    if ($helpfulRoute) {
        echo "   ✓ Route exists: " . $helpfulRoute->uri() . "\n";
        echo "   ✓ Methods: " . implode('|', $helpfulRoute->methods()) . "\n";
        echo "   ✓ Parameters: " . implode(', ', $helpfulRoute->parameterNames()) . "\n";
    } else {
        echo "   ✗ Route not found\n";
    }
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 3: Generate URLs to verify they work
echo "3. Testing URL generation:\n";
try {
    $paymentsUrl = route('payments.show');
    echo "   ✓ Payments URL: " . $paymentsUrl . "\n";
} catch (Exception $e) {
    echo "   ✗ Payments URL error: " . $e->getMessage() . "\n";
}

try {
    // Assume article ID 1 exists
    $helpfulUrl = route('support.help.helpful', ['article' => 1]);
    echo "   ✓ Help helpful URL: " . $helpfulUrl . "\n";
} catch (Exception $e) {
    echo "   ✗ Help helpful URL error: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";
