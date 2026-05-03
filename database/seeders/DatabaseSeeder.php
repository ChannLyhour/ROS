<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Roles
        $adminRole = \App\Models\Role::firstOrCreate(
            ['slug' => 'admin'],
            ['name' => 'Admin', 'description' => 'System Administrator']
        );

        $cashierRole = \App\Models\Role::firstOrCreate(
            ['slug' => 'cashier'],
            ['name' => 'Cashier', 'description' => 'Front desk staff']
        );

        // Users
        User::updateOrCreate(
            ['email' => 'admin@ros.com'],
            [
                'name' => 'Admin Staff',
                'password' => bcrypt('password'),
                'role_id' => $adminRole->id,
                'state' => 'Active'
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin2@ros.com'],
            [
                'name' => 'Admin 2',
                'password' => bcrypt('password'),
                'role_id' => $adminRole->id,
                'state' => 'Active'
            ]
        );

        // Categories & Menu Items
        $this->call([
            CategorySeeder::class,
            MenuItemSeeder::class,
        ]);

        // Tables
        \App\Models\Table::firstOrCreate(['name' => 'Table 1'], ['capacity' => 2, 'status' => 'available']);
        \App\Models\Table::firstOrCreate(['name' => 'Table 2'], ['capacity' => 4, 'status' => 'available']);
        \App\Models\Table::firstOrCreate(['name' => 'Table 3'], ['capacity' => 6, 'status' => 'available']);

        // Settings
        $settings = [
            'business_name' => 'The Grand Restaurant',
            'business_email' => 'info@grandrest.com',
            'business_phone' => '+855 012 345 678',
            'business_address' => 'Phnom Penh, Cambodia',
            'tax_percentage' => '10',
            'currency_symbol' => '$',
            'exchange_rate' => '4100', // 1 USD = 4100 KHR
            'business_logo' => null,
            'business_favicon' => null,
        ];

        foreach ($settings as $key => $value) {
            \App\Models\Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        // Translations
        $this->call(TranslationSeeder::class);
    }
}
