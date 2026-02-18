<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::capture();
$response = $kernel->handle($request);

$users = App\Models\User::all(['id', 'name', 'email', 'role']);
foreach ($users as $u) {
    echo "{$u->id} {$u->name} {$u->email} {$u->role}\n";
}
