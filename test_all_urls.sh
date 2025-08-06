#!/bin/bash

echo "🧪 Testing all URLs mentioned by user..."
echo "========================================"

# List of URLs to test
urls=(
    "http://done.ddev.site:33000/admin"
    "http://done.ddev.site:33000/admin/logs"
    "http://done.ddev.site:33000/admin/users"
    "http://done.ddev.site:33000/admin/artworks"
    "http://done.ddev.site:33000/admin/languages"
    "http://done.ddev.site:33000/admin/evaluations"
    "http://done.ddev.site:33000/admin/settings"
    "http://done.ddev.site:33000/dashboard"
    "http://done.ddev.site:33000/profile"
    "http://done.ddev.site:33000/artworks"
    "http://done.ddev.site:33000/users/20"
    "http://done.ddev.site:33000/moderation/dashboard"
    "http://done.ddev.site:33000/communities/art-critique-circle"
    "http://done.ddev.site:33000/support"
    "http://done.ddev.site:33000/nft/collection"
    "http://done.ddev.site:33000/communities"
)

echo "Starting URL tests..."
echo ""

for url in "${urls[@]}"; do
    echo -n "Testing ${url##*/}: "
    
    # Test with timeout to avoid hanging
    status=$(timeout 10 curl -s -o /dev/null -w "%{http_code}" "$url" 2>/dev/null)
    
    if [ $? -eq 0 ]; then
        case $status in
            200|301|302)
                echo "✅ $status (OK)"
                ;;
            404)
                echo "⚠️  $status (Not Found - may be expected)"
                ;;
            500)
                echo "❌ $status (Server Error - Blade syntax issue likely)"
                # Get the actual error
                timeout 5 curl -s "$url" | head -5 | grep -i "error\|exception" || echo "   No error details available"
                ;;
            *)
                echo "? $status (Unknown)"
                ;;
        esac
    else
        echo "❌ Timeout or connection error"
    fi
done

echo ""
echo "✨ URL testing completed!"
echo ""
echo "Legend:"
echo "✅ = Working correctly (200, 301, 302)"
echo "⚠️  = Not found (404) - may be expected for some URLs"
echo "❌ = Server error (500) - indicates remaining Blade issues"
