<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class DemoServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        if (! function_exists('demo_enabled') || ! demo_enabled()) {
            return;
        }

        // Dynamically override app.name when in demo mode
        $demoAppName = config('demo.branding.app_name');
        if (! empty($demoAppName)) {
            config(['app.name' => $demoAppName]);
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if (! function_exists('demo_enabled') || ! demo_enabled()) {
            return;
        }

        // Share $demo and $brand helpers with views only in demo mode
        View::composer('*', function ($view) {
            $view->with('demo', config('demo'));
            $view->with('brandConfig', config('demo.branding', []));
        });
    }
}
