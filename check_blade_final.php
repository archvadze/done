#!/usr/bin/env php
<?php

function checkBladeFileCorrect($filepath) {
    $content = file_get_contents($filepath);
    $lines = explode("\n", $content);
    
    $stack = [];
    $errors = [];
    
    foreach ($lines as $lineNum => $line) {
        $lineText = trim($line);
        
        // Opening directives
        if (preg_match('/@(if|unless|isset|auth|guest|can|cannot|hasSection|sectionMissing|switch|for|foreach|forelse|while)\b/', $lineText, $matches)) {
            $directive = $matches[1];
            $stack[] = [
                'directive' => $directive,
                'line' => $lineNum + 1,
                'content' => $lineText
            ];
            continue;
        }
        
        // @empty is special - it's only valid inside @forelse and doesn't need closing
        if (preg_match('/@empty\b/', $lineText)) {
            if (empty($stack)) {
                $errors[] = "Line " . ($lineNum + 1) . ": @empty without @forelse: $lineText";
            } else {
                $lastOpen = end($stack);
                if ($lastOpen['directive'] !== 'forelse') {
                    $errors[] = "Line " . ($lineNum + 1) . ": @empty can only be used with @forelse (current: @{$lastOpen['directive']} at line {$lastOpen['line']})";
                }
            }
            continue;
        }
        
        // Other else/elseif directives (don't affect stack)
        if (preg_match('/@(else|elseif|elseunless|elsecan|elsecannot|elseauth|elseguest)\b/', $lineText)) {
            if (empty($stack)) {
                $errors[] = "Line " . ($lineNum + 1) . ": {$matches[1]} without opening directive: $lineText";
            }
            continue;
        }
        
        // Closing directives
        if (preg_match('/@(endif|endunless|endisset|endauth|endguest|endcan|endcannot|endhasSection|endsectionMissing|endswitch|endfor|endforeach|endforelse|endwhile)\b/', $lineText, $matches)) {
            $closeDirective = $matches[1];
            $expectedOpen = str_replace('end', '', $closeDirective);
            
            if (empty($stack)) {
                $errors[] = "Line " . ($lineNum + 1) . ": @$closeDirective without opening directive: $lineText";
            } else {
                $lastOpen = array_pop($stack);
                if ($lastOpen['directive'] !== $expectedOpen) {
                    $errors[] = "Line " . ($lineNum + 1) . ": Expected @end{$lastOpen['directive']} but found @$closeDirective (opened at line {$lastOpen['line']})";
                    // Put it back since we didn't match
                    $stack[] = $lastOpen;
                }
            }
        }
    }
    
    // Check for unclosed directives
    foreach ($stack as $unclosed) {
        $errors[] = "Line {$unclosed['line']}: Unclosed @{$unclosed['directive']}: {$unclosed['content']}";
    }
    
    return $errors;
}

// Check all blade files
$bladeFiles = [];
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('resources/views'));
foreach ($iterator as $file) {
    if ($file->isFile() && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
        $bladeFiles[] = $file->getPathname();
    }
}

$totalErrors = 0;
foreach ($bladeFiles as $file) {
    $errors = checkBladeFileCorrect($file);
    if (!empty($errors)) {
        echo "\n=== ERRORS IN $file ===\n";
        foreach ($errors as $error) {
            echo "  $error\n";
            $totalErrors++;
        }
    }
}

if ($totalErrors === 0) {
    echo "✅ All Blade files have balanced directives!\n";
} else {
    echo "\n❌ Found $totalErrors directive errors across " . count($bladeFiles) . " Blade files.\n";
}
