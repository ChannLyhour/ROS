<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Comprehensive Permission Matrix matching the Sidebar Navigation
        $permissions = [
            'order-management' => ['view-orders', 'create-orders', 'edit-orders', 'delete-orders', 'void-orders'],
            'menu-management' => ['view-menu', 'create-menu', 'edit-menu', 'delete-menu'],
            'table-management' => ['view-tables', 'manage-tables'],
            'payment-management' => ['view-payments', 'refund-payments'],
            'staff-management' => ['view-staff', 'manage-staff'],
            'role-management' => ['view-roles', 'create-roles', 'edit-roles', 'delete-roles'],
            'reports-management' => ['view-reports'],
            'settings' => ['manage-settings', 'manage-translations'],
            
        ];

        foreach ($permissions as $group => $names) {
            foreach ($names as $name) {
                Permission::firstOrCreate([
                    'name' => $name,
                    'guard_name' => 'web',
                ]);
            }
        }

        // Create Roles and assign permissions

        // 1. Administrator: Has everything
        $admin = Role::firstOrCreate(
            ['name' => 'Administrator', 'guard_name' => 'web'],
            ['slug' => 'administrator', 'description' => 'Full Administrative System Access'],
        );
        $admin->syncPermissions(Permission::all());

        // 2. Cashier: Focused on Sales & Orders
        $cashier = Role::firstOrCreate(
            ['name' => 'Cashier', 'guard_name' => 'web'],
            ['slug' => 'cashier', 'description' => 'Primary focus on POS, Orders, and Payments']
        );
        $cashier->syncPermissions([
            'view-orders', 'create-orders', 'edit-orders',
            'view-payments', 
            'view-tables', 'manage-tables',
            'view-menu'
        ]);

        // 3. Kitchen: Focused on KDS and Menu viewing
        $kitchen = Role::firstOrCreate(
            ['name' => 'Kitchen', 'guard_name' => 'web'],
            ['slug' => 'kitchen', 'description' => 'Kitchen Display System and Order Fulfillment']
        );
        $kitchen->syncPermissions([
            'view-menu',
            'view-tables',
            'view-orders'
        ]);
    }
}
