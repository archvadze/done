<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$user = User::where('email', 'anna.weber@artcraft.ge')->first();

if ($user) {
    $user->password = bcrypt('artist123');
    $user->save();
    echo "Password updated successfully for Anna Weber!\n";
} else {
    echo "User not found!\n";
}
