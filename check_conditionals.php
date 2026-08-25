<?php

$file = 'resources/views/partials/nav.blade.php';
$content = file_get_contents($file);
$lines = explode("\n", $content);

$stack = [];
$errors = [];

foreach ($lines as $lineNum => $line) {
    $lineNum++; // Make it 1-based
    
    // Check for opening conditionals
    if (preg_match('/@(if|auth|guest|unless|empty|isset|switch)\b/', $line, $matches)) {
        $stack[] = [$matches[1], $lineNum];
        echo "Line {$lineNum}: Opening {$matches[1]}\n";
    }
    
    // Check for elseif/else
    if (preg_match('/@(elseif|else|elseguest|elseauth)\b/', $line, $matches)) {
        echo "Line {$lineNum}: {$matches[1]}\n";
    }
    
    // Check for closing conditionals
    if (preg_match('/@(endif|endauth|endguest|endunless|endempty|endisset|endswitch)\b/', $line, $matches)) {
        $expected = [
            'endif' => 'if',
            'endauth' => 'auth', 
            'endguest' => 'guest',
            'endunless' => 'unless',
            'endempty' => 'empty',
            'endisset' => 'isset',
            'endswitch' => 'switch'
        ];
        
        $closingType = $matches[1];
        $expectedOpening = $expected[$closingType];
        
        if (empty($stack)) {
            $errors[] = "Line {$lineNum}: Unexpected {$closingType} - no opening statement";
        } else {
            $last = array_pop($stack);
            if ($last[0] !== $expectedOpening) {
                $errors[] = "Line {$lineNum}: {$closingType} doesn't match opening {$last[0]} at line {$last[1]}";
            }
        }
        echo "Line {$lineNum}: Closing {$closingType}\n";
    }
}

if (!empty($stack)) {
    foreach ($stack as $unclosed) {
        $errors[] = "Unclosed {$unclosed[0]} at line {$unclosed[1]}";
    }
}

if (empty($errors)) {
    echo "\nAll conditionals are properly balanced!\n";
} else {
    echo "\nErrors found:\n";
    foreach ($errors as $error) {
        echo "- {$error}\n";
    }
}
