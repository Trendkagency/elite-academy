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
        $this->app->singleton('files', function () {
            return new \App\Services\WindowsSafeFilesystem();
        });

        if (file_exists(app_path('helpers.php'))) {
            require_once app_path('helpers.php');
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(LiveSession::class, LiveSessionPolicy::class);

        // Role-Based Access Control Gates
        Gate::define('admin', fn (\App\Models\User $user) => $user->isAdmin());
        Gate::define('teacher', fn (\App\Models\User $user) => $user->isTeacher() || $user->isAdmin());
        Gate::define('student', fn (\App\Models\User $user) => $user->isStudent() || $user->isAdmin());
        Gate::define('parent', fn (\App\Models\User $user) => $user->isParent() || $user->isAdmin());

        // Granular Permission Gates
        foreach (\App\Permissions\PermissionsRegistry::all() as $permission) {
            Gate::define($permission, fn (\App\Models\User $user) => $user->hasPermission($permission));
        }

        if (! $this->app->runningInConsole() && $this->app->bound('request')) {
            $req = request();
            if (
                $this->app->environment('production', 'staging') ||
                $req->header('X-Forwarded-Proto') === 'https' ||
                $req->header('X-Forwarded-Ssl') === 'on' ||
                $req->secure() ||
                env('FORCE_HTTPS', true) ||
                str_contains(config('app.url'), 'https://')
            ) {
                URL::forceScheme('https');
            }
        } elseif (env('FORCE_HTTPS', true) || str_contains(config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }
    }
}
