<?php

namespace App\Http\Controllers\Demo;

use App\Enums\AccountStatus;
use App\Http\Controllers\Controller;
use App\Models\AdminProfile;
use App\Models\ParentProfile;
use App\Models\StudentProfile;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DemoLoginController extends Controller
{
    /**
     * Handle one-click direct login for demo accounts.
     * Route: POST /demo/login/{role}
     */
    public function directLogin(Request $request, string $role): JsonResponse|RedirectResponse
    {
        // 1. Ensure demo mode & direct login are enabled
        if (! function_exists('demo_enabled') || ! demo_enabled()) {
            abort(404, 'Not Found');
        }

        $directLoginConfig = config('demo.credentials.direct_login', []);
        if (empty($directLoginConfig['enabled'])) {
            abort(403, 'Direct login is disabled in demo configuration.');
        }

        // 2. Strict whitelist: only roles configured in demo.credentials.accounts
        $accounts = collect(config('demo.credentials.accounts', []));
        $account = $accounts->firstWhere('role', $role);

        if (! $account || empty($account['email'])) {
            abort(404, 'Invalid demo role.');
        }

        $targetEmail = strtolower(trim((string) $account['email']));

        // Security rule: Never allow support@trendk.com to be logged in via direct login
        if ($targetEmail === 'support@trendk.com' || str_contains($targetEmail, 'trendk.com')) {
            abort(403, 'Direct login for this account is prohibited.');
        }

        // 3. Find user or ensure demo account exists idempotently
        $user = User::where('email', $targetEmail)->first();

        if (! $user) {
            $user = $this->createDemoUserFallback($role, $targetEmail, (array) $account);
        }

        // Ensure user is approved
        if ($user->status !== AccountStatus::APPROVED) {
            $user->update(['status' => AccountStatus::APPROVED]);
        }

        // 4. Authenticate and regenerate session
        Auth::login($user, true);
        $request->session()->regenerate();

        // 5. Determine destination redirect
        $redirectUrl = match ($role) {
            'admin' => url('/admin'),
            'teacher' => route('teacher-portal'),
            'student' => route('student-portal'),
            'parent' => route('parent-portal'),
            default => url((string) ($account['redirect'] ?? '/')),
        };

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Welcome to Elite Academy Demo'),
                'redirect' => $redirectUrl,
            ]);
        }

        return redirect()->intended($redirectUrl);
    }

    /**
     * Safety fallback to provision the demo user if database hasn't been seeded yet.
     */
    protected function createDemoUserFallback(string $role, string $email, array $account): User
    {
        $defaultPassword = config('demo.credentials.password', 'Demo@12345');
        $label = is_array($account['label'] ?? null)
            ? ($account['label']['en'] ?? ucfirst($role))
            : (string) ($account['label'] ?? ucfirst($role));

        $user = User::create([
            'name' => "Demo {$label}",
            'email' => $email,
            'phone' => '+20109999000' . match ($role) {
                'admin' => '1',
                'teacher' => '2',
                'student' => '3',
                'parent' => '4',
                default => '0',
            },
            'password' => Hash::make($defaultPassword),
            'status' => AccountStatus::APPROVED,
            'email_verified_at' => now(),
        ]);

        match ($role) {
            'admin' => AdminProfile::firstOrCreate(['user_id' => $user->id]),
            'teacher' => TeacherProfile::firstOrCreate(['user_id' => $user->id], [
                'specialization' => 'Mathematics & Science',
                'bio' => 'Senior Instructor at Elite Academy Demo.',
            ]),
            'student' => StudentProfile::firstOrCreate(['user_id' => $user->id], [
                'student_code' => 'STU-DEMO-001',
                'school_name' => 'Elite Academy STEM High',
            ]),
            'parent' => ParentProfile::firstOrCreate(['user_id' => $user->id], [
                'relationship' => 'guardian',
            ]),
            default => null,
        };

        return $user;
    }
}
