#!/bin/bash

# Fix common problematic classes in Blade files
echo "Fixing problematic Tailwind classes..."

# Find all Blade files and apply fixes
find resources/views -name "*.blade.php" -type f | while read file; do
    echo "Processing: $file"
    
    # Remove common background classes
    sed -i 's/bg-gray-50//g' "$file"
    sed -i 's/bg-gray-100//g' "$file"
    sed -i 's/bg-gray-200//g' "$file"
    sed -i 's/bg-gray-300//g' "$file"
    sed -i 's/bg-gray-400//g' "$file"
    sed -i 's/bg-gray-500//g' "$file"
    sed -i 's/bg-gray-600//g' "$file"
    sed -i 's/bg-gray-700//g' "$file"
    sed -i 's/bg-gray-800//g' "$file"
    sed -i 's/bg-gray-900//g' "$file"
    
    # Replace some specific background colors with style guide classes
    sed -i 's/bg-white/bg-card/g' "$file"
    sed -i 's/bg-blue-/bg-primary/g' "$file"
    sed -i 's/bg-green-/bg-success/g' "$file"
    sed -i 's/bg-red-/bg-error/g' "$file"
    sed -i 's/bg-purple-/bg-secondary/g' "$file"
    
    # Remove border classes (but keep border- for specific borders)
    sed -i 's/border border-/border-/g' "$file"
    sed -i 's/ border / /g' "$file"
    sed -i 's/"border"/""/g' "$file"
    
    # Remove rounded classes but keep rounded- for specific ones
    sed -i 's/rounded-lg//g' "$file"
    sed -i 's/rounded-md//g' "$file"
    sed -i 's/rounded-sm//g' "$file"
    sed -i 's/rounded-xl//g' "$file"
    sed -i 's/rounded-2xl//g' "$file"
    sed -i 's/rounded-3xl//g' "$file"
    sed -i 's/rounded-full//g' "$file"
    sed -i 's/ rounded / /g' "$file"
    sed -i 's/"rounded"/""/g' "$file"
    
    # Fix text colors to style guide
    sed -i 's/text-gray-900/text-primary/g' "$file"
    sed -i 's/text-gray-800/text-primary/g' "$file"
    sed -i 's/text-gray-700/text-secondary/g' "$file"
    sed -i 's/text-gray-600/text-secondary/g' "$file"
    sed -i 's/text-gray-500/text-secondary/g' "$file"
    sed -i 's/text-gray-400/text-secondary/g' "$file"
    sed -i 's/text-blue-600/text-primary/g' "$file"
    sed -i 's/text-blue-700/text-primary-dark/g' "$file"
    
    # Fix hover colors
    sed -i 's/hover:bg-gray-/hover:bg-card-/g' "$file"
    sed -i 's/hover:bg-blue-/hover:bg-primary-/g' "$file"
    sed -i 's/hover:text-blue-/hover:text-primary-/g' "$file"
    sed -i 's/hover:text-gray-/hover:text-/g' "$file"
    
    # Clean up double spaces
    sed -i 's/  / /g' "$file"
    sed -i 's/class=" /class="/g' "$file"
    sed -i 's/ "/"/g' "$file"
done

echo "Style fixes completed!"
