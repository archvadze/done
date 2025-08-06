#!/usr/bin/env php
<?php

function checkBladeFileDetailed($filepath) {
    $content = file_get_contents($filepath);
    $lines = explode("\n", $content);
    
    $stack = [];
    $errors = [];
    
    foreach ($lines as $lineNum => $line) {
        $lineText = trim($line);
        
        // Opening directives
        if (preg_match('/@(if|unless|isset|empty|auth|guest|can|cannot|hasSection|sectionMissing|switch|for|foreach|forelse|while)\b/', $lineText, $matches)) {
            $directive = $matches[1];
            $stack[] = [
                'directive' => $directive,
                'line' => $lineNum + 1,
                'content' => $lineText
            ];
            continue;
        }
        
        // Else/elseif (don't affect stack)
        if (preg_match('/@(else|elseif|elseunless|elsecan|elsecannot|elseauth|elseguest|empty)\b/', $lineText, $matches)) {
            $directive = $matches[1];
            if (empty($stack)) {
                $errors[] = "Line " . ($lineNum + 1) . ": $directive without opening directive: $lineText";
            } elseif ($directive === 'empty') {
                // @empty is special - it should only be inside @forelse
                $lastOpen = end($stack);
                if ($lastOpen['directive'] !== 'forelse') {
                    $errors[] = "Line " . ($lineNum + 1) . ": @empty can only be used with @forelse (current: @{$lastOpen['directive']} at line {$lastOpen['line']})";
                }
            }
            continue;
        }
        
        // Closing directives
        if (preg_match('/@(endif|endunless|endisset|endempty|endauth|endguest|endcan|endcannot|endhasSection|endsectionMissing|endswitch|endfor|endforeach|endforelse|endwhile)\b/', $lineText, $matches)) {
            $closeDirective = $matches[1];
            $expectedOpen = str_replace('end', '', $closeDirective);
            
            if (empty($stack)) {
                $errors[] = "Line " . ($lineNum + 1) . ": $closeDirective without opening directive: $lineText";
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

$file = 'resources/views/communities/posts/show.blade.php';
echo "Checking $file...\n";
$errors = checkBladeFileDetailed($file);
if (!empty($errors)) {
    foreach ($errors as $error) {
        echo "  $error\n";
    }
} else {
    echo "  ✅ No errors found\n";
}
