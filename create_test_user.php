<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$user = User::firstOrCreate(
    ['email' => 'anna.weber@artcraft.ge'],
    [
        'name' => 'Anna Weber',
        'email' => 'anna.weber@artcraft.ge',
        'password' => bcrypt('artist123'),
        'email_verified_at' => now(),
        'role' => 'artist',
        'status' => 'active',
    ]
);

echo "User created/updated successfully!\n";
echo "Email: " . $user->email . "\n";
echo "Name: " . $user->name . "\n";
