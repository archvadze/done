#!/bin/bash
echo "=== FINAL TESTING ==="
echo "Testing the 3 original problematic URLs..."

echo "1. Artworks:"
curl -s -I http://done.ddev.site:33000/artworks | head -1

echo "2. Users/20:"
curl -s -I http://done.ddev.site:33000/users/20 | head -1

echo "3. Support:"
curl -s -I http://done.ddev.site:33000/support | head -1

echo "Done!"
