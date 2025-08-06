#!/usr/bin/env php
<?php

function checkBladeFile($filepath) {
    $content = file_get_contents($filepath);
    $lines = explode("\n", $content);
    
    $stack = [];
    $errors = [];
    
    foreach ($lines as $lineNum => $line) {
        $line = trim($line);
        
        // Check for opening directives
        if (preg_match('/@(if|unless|isset|empty|auth|guest|can|cannot|hasSection|sectionMissing|switch|for|foreach|forelse|while)\b/', $line, $matches)) {
            $directive = $matches[1];
            $stack[] = [
                'directive' => $directive,
                'line' => $lineNum + 1,
                'content' => $line
            ];
        }
        
        // Check for else/elseif
        elseif (preg_match('/@(else|elseif|elseunless|elsecan|elsecannot|elseauth|elseguest)\b/', $line, $matches)) {
            if (empty($stack)) {
                $errors[] = "Line " . ($lineNum + 1) . ": {$matches[1]} without opening directive: $line";
            }
        }
        
        // Check for closing directives
        elseif (preg_match('/@(endif|endunless|endisset|endempty|endauth|endguest|endcan|endcannot|endhasSection|endsectionMissing|endswitch|endfor|endforeach|endforelse|endwhile)\b/', $line, $matches)) {
            $closeDirective = $matches[1];
            $expectedOpen = str_replace('end', '', $closeDirective);
            
            if (empty($stack)) {
                $errors[] = "Line " . ($lineNum + 1) . ": $closeDirective without opening directive: $line";
            } else {
                $lastOpen = array_pop($stack);
                if ($lastOpen['directive'] !== $expectedOpen) {
                    $errors[] = "Line " . ($lineNum + 1) . ": Expected end{$lastOpen['directive']} but found $closeDirective (opened at line {$lastOpen['line']})";
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

// Find all Blade files
$bladeFiles = [];
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('resources/views'));
foreach ($iterator as $file) {
    if ($file->isFile() && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
        $bladeFiles[] = $file->getPathname();
    }
}

$totalErrors = 0;
foreach ($bladeFiles as $file) {
    $errors = checkBladeFile($file);
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
