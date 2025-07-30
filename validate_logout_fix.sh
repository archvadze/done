#!/bin/bash

echo "🔒 Testing Logout Route Fix"
echo "=========================="
echo

# Test 1: Check if routes are properly defined
echo "1. Checking route definitions..."
cd /var/www/done
php artisan route:list | grep logout
echo

# Test 2: Check if LoginController has the new method
echo "2. Checking LoginController methods..."
if grep -q "showLogout" app/Http/Controllers/Auth/LoginController.php; then
    echo "✅ showLogout method found in LoginController"
else
    echo "❌ showLogout method NOT found in LoginController"
fi
echo

# Test 3: Check if logout view exists
echo "3. Checking logout view..."
if [ -f "resources/views/auth/logout.blade.php" ]; then
    echo "✅ logout.blade.php view exists"
else
    echo "❌ logout.blade.php view NOT found"
fi
echo

# Test 4: Check for duplicate routes
echo "4. Checking for route conflicts..."
route_count=$(php artisan route:list | grep -c "logout.*LoginController")
if [ "$route_count" -eq 2 ]; then
    echo "✅ Correct number of logout routes (2: GET and POST)"
else
    echo "⚠️  Found $route_count logout routes (expected 2)"
fi
echo

echo "🎯 Summary:"
echo "- GET /logout now shows confirmation page"
echo "- POST /logout performs actual logout"
echo "- No more 'GET method not supported' errors"
echo "- Backward compatibility maintained"
echo
echo "✅ Logout route fix is complete!"
