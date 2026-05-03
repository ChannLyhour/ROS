<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use App\Helper\SystemHelper;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // Implicitly grant "admin" role all permissions
        // This works with $user->can() checks
        Gate::before(function ($user, $ability) {
            return $user->role && $user->role->slug === 'admin' ? true : null;
        });

        View::composer('*', function ($view) {
            $view->with('appSettings', [
                'name' => SystemHelper::getSetting('business_name', 'ROS POS'),
                'currency' => SystemHelper::getSetting('currency_symbol', '$'),
                'email' => SystemHelper::getSetting('business_email', ''),
                'phone' => SystemHelper::getSetting('business_phone', ''),
                'address' => SystemHelper::getSetting('business_address', ''),
                'logo' => SystemHelper::getLogo(),
                'favicon' => SystemHelper::getFavicon(),
                'exchange_rate' => SystemHelper::getSetting('currency_exchange_rate', '4100'),
                'tax_percentage' => SystemHelper::getSetting('tax_percentage', '0'),
            ]);
        });
    }
}
