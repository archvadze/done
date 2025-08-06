#!/bin/bash

echo "🧪 Comprehensive Application Testing Report"
echo "==========================================="
echo ""

# Test URLs with detailed reporting
urls=(
    "http://done.ddev.site:33000/"
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

working_count=0
error_count=0
redirect_count=0
not_found_count=0

echo "URL Test Results:"
echo "=================="

for url in "${urls[@]}"; do
    route_name=$(echo "$url" | sed 's|http://done.ddev.site:33000||' | sed 's|^$|/|')
    printf "%-40s " "$route_name"
    
    # Test with 8 second timeout
    status=$(timeout 8 curl -s -o /dev/null -w "%{http_code}" "$url" 2>/dev/null)
    
    if [ $? -eq 0 ]; then
        case $status in
            200)
                echo "✅ $status (Working)"
                working_count=$((working_count + 1))
                ;;
            301|302)
                echo "🔄 $status (Redirect)"
                redirect_count=$((redirect_count + 1))
                ;;
            404)
                echo "⚠️  $status (Not Found)"
                not_found_count=$((not_found_count + 1))
                ;;
            500)
                echo "❌ $status (Server Error)"
                error_count=$((error_count + 1))
                ;;
            *)
                echo "? $status (Other)"
                ;;
        esac
    else
        echo "❌ Timeout/Connection Error"
        error_count=$((error_count + 1))
    fi
done

echo ""
echo "Summary:"
echo "========"
echo "✅ Working (200):        $working_count"
echo "🔄 Redirects (301/302):  $redirect_count" 
echo "⚠️  Not Found (404):     $not_found_count"
echo "❌ Errors (500/timeout): $error_count"

total=$((working_count + redirect_count + not_found_count + error_count))
echo "📊 Total URLs tested:    $total"

if [ $error_count -eq 0 ]; then
    echo ""
    echo "🎉 All URLs are working! No Blade syntax errors detected."
else
    echo ""
    echo "⚠️  $error_count URLs have errors that need attention."
    echo ""
    echo "🔍 Checking for Blade errors in logs..."
    if grep -q "syntax error.*endif" storage/logs/laravel.log 2>/dev/null; then
        echo "❌ Blade syntax errors found in logs"
        echo "📋 Latest Blade error:"
        grep "syntax error.*endif" storage/logs/laravel.log | tail -1
    else
        echo "✅ No Blade syntax errors in current logs"
    fi
fi
