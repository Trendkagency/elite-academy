<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\GradeLevel;
use App\Models\ParentStudentLink;
use App\Models\StudentPackage;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class StudentProfileController extends Controller
{
    /**
     * Display the student profile management dashboard.
     */
    public function show(Request $request): View
    {
        /** @var User $user */
        $user = auth()->user();

        // Ensure student profile record exists
        $profile = StudentProfile::firstOrCreate(
            ['user_id' => $user->id],
            [
                'grade_level_id' => null,
                'school_name' => null,
                'date_of_birth' => null,
            ]
        );

        $gradeLevels = GradeLevel::where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->get();

        $activePackage = StudentPackage::where('student_user_id', $user->id)
            ->with(['packageTemplate', 'course'])
            ->where('status', 'active')
            ->latest()
            ->first();

        $packageTransactions = $activePackage
            ? $activePackage->transactions()->latest()->take(5)->get()
            : collect();

        $parentLinks = \Illuminate\Support\Facades\DB::table('parent_student')
            ->where('student_user_id', $user->id)
            ->get();
        $parentUserIds = $parentLinks->pluck('parent_user_id')->toArray();
        $parents = User::whereIn('id', $parentUserIds)->get();

        return view('pages.student-profile', [
            'user' => $user,
            'profile' => $profile,
            'gradeLevels' => $gradeLevels,
            'activePackage' => $activePackage,
            'packageTransactions' => $packageTransactions,
            'parents' => $parents,
        ]);
    }

    /**
     * Update the student personal and academic details.
     */
    public function update(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = auth()->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'grade_level_id' => ['nullable', 'exists:grade_levels,id'],
            'school_name' => ['nullable', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        // Update User model
        $user->update([
            'name' => $validated['name'],
            'phone' => $validated['phone'] ?? $user->phone,
        ]);

        // Update or Create StudentProfile model
        $profile = StudentProfile::firstOrCreate(['user_id' => $user->id]);

        $profileData = [
            'grade_level_id' => $validated['grade_level_id'] ?? $profile->grade_level_id,
            'school_name' => $validated['school_name'] ?? $profile->school_name,
            'date_of_birth' => $validated['date_of_birth'] ?? $profile->date_of_birth,
        ];

        // Handle Avatar File Upload
        if ($request->hasFile('avatar')) {
            if ($profile->avatar && Storage::disk('public')->exists($profile->avatar)) {
                Storage::disk('public')->delete($profile->avatar);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $profileData['avatar'] = $path;
        }

        $profile->update($profileData);

        return back()->with('success', app()->getLocale() === 'ar' 
            ? 'تم تحديث بيانات الملف الشخصي بنجاح!' 
            : 'Student profile details updated successfully!');
    }

    /**
     * Update the student account password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', Password::min(8)->mixedCase()->numbers(), 'confirmed'],
        ]);

        /** @var User $user */
        $user = auth()->user();
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', app()->getLocale() === 'ar' 
            ? 'تم تغيير كلمة المرور بنجاح!' 
            : 'Account password updated successfully!');
    }
}
