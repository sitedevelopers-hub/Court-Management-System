<?php
// Quick check: does a known user exist with a known password?
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$users = App\Models\User::all(['id', 'name', 'email', 'email_verified_at']);
echo "User count: " . $users->count() . PHP_EOL;
foreach ($users as $u) {
    echo $u->id . ' | ' . $u->email . ' | verified: ' . ($u->email_verified_at ? 'yes' : 'no') . PHP_EOL;
    echo '  password "password" matches: ' . (Illuminate\Support\Facades\Hash::check('password', $u->password) ? 'YES' : 'no') . PHP_EOL;
}
