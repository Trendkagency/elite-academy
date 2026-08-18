<?php

namespace App\Providers;

use App\Models\LiveSession;
use App\Policies\LiveSessionPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

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
        Gate::policy(LiveSession::class, LiveSessionPolicy::class);

        if (
            $this->app->environment('production', 'staging') ||
            request()->header('X-Forwarded-Proto') === 'https' ||
            request()->header('X-Forwarded-Ssl') === 'on' ||
            request()->secure() ||
            env('FORCE_HTTPS', true) ||
            str_contains(config('app.url'), 'https://')
        ) {
            URL::forceScheme('https');
        }
    }
}
