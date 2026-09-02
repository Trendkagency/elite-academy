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
     * Handle an incoming request to Web / Filament Admin panel.
     * Ensure standard admin accounts (admin@elite.edu & admin@elite-academy.com) exist with AdminProfile.
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            if (Schema::hasTable('users') && Schema::hasTable('admin_profiles')) {
                $defaultAdmins = [
                    [
                        'email' => 'admin@elite.edu',
                        'name' => 'المدير العام — System Admin',
                        'phone' => '+201000000001',
                        'password' => 'password',
                    ],
                    [
                        'email' => 'admin@elite-academy.com',
                        'name' => 'System Administrator',
                        'phone' => '+201000000002',
                        'password' => 'password',
                    ],
                ];

                foreach ($defaultAdmins as $adminData) {
                    $user = User::firstOrCreate(
                        ['email' => $adminData['email']],
                        [
                            'name' => $adminData['name'],
                            'phone' => $adminData['phone'],
                            'password' => Hash::make($adminData['password']),
                            'status' => AccountStatus::APPROVED,
                            'email_verified_at' => now(),
                        ]
                    );

                    // Ensure status is approved & has AdminProfile
                    if ($user->status !== AccountStatus::APPROVED && $user->status !== 'approved') {
                        $user->update(['status' => AccountStatus::APPROVED]);
                    }

                    AdminProfile::firstOrCreate(['user_id' => $user->id]);
                }
            }
        } catch (\Throwable $e) {
            // Silently log errors
            report($e);
        }

        return $next($request);
    }
}
