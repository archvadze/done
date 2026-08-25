<?php

// Read the nav file and parse ALL Blade directives
$file = 'resources/views/dashboard/index.blade.php';
$content = file_get_contents($file);
$lines = explode("\n", $content);

$ifStack = [];
$authStack = [];
$errors = [];

foreach ($lines as $lineNum => $line) {
    $lineNum++; // Make it 1-based
    
    // Check for @if directives (including compound conditions)
    if (preg_match('/@(if|unless|isset|empty|switch)\b/', $line, $matches)) {
        $ifStack[] = [$matches[1], $lineNum];
        echo "Line {$lineNum}: Opening {$matches[1]}\n";
    }
    
    // Check for auth/guest directives
    if (preg_match('/@(auth|guest)\b/', $line, $matches)) {
        $authStack[] = [$matches[1], $lineNum];
        echo "Line {$lineNum}: Opening {$matches[1]}\n";
    }
    
    // Check for else/elseif
    if (preg_match('/@(else|elseif|elseguest|elseauth)\b/', $line, $matches)) {
        echo "Line {$lineNum}: {$matches[1]}\n";
    }
    
    // Check for closing @if directives
    if (preg_match('/@(endif|endunless|endisset|endempty|endswitch)\b/', $line, $matches)) {
        $expected = [
            'endif' => 'if',
            'endunless' => 'unless',
            'endisset' => 'isset',
            'endempty' => 'empty',
            'endswitch' => 'switch'
        ];
        
        $closingType = $matches[1];
        $expectedOpening = $expected[$closingType];
        
        if (empty($ifStack)) {
            $errors[] = "Line {$lineNum}: Unexpected {$closingType} - no opening statement";
            echo "ERROR Line {$lineNum}: Unexpected {$closingType}\n";
        } else {
            $last = array_pop($ifStack);
            if ($last[0] !== $expectedOpening) {
                $errors[] = "Line {$lineNum}: {$closingType} doesn't match opening {$last[0]} at line {$last[1]}";
                echo "ERROR Line {$lineNum}: {$closingType} doesn't match {$last[0]} from line {$last[1]}\n";
            } else {
                echo "Line {$lineNum}: Closing {$closingType} (matches {$expectedOpening} from line {$last[1]})\n";
            }
        }
    }
    
    // Check for end auth directives
    if (preg_match('/@(endauth|endguest)\b/', $line, $matches)) {
        $expected = [
            'endauth' => 'auth',
            'endguest' => 'guest'
        ];
        
        $closingType = $matches[1];
        $expectedOpening = $expected[$closingType];
        
        if (empty($authStack)) {
            $errors[] = "Line {$lineNum}: Unexpected {$closingType} - no opening statement";
            echo "ERROR Line {$lineNum}: Unexpected {$closingType}\n";
        } else {
            $last = array_pop($authStack);
            if ($last[0] !== $expectedOpening) {
                $errors[] = "Line {$lineNum}: {$closingType} doesn't match opening {$last[0]} at line {$last[1]}";
                echo "ERROR Line {$lineNum}: {$closingType} doesn't match {$last[0]} from line {$last[1]}\n";
            } else {
                echo "Line {$lineNum}: Closing {$closingType} (matches {$expectedOpening} from line {$last[1]})\n";
            }
        }
    }
}

echo "\n=== SUMMARY ===\n";
if (!empty($ifStack)) {
    echo "❌ Unclosed @if directives:\n";
    foreach ($ifStack as $unclosed) {
        $errors[] = "Unclosed {$unclosed[0]} at line {$unclosed[1]}";
        echo "- Unclosed {$unclosed[0]} at line {$unclosed[1]}\n";
    }
}

if (!empty($authStack)) {
    echo "❌ Unclosed auth directives:\n";
    foreach ($authStack as $unclosed) {
        $errors[] = "Unclosed {$unclosed[0]} at line {$unclosed[1]}";
        echo "- Unclosed {$unclosed[0]} at line {$unclosed[1]}\n";
    }
}

if (empty($errors)) {
    echo "✅ All Blade directives are properly balanced!\n";
} else {
    echo "❌ Total errors found: " . count($errors) . "\n";
}
