<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$u = App\Models\User::where('email', 'qa-test@example.com')->first();
$u->forceFill([
    'email_verified_at' => now(),
    'password' => Illuminate\Support\Facades\Hash::make('password123'),
])->save();
$u->refresh();
echo 'verified: ' . ($u->email_verified_at ? 'YES' : 'no') . ' | password matches: '
    . (Illuminate\Support\Facades\Hash::check('password123', $u->password) ? 'YES' : 'no') . PHP_EOL;
