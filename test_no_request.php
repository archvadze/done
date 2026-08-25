<?php

// Test loading Laravel without any request handling
require_once __DIR__ . '/vendor/autoload.php';

echo "Step 1: Autoloader loaded\n";

$app = require_once __DIR__ . '/bootstrap/app.php';

echo "Step 2: App loaded\n";

// Don't create kernel or handle requests - just test basic app
echo "Step 3: App class: " . get_class($app) . "\n";

// Test config without request
$config = $app->make('config');
echo "Step 4: Config loaded\n";

// Test without creating view service
echo "Done - Laravel app loads but request handling is the issue\n";
