<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$kitchenRole = \App\Models\Role::where('slug', 'kitchen')->first();
if ($kitchenRole) {
    $kitchenUser = \App\Models\User::updateOrCreate(
        ['email' => 'kitchen@ros.com'],
        [
            'name' => 'Kitchen Staff',
            'password' => bcrypt('password'),
            'role_id' => $kitchenRole->id,
            'state' => 'Active'
        ]
    );
    $kitchenUser->assignRole('Kitchen');
    echo "Kitchen user created.\n";
} else {
    echo "Kitchen role not found.\n";
}
