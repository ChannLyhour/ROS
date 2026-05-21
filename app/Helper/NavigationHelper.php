<?php

namespace App\Helper;

class NavigationHelper
{
    /**
     * Get the sidebar menu items.
     *
     * @return array
     */
    public static function getSidebarMenu(): array
    {
        $user = auth()->user();
        if (!$user) return [];

        return [
            [
                'header' => 'MAIN',
                'items' => [
                    [
                        'label' => 'Dashboard',
                        'route' => 'home',
                        'icon' => 'layout-dashboard',
                        'activePattern' => 'home',
                    ],
                ],
            ],
            [
                'header' => 'MANAGEMENT',
                'visible' => $user->can('view-menu') || $user->can('view-tables'),
                'items' => [
                    [
                        'label' => 'Categories',
                        'route' => 'categories.index',
                        'icon' => 'grid',
                        'activePattern' => 'categories.*',
                        'visible' => $user->can('view-menu'),
                    ],
                    [
                        'label' => 'Menu Items',
                        'route' => 'menu.index',
                        'icon' => 'utensils-crossed',
                        'activePattern' => 'menu.*',
                        'visible' => $user->can('view-menu'),
                    ],
                    [
                        'label' => 'Tables',
                        'route' => 'tables.index',
                        'icon' => 'table',
                        'activePattern' => 'tables.*',
                        'visible' => $user->can('view-tables'),
                    ],
                ],
            ],
            [
                'header' => 'SALES & ORDERS',
                'visible' => $user->can('view-orders') || $user->can('view-payments'),
                'items' => [
                    [
                        'label' => 'Orders',
                        'route' => 'orders.index',
                        'icon' => 'shopping-cart',
                        'activePattern' => 'orders.*',
                        'visible' => $user->can('view-orders'),
                    ],
                    [
                        'label' => 'Payments',
                        'route' => 'payments.index',
                        'icon' => 'banknote',
                        'activePattern' => 'payments.*',
                        'visible' => $user->can('view-payments'),
                    ],
                    [
                        'label' => 'Kitchen KDS',
                        'route' => 'kitchen.index',
                        'icon' => 'flame',
                        'activePattern' => 'kitchen.*',
                        'visible' => $user->can('view-kitchen'),
                    ],
                ],
            ],
            [
                'header' => 'REPORTS',
                'visible' => $user->can('view-reports'),
                'items' => [
                    [
                        'label' => 'Income Report',
                        'route' => 'reports.income',
                        'icon' => 'bar-chart-3',
                        'activePattern' => 'reports.income',
                    ],
                ],
            ],
            [
                'header' => 'SYSTEM',
                'items' => [
                    [
                        'label' => 'Profile',
                        'route' => 'profile.index',
                        'icon' => 'user-cog',
                        'activePattern' => 'profile.*',
                    ],
                    [
                        'label' => 'Staff Management',
                        'route' => 'users.index',
                        'icon' => 'user-plus',
                        'activePattern' => 'users.*',
                        'visible' => $user->can('view-staff'),
                    ],
                    [
                        'label' => 'Roles',
                        'route' => 'roles.index',
                        'icon' => 'shield-check',
                        'activePattern' => 'roles.*',
                        'visible' => $user->can('view-roles'),
                    ],
                    [
                        'label' => 'Currency Symbol',
                        'route' => 'currencies.index',
                        'icon' => 'banknote',
                        'activePattern' => 'currencies.*',
                        'visible' => $user->can('manage-settings'),
                    ],
                    [
                        'label' => 'Translations',
                        'route' => 'translations.index',
                        'icon' => 'languages',
                        'activePattern' => 'translations.*',
                        'visible' => $user->can('manage-translations'),
                    ],
                    [
                        'label' => 'Settings',
                        'route' => 'settings.index',
                        'icon' => 'settings',
                        'activePattern' => 'settings.*',
                        'visible' => $user->can('manage-settings'),
                    ],
                    [
                        'label' => 'Backups',
                        'route' => 'backups.index',
                        'icon' => 'database',
                        'activePattern' => 'backups.*',
                        'visible' => $user->can('manage-settings'),
                    ],
                    [
                        'label' => 'Logout',
                        'route' => 'logout',
                        'icon' => 'log-out',
                        'class' => 'text-danger',
                        'special' => 'logout',
                        'activePattern' => '',
                    ],
                ],
            ],
        ];
    }
}
