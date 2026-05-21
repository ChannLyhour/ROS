<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

foreach (App\Models\User::all() as $user) {
    if ($user->role_id) {
        $role = Spatie\Permission\Models\Role::find($user->role_id);
        if ($role) {
            $user->syncRoles([$role->name]);
            echo "Synced {$user->email} to {$role->name}\n";
        }
    }
}
