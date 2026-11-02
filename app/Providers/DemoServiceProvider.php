<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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

        // Safety Guard 1: Fake Mailer in Demo Mode
        if (config('demo.guards.block_external_calls', true)) {
            config(['mail.default' => 'log']);
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

        // Safety Guard 2: Block External HTTP Calls (FCM, Zoom, SMS Gateway, etc.)
        if (config('demo.guards.block_external_calls', true)) {
            Http::fake([
                'fcm.googleapis.com/*' => Http::response(['name' => 'demo-fake-fcm-msg', 'success' => 1], 200),
                'api.zoom.us/*' => Http::response(['id' => 'demo-zoom-84920481928', 'join_url' => 'https://zoom.us/j/demo'], 200),
                '*' => Http::response(['success' => true, 'demo' => true], 200),
            ]);
            Mail::fake();
        }

        // Safety Guard 3: Block Destructive Deletions on Primary Demo Accounts
        if (config('demo.guards.block_destructive', true)) {
            User::deleting(function (User $user) {
                $protectedEmails = [
                    'admin@demo.test',
                    'teacher@demo.test',
                    'student@demo.test',
                    'parent@demo.test',
                    'support@trendk.com',
                ];

                if (in_array(strtolower($user->email), $protectedEmails, true)) {
                    Log::warning("[DEMO GUARD] Blocked attempt to delete protected demo user: {$user->email}");
                    abort(403, 'Action prohibited in Demo mode: Protected demo accounts cannot be deleted.');
                }
            });
        }
    }
}
