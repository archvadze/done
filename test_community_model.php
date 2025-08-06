<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\Community;

$app = require_once __DIR__ . '/bootstrap/app.php';

// Test finding the community
try {
    $community = Community::where('slug', 'art-critique-circle')->first();
    if ($community) {
        echo "✅ Community found: " . $community->name . "\n";
        echo "   ID: " . $community->id . "\n";
        echo "   Slug: " . $community->slug . "\n";
        echo "   Privacy: " . $community->privacy . "\n";
        
        // Test loading relationships
        try {
            $community->load(['creator']);
            echo "✅ Creator relationship loaded\n";
        } catch (Exception $e) {
            echo "❌ Creator relationship failed: " . $e->getMessage() . "\n";
        }
        
        try {
            $community->load(['activeMembers']);
            echo "✅ Active members relationship loaded\n";
        } catch (Exception $e) {
            echo "❌ Active members relationship failed: " . $e->getMessage() . "\n";
        }
        
    } else {
        echo "❌ Community 'art-critique-circle' not found\n";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
