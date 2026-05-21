<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$role = Spatie\Permission\Models\Role::where('name', 'Kitchen')->first();
$role->syncPermissions(['view-menu', 'view-tables', 'view-orders', 'view-kitchen']);
echo json_encode($role->permissions->pluck('name'));
