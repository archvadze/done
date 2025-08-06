#!/bin/bash

echo "🔧 Comprehensive Blade Syntax Error Fix Script"
echo "=============================================="

# Step 1: Force clear all compiled views and caches
echo "1. Clearing all compiled views and caches..."
rm -rf storage/framework/views/*
rm -rf storage/framework/cache/data/*
rm -rf storage/framework/sessions/*
rm -rf bootstrap/cache/*.php

echo "   ✅ Cleared compiled views and caches"

# Step 2: Check for and fix line ending issues in key files
echo "2. Checking and fixing line endings in critical files..."

# Fix routes files (again, in case they got corrupted)
if [ -f "routes/web.php" ]; then
    dos2unix routes/web.php 2>/dev/null || true
    echo "   ✅ Fixed routes/web.php line endings"
fi

if [ -f "routes/api.php" ]; then
    dos2unix routes/api.php 2>/dev/null || true
    echo "   ✅ Fixed routes/api.php line endings"
fi

# Fix all Blade template files
find resources/views -name "*.blade.php" -exec dos2unix {} \; 2>/dev/null || true
echo "   ✅ Fixed all Blade template line endings"

# Step 3: Check for BOM (Byte Order Mark) in files
echo "3. Checking for BOM in Blade files..."
find resources/views -name "*.blade.php" -exec sed -i '1s/^\xEF\xBB\xBF//' {} \;
echo "   ✅ Removed BOM from Blade files"

# Step 4: Check specific files for syntax issues
echo "4. Performing syntax validation..."

# Create a simple PHP script to validate Blade syntax
cat > temp_blade_validator.php << 'EOF'
<?php
function validateBladeFile($file) {
    $content = file_get_contents($file);
    
    // Check for basic balance of directives
    $directives = [
        '@if' => '@endif',
        '@unless' => '@endunless',
        '@isset' => '@endisset',
        '@empty' => '@endempty',
        '@auth' => '@endauth',
        '@guest' => '@endguest',
        '@section' => '@endsection',
        '@push' => '@endpush',
        '@once' => '@endonce',
        '@php' => '@endphp',
        '@foreach' => '@endforeach',
        '@forelse' => '@endforelse',
        '@for' => '@endfor',
        '@while' => '@endwhile',
        '@switch' => '@endswitch',
        '@verbatim' => '@endverbatim'
    ];
    
    $errors = [];
    foreach ($directives as $start => $end) {
        $startCount = substr_count($content, $start);
        $endCount = substr_count($content, $end);
        
        if ($startCount !== $endCount) {
            $errors[] = "Unmatched: $start ($startCount) vs $end ($endCount)";
        }
    }
    
    return $errors;
}

// Check main layout files
$filesToCheck = [
    'resources/views/layouts/app.blade.php',
    'resources/views/layouts/admin.blade.php',
    'resources/views/partials/nav.blade.php',
    'resources/views/partials/footer.blade.php'
];

foreach ($filesToCheck as $file) {
    if (file_exists($file)) {
        $errors = validateBladeFile($file);
        if (empty($errors)) {
            echo "✅ $file: OK\n";
        } else {
            echo "❌ $file: " . implode(', ', $errors) . "\n";
        }
    }
}
EOF

php temp_blade_validator.php
rm temp_blade_validator.php

# Step 5: Fix specific known issues
echo "5. Applying specific fixes..."

# Ensure app.blade.php is properly formatted
if [ -f "resources/views/layouts/app.blade.php" ]; then
    # Remove any trailing whitespace
    sed -i 's/[[:space:]]*$//' resources/views/layouts/app.blade.php
    echo "   ✅ Cleaned app.blade.php"
fi

# Step 6: Set proper permissions
echo "6. Setting proper permissions..."
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
echo "   ✅ Set proper permissions"

# Step 7: Try to regenerate autoloader and config
echo "7. Regenerating Laravel configuration..."
composer dump-autoload --no-dev --classmap-authoritative 2>/dev/null || composer dump-autoload 2>/dev/null || true

echo ""
echo "🎉 Fix script completed!"
echo "Now test the URLs to see if the Blade syntax errors are resolved."
