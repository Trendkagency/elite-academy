<?php

namespace App\Http\Middleware;

use App\Enums\AccountStatus;
use App\Models\AdminProfile;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminAccountExists
{
    /**
     * Handle an incoming request to Filament Admin panel.
     * Ensure at least one system admin account exists; if not, create the default admin account.
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            if (Schema::hasTable('users') && Schema::hasTable('admin_profiles')) {
                $hasAdmin = User::whereHas('adminProfile')
                    ->orWhere('email', 'admin@elite-academy.com')
                    ->orWhere('email', 'admin@elite.edu')
                    ->orWhere('email', config('app.admin_email', env('ADMIN_DEFAULT_EMAIL', 'admin@elite-academy.com')))
                    ->exists();

                if (! $hasAdmin) {
                    $email = env('ADMIN_DEFAULT_EMAIL', 'admin@elite-academy.com');
                    $password = env('ADMIN_DEFAULT_PASSWORD', 'password');
                    $name = env('ADMIN_DEFAULT_NAME', 'System Administrator');
                    $phone = env('ADMIN_DEFAULT_PHONE', '+201000000001');

                    $admin = User::firstOrCreate(
                        ['email' => $email],
                        [
                            'name' => $name,
                            'phone' => $phone,
                            'password' => Hash::make($password),
                            'status' => AccountStatus::APPROVED,
                            'email_verified_at' => now(),
                        ]
                    );

                    AdminProfile::firstOrCreate(['user_id' => $admin->id]);
                }
            }
        } catch (\Throwable $e) {
            // Silently log database errors (e.g. during early initialization)
            report($e);
        }

        return $next($request);
    }
}
