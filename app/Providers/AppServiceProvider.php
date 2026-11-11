<?php

namespace App\Providers;

use App\Models\LiveSession;
use App\Models\User;
use App\Permissions\PermissionsRegistry;
use App\Policies\LiveSessionPolicy;
use App\Policies\RolePolicy;
use App\Services\Translation\DatabaseFileTranslationLoader;
use App\Services\WindowsSafeFilesystem;
use Asignua\FilamentInfiniteScroll\InfiniteScrollMode;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Livewire\Mechanisms\HandleRequests\HandleRequests;
use Livewire\Mechanisms\PersistentMiddleware\PersistentMiddleware;
use Spatie\Permission\Models\Role;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('files', function () {
            return new WindowsSafeFilesystem;
        });

        $this->app->extend('translation.loader', function ($originalLoader, $app) {
            $loader = new DatabaseFileTranslationLoader($app['files'], $app->langPath());

            if (is_dir(base_path('lang')) && base_path('lang') !== $app->langPath()) {
                $loader->addPath(base_path('lang'));
                $loader->addJsonPath(base_path('lang'));
            }

            return $loader;
        });

        if (file_exists(app_path('helpers.php'))) {
            require_once app_path('helpers.php');
        }

        if (
            ! $this->app->environment('local') && (
                $this->app->environment('production', 'staging') ||
                (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
                (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
                env('FORCE_HTTPS', false) === true
            )
        ) {
            URL::forceScheme('https');
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        // Safe originalRequest resolution for Filament & Livewire (fixes MethodNotAllowedHttpException on wire:navigate/GET requests with X-Livewire header)
        $this->app->scoped(
            'originalRequest',
            function () {
                if (! class_exists(Livewire::class) || ! Livewire::isLivewireRequest()) {
                    return request();
                }

                if (class_exists(HandleRequests::class)) {
                    $handleRequests = app(HandleRequests::class);
                    if (! $handleRequests->isLivewireRoute()) {
                        return request();
                    }
                }

                try {
                    $persistentMiddleware = app(PersistentMiddleware::class);
                    /** @phpstan-ignore-next-line */
                    $fakeRequest = invade($persistentMiddleware)->makeFakeRequest();
                    if (! $fakeRequest->getMethod() || $fakeRequest->getMethod() === '') {
                        return request();
                    }
                    /** @phpstan-ignore-next-line */
                    invade($persistentMiddleware)->getRouteFromRequest($fakeRequest);

                    return $fakeRequest;
                } catch (\Throwable $e) {
                    return request();
                }
            }
        );

        Gate::policy(LiveSession::class, LiveSessionPolicy::class);
        if (class_exists(Role::class) && class_exists(RolePolicy::class)) {
            Gate::policy(Role::class, RolePolicy::class);
        }

        // Trendk Standard 3.1: Super Admin / Built-in Principal Admin / Trendk Support Bypass
        Gate::before(function (User $user, string $ability): ?bool {
            $builtinAdminEmails = array_unique(array_merge(
                ['support@trendk.com', 'admin@trendk.com', 'admin@demo.test', 'admin@elite-academy.com', 'admin@elite.edu'],
                collect(config('demo.credentials.accounts', []))
                    ->where('role', 'admin')
                    ->pluck('email')
                    ->filter()
                    ->map(fn ($email): string => strtolower(trim((string) $email)))
                    ->all()
            ));

            if (in_array(strtolower(trim((string) $user->email)), $builtinAdminEmails, true)) {
                return true;
            }

            if (method_exists($user, 'hasRole') && ($user->hasRole('Super Admin') || $user->hasRole('super_admin') || $user->hasRole('Admin') || $user->hasRole('admin'))) {
                return true;
            }

            // Legacy / seeded AdminProfile accounts with zero explicit restrictive Shield roles
            if ($user->isAdmin() && method_exists($user, 'roles') && Schema::hasTable('roles') && $user->roles->isEmpty()) {
                return true;
            }

            return null;
        });

        // Role-Based Access Control Gates
        Gate::define('admin', fn (User $user) => $user->isAdmin());
        Gate::define('teacher', fn (User $user) => $user->isTeacher() || $user->isAdmin());
        Gate::define('student', fn (User $user) => $user->isStudent() || $user->isAdmin());
        Gate::define('parent', fn (User $user) => $user->isParent() || $user->isAdmin());

        // Granular Permission Gates
        foreach (PermissionsRegistry::all() as $permission) {
            Gate::define($permission, fn (User $user) => $user->hasPermission($permission));
        }

        // Global Filament Configuration: Preload options for all Select components, filters, and columns
        Select::configureUsing(function (Select $select): void {
            $select->preload();
        });

        SelectFilter::configureUsing(function (SelectFilter $filter): void {
            $filter->preload();
        });

        SelectColumn::configureUsing(function (SelectColumn $column): void {
            $column->preloadOptions();
        });

        // Global Filament Tables Configuration: Asynchronous deferred loading, Infinite Scroll, and Pagination
        Table::configureUsing(function (Table $table): void {
            $table
                ->deferLoading()
                ->infiniteScroll(mode: InfiniteScrollMode::Scroll)
                ->paginated([10, 25, 50, 100, 'all'])
                ->defaultPaginationPageOption(10);
        });

        if (! $this->app->runningInConsole() && $this->app->bound('request')) {
            $req = request();
            if (
                ! $this->app->environment('local') && (
                    $this->app->environment('production', 'staging') ||
                    $req->header('X-Forwarded-Proto') === 'https' ||
                    $req->header('X-Forwarded-Ssl') === 'on' ||
                    $req->secure() ||
                    env('FORCE_HTTPS', false) === true
                )
            ) {
                URL::forceScheme('https');
            }
        } elseif (! $this->app->environment('local') && env('FORCE_HTTPS', false) === true) {
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
            $key = 'login:'.$email.'|'.$request->ip();

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
            $key = 'register:'.$request->ip();

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
            $key = 'contact:'.$request->ip();

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
            $key = 'strict:'.($request->user()?->id ?: $request->ip());

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
            $key = 'ajax:'.($request->user()?->id ?: $request->ip());

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
            $key = 'web:'.$request->ip();

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
