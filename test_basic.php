<?php

// Very basic test - just load Laravel without views
require_once __DIR__ . '/vendor/autoload.php';

echo "Step 1: Autoloader loaded\n";

$app = require_once __DIR__ . '/bootstrap/app.php';

echo "Step 2: App loaded\n";

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "Step 3: Kernel created\n";

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "Step 4: Request handled\n";

// Test if the app container works
$config = app('config');
echo "Step 5: Config service available\n";

// Test if view is the issue
echo "Step 6: About to access view service\n";
$view = app('view');
echo "Step 7: View service created\n";

echo "Done!\n";
