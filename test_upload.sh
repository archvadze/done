#!/bin/bash

# Test file upload and storage functionality
echo "Testing File Upload Service..."

# Create a test image file
cd /var/www/done
echo "Creating test file..."
cp storage/app/public/artworks/2025/07/25/xAL4g7iv7jV6A0AU8StOxEx8EUbLgzoIykcpj51j_thumb.jpg storage/test_image.jpg

# Test the file upload service directly
php -r "
require 'vendor/autoload.php';
\$app = require 'bootstrap/app.php';
\$kernel = \$app->make('Illuminate\Contracts\Console\Kernel');
\$kernel->bootstrap();

// Test file creation
\$testFile = new \Illuminate\Http\UploadedFile(
    'storage/test_image.jpg',
    'test.jpg',
    'image/jpeg',
    null,
    true
);

\$service = app(\App\Services\FileUploadService::class);
\$user = \App\Models\User::first();

try {
    echo 'Starting file upload test...' . PHP_EOL;
    \$filePath = \$service->generateFilePath('test.jpg');
    echo 'Generated path: ' . \$filePath . PHP_EOL;

    // Test file storage
    \$storedPath = \$testFile->storeAs('public/' . dirname(\$filePath), basename(\$filePath));
    echo 'Stored path: ' . \$storedPath . PHP_EOL;

    // Check if file exists
    if(Storage::disk('public')->exists(\$filePath)) {
        echo 'SUCCESS: File was stored correctly!' . PHP_EOL;
    } else {
        echo 'ERROR: File was not stored!' . PHP_EOL;
    }

} catch (Exception \$e) {
    echo 'ERROR: ' . \$e->getMessage() . PHP_EOL;
}
"

echo "Test completed."
