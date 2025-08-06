<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

echo "=== AVAILABLE TEST USERS ===\n";

try {
    $users = $app->make('App\Models\User')->select('id', 'name', 'email', 'role', 'status')->get();
    
    echo "You can log in with any of these users:\n\n";
    foreach ($users as $user) {
        echo "Email: {$user->email}\n";
        echo "Name: {$user->name}\n";
        echo "Role: {$user->role}\n";
        echo "Status: {$user->status}\n";
        echo "---\n";
    }
    
    echo "\nNOTE: You'll need to know the password for these accounts.\n";
    echo "If this is a development environment, passwords might be 'password' or 'secret'.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
