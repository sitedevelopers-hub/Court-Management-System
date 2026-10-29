<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

foreach (App\Models\User::take(8)->get(['id', 'email', 'name']) as $u) {
    echo $u->id . ' | ' . $u->email . ' | ' . $u->name . PHP_EOL;
}
