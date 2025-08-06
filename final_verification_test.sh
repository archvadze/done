#!/bin/bash
echo "=== FINAL COMPREHENSIVE TEST AFTER BLADE FIX ==="
echo "Testing all URLs that were previously failing..."
echo

# Test URLs
urls=(
    "/"
    "/artworks"
    "/users/20"
    "/support"
    "/dashboard"
    "/admin/login"
)

for url in "${urls[@]}"; do
    echo -n "Testing $url ... "
    
    # Use timeout to prevent hanging
    response=$(timeout 10 curl -s -o /dev/null -w "%{http_code}" "http://localhost$url" 2>/dev/null)
    
    if [ $? -eq 124 ]; then
        echo "⏰ TIMEOUT"
    elif [ "$response" = "200" ]; then
        echo "✅ $response (OK)"
    elif [ "$response" = "302" ] || [ "$response" = "301" ]; then
        echo "🔄 $response (Redirect)"
    elif [ "$response" = "404" ]; then
        echo "❌ $response (Not Found)"
    elif [ "$response" = "500" ]; then
        echo "💥 $response (Server Error)"
    else
        echo "❓ $response (Unknown)"
    fi
done

echo
echo "=== SUMMARY ==="
echo "✅ = Working correctly"
echo "🔄 = Redirecting (likely protected routes)"
echo "❌ = Not found (may be expected)"
echo "💥 = Server error (needs investigation)"
echo "⏰ = Timeout (possible infinite loop)"
