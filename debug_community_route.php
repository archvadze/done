<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Test if we can connect to database
try {
    $pdo = new PDO('mysql:host=db;dbname=db', 'db', 'db');
    echo "✅ Database connection successful\n";
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
}

// Test if communities table exists and has data
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'communities'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Communities table exists\n";
        
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM communities");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "📊 Communities count: " . $result['count'] . "\n";
        
        if ($result['count'] > 0) {
            $stmt = $pdo->query("SELECT id, name, slug FROM communities LIMIT 5");
            echo "📋 Sample communities:\n";
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "  - ID: {$row['id']}, Name: {$row['name']}, Slug: {$row['slug']}\n";
            }
        } else {
            echo "⚠️  Communities table is empty\n";
        }
    } else {
        echo "❌ Communities table does not exist\n";
    }
} catch (Exception $e) {
    echo "❌ Error checking communities table: " . $e->getMessage() . "\n";
}

// Test Laravel's ability to find communities
try {
    $request = Request::create('/community/art-critique-circle', 'GET');
    $response = $kernel->handle($request);
    echo "🔍 Laravel route test response code: " . $response->getStatusCode() . "\n";
    
    if ($response->getStatusCode() === 500) {
        echo "❌ 500 error detected\n";
        // Let's check what the actual error is
        $content = $response->getContent();
        if (strpos($content, 'No query results for model') !== false) {
            echo "  Issue: Community model not found (slug doesn't exist)\n";
        } elseif (strpos($content, 'Class') !== false && strpos($content, 'not found') !== false) {
            echo "  Issue: Missing class/dependency\n";
        } else {
            echo "  Content preview: " . substr(strip_tags($content), 0, 200) . "...\n";
        }
    }
} catch (Exception $e) {
    echo "❌ Laravel route test failed: " . $e->getMessage() . "\n";
}
