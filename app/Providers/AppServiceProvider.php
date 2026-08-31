<?php

namespace App\Providers;

use App\Models\LiveSession;
use App\Policies\LiveSessionPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
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
        $this->configureRateLimiting();

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

        // Global Filament Configuration: Preload options for all Select components, filters, and columns
        \Filament\Forms\Components\Select::configureUsing(function (\Filament\Forms\Components\Select $select): void {
            $select->preload();
        });

        \Filament\Tables\Filters\SelectFilter::configureUsing(function (\Filament\Tables\Filters\SelectFilter $filter): void {
            $filter->preload();
        });

        \Filament\Tables\Columns\SelectColumn::configureUsing(function (\Filament\Tables\Columns\SelectColumn $column): void {
            $column->preloadOptions();
        });

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

    /**
     * Configure enterprise-grade Rate Limiters for DDoS, Brute Force & Request Flooding Protection.
     */
    protected function configureRateLimiting(): void
    {
        // 1. Login Rate Limiter (Brute Force & Credential Stuffing Protection - 5 attempts/min)
        RateLimiter::for('login', function (Request $request) {
            $email = mb_strtolower(trim((string) $request->input('email', '')));
            $key = 'login:' . $email . '|' . $request->ip();

            return Limit::perMinute(5)->by($key)->response(function (Request $request, array $headers) {
                $seconds = (int) ($headers['Retry-After'] ?? 60);
                return response()->json([
                    'success' => false,
                    'message' => __('Too many login attempts. Please try again in :seconds seconds.', ['seconds' => $seconds]),
                    'retry_after' => $seconds,
                ], 429, $headers);
            });
        });

        // 2. Register Rate Limiter (Bot Registration & Account Spam Prevention - 5 attempts/10mins)
        RateLimiter::for('register', function (Request $request) {
            $key = 'register:' . $request->ip();

            return Limit::perMinutes(10, 5)->by($key)->response(function (Request $request, array $headers) {
                $seconds = (int) ($headers['Retry-After'] ?? 600);
                return response()->json([
                    'success' => false,
                    'message' => __('Too many registration attempts from your IP. Please try again later.'),
                    'retry_after' => $seconds,
                ], 429, $headers);
            });
        });

        // 3. Contact Form Rate Limiter (Form Spam & Mail Bombing Protection - 3 attempts/10mins)
        RateLimiter::for('contact', function (Request $request) {
            $key = 'contact:' . $request->ip();

            return Limit::perMinutes(10, 3)->by($key)->response(function (Request $request, array $headers) {
                $seconds = (int) ($headers['Retry-After'] ?? 600);
                return response()->json([
                    'success' => false,
                    'message' => __('Too many contact form submissions. Please wait a few minutes before trying again.'),
                    'retry_after' => $seconds,
                ], 429, $headers);
            });
        });

        // 4. Strict Actions Rate Limiter (Enrollments, Submissions, Exceptions, Child Linking - 10 attempts/min)
        RateLimiter::for('strict_actions', function (Request $request) {
            $key = 'strict:' . ($request->user()?->id ?: $request->ip());

            return Limit::perMinute(10)->by($key)->response(function (Request $request, array $headers) {
                $seconds = (int) ($headers['Retry-After'] ?? 60);
                return response()->json([
                    'success' => false,
                    'message' => __('Action rate limit exceeded. Please wait before attempting this operation again.'),
                    'retry_after' => $seconds,
                ], 429, $headers);
            });
        });

        // 5. Interactive AJAX Rate Limiter (Draft Auto-Save, Step Updates, Notifications - 60 attempts/min)
        RateLimiter::for('ajax_interactive', function (Request $request) {
            $key = 'ajax:' . ($request->user()?->id ?: $request->ip());

            return Limit::perMinute(60)->by($key)->response(function (Request $request, array $headers) {
                $seconds = (int) ($headers['Retry-After'] ?? 60);
                return response()->json([
                    'success' => false,
                    'message' => __('High request frequency detected. Please slow down.'),
                    'retry_after' => $seconds,
                ], 429, $headers);
            });
        });

        // 6. Global Web DDoS Protection (Public Route Flooding Protection - 120 attempts/min)
        RateLimiter::for('global_web', function (Request $request) {
            $key = 'web:' . $request->ip();

            return Limit::perMinute(120)->by($key)->response(function (Request $request, array $headers) {
                $seconds = (int) ($headers['Retry-After'] ?? 60);
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => __('System rate limit exceeded. Connection throttled for DDoS protection.'),
                        'retry_after' => $seconds,
                    ], 429, $headers);
                }
                return response()->view('errors.429', ['retryAfter' => $seconds], 429, $headers);
            });
        });
    }
}
