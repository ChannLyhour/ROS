<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

// Create the new permission
$permission = Permission::firstOrCreate(['name' => 'view-kitchen', 'guard_name' => 'web']);

// Give to Administrator
$admin = Role::where('slug', 'administrator')->first();
if ($admin) {
    $admin->givePermissionTo($permission);
    echo "Gave view-kitchen to Administrator\n";
}

// Give to Kitchen
$kitchen = Role::where('slug', 'kitchen')->first();
if ($kitchen) {
    $kitchen->givePermissionTo($permission);
    echo "Gave view-kitchen to Kitchen\n";
} else {
    // Create Kitchen role if it was accidentally deleted
    $kitchen = Role::create(['name' => 'Kitchen', 'slug' => 'kitchen', 'guard_name' => 'web', 'description' => 'Kitchen Display System and Order Fulfillment']);
    $kitchen->syncPermissions(['view-menu', 'view-tables', 'view-orders', 'view-kitchen']);
    echo "Created Kitchen role and gave permissions\n";
}
