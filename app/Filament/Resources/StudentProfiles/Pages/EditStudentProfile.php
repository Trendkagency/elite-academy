<?php

namespace App\Filament\Resources\StudentProfiles\Pages;

use App\Enums\AccountStatus;
use App\Filament\Resources\StudentProfiles\StudentProfileResource;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\FileUpload;
use App\Models\PackageTemplate;
use App\Models\ParentProfile;
use App\Models\StudentPackage;
use App\Models\TeacherProfile;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload as FormFileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Grid;
use Filament\Support\Enums\Width;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\HtmlString;

class EditStudentProfile extends EditRecord
{
    protected static string $resource = StudentProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('approveStudent')
                ->label(__('Approve Student / تفعيل الحساب'))
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->action(function () {
                    $this->record->user?->update(['status' => AccountStatus::APPROVED]);
                    Notification::make()->title(__('Student Account Approved / تم تفعيل الحساب'))->success()->send();
                })
                ->visible(fn () => $this->record->user?->status !== AccountStatus::APPROVED && $this->record->user?->status !== 'approved'),

            Action::make('rejectStudent')
                ->label(__('Reject Student / رفض الحساب'))
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->user?->update(['status' => AccountStatus::REJECTED]);
                    Notification::make()->title(__('Student Account Rejected / تم رفض الحساب'))->warning()->send();
                })
                ->visible(fn () => $this->record->user?->status === AccountStatus::PENDING || $this->record->user?->status === 'pending'),

            // 1. Parent Management Actions
            Action::make('linkParent')
                ->label(__('Link Existing Parent / ربط ولي أمر'))
                ->modalHeading(__('Link Parent to Student / ربط ولي أمر بالطالب'))
                ->modalDescription(__('Connect an existing parent user account to this student / ربط حساب ولي أمر مسجل مسبقاً بهذا الطالب'))
                ->icon('heroicon-o-link')
                ->color('info')
                ->form([
                    Select::make('parent_user_id')
                        ->label(__('Parent Account / حساب ولي الأمر'))
                        ->placeholder(__('Search and select parent... / اختر ولي الأمر...'))
                        ->options(function () {
                            $alreadyLinked = DB::table('parent_student')
                                ->where('student_user_id', $this->record->user_id)
                                ->pluck('parent_user_id')
                                ->toArray();

                            return User::query()
                                ->whereNotIn('id', $alreadyLinked)
                                ->where(function ($q) {
                                    $q->whereHas('parentProfile')
                                      ->orWhereDoesntHave('studentProfile');
                                })
                                ->orderBy('name')
                                ->get()
                                ->mapWithKeys(function ($u) {
                                    $name = $u->name ?: 'User #' . $u->id;
                                    $email = $u->email ? ' (' . $u->email . ')' : '';
                                    $phone = $u->phone ? ' - ' . $u->phone : '';
                                    return [$u->id => $name . $email . $phone];
                                })
                                ->toArray();
                        })
                        ->searchable()
                        ->preload()
                        ->required(),

                    Select::make('relationship')
                        ->label(__('Relationship / صلة القرابة'))
                        ->options([
                            'father' => __('Father / الأب'),
                            'mother' => __('Mother / الأم'),
                            'guardian' => __('Guardian / ولي الأمر / الوصي'),
                            'other' => __('Other Relative / قريب آخر'),
                        ])
                        ->default('father')
                        ->required(),

                    Toggle::make('is_primary')
                        ->label(__('Primary Contact / جهة الاتصال الأساسية'))
                        ->default(true),
                ])
                ->action(function (array $data) {
                    $parentUserId = $data['parent_user_id'];
                    $studentUserId = $this->record->user_id;

                    ParentProfile::withTrashed()->firstOrCreate(['user_id' => $parentUserId]);
                    $profile = ParentProfile::withTrashed()->where('user_id', $parentUserId)->first();
                    if ($profile && $profile->trashed()) {
                        $profile->restore();
                    }

                    DB::table('parent_student')->updateOrInsert(
                        [
                            'parent_user_id' => $parentUserId,
                            'student_user_id' => $studentUserId,
                        ],
                        [
                            'relationship' => $data['relationship'] ?? 'guardian',
                            'is_primary' => (bool) ($data['is_primary'] ?? false),
                            'updated_at' => now(),
                            'created_at' => now(),
                        ]
                    );

                    if (!empty($data['is_primary'])) {
                        DB::table('parent_student')
                            ->where('student_user_id', $studentUserId)
                            ->where('parent_user_id', '<>', $parentUserId)
                            ->update(['is_primary' => false]);
                    }

                    Notification::make()
                        ->title(__('Parent account linked successfully / تم ربط ولي الأمر بالطالب بنجاح'))
                        ->success()
                        ->send();
                }),

            Action::make('createAndLinkParent')
                ->label(__('Create & Link New Parent / إضافة ولي أمر جديد وربطه'))
                ->modalHeading(__('Create New Parent & Link / إنشاء حساب ولي أمر جديد وربطه'))
                ->modalDescription(__('Create account credentials for the parent and configure kinship relation with student. / إنشاء بيانات ولي الأمر وتحديد صلة القرابة.'))
                ->modalWidth(Width::TwoExtraLarge)
                ->modalSubmitActionLabel(__('Create & Link Parent / حفظ وربط ولي الأمر'))
                ->modalCancelActionLabel(__('Cancel / إلغاء'))
                ->icon('heroicon-o-user-plus')
                ->color('success')
                ->form([
                    Grid::make(['default' => 1, 'sm' => 2])
                        ->schema([
                            TextInput::make('name')
                                ->label(__('Parent Name / اسم ولي الأمر'))
                                ->placeholder(__('Full Name / الاسم ثلاثي'))
                                ->required()
                                ->maxLength(100),

                            TextInput::make('email')
                                ->label(__('Email / البريد الإلكتروني'))
                                ->placeholder(__('example@domain.com'))
                                ->email()
                                ->required()
                                ->maxLength(255),
                        ]),

                    Grid::make(['default' => 1, 'sm' => 2])
                        ->schema([
                            TextInput::make('phone')
                                ->label(__('Phone Number / رقم الهاتف'))
                                ->placeholder(__('e.g. +2010XXXXXXXX'))
                                ->tel()
                                ->nullable()
                                ->maxLength(30),

                            TextInput::make('password')
                                ->label(__('Password / كلمة المرور'))
                                ->password()
                                ->revealable()
                                ->default('Parent@123456')
                                ->helperText(__('Default password provided, can be changed later / كلمة مرور مؤقتة قابلة للتغيير'))
                                ->required(),
                        ]),

                    Grid::make(['default' => 1, 'sm' => 2])
                        ->schema([
                            Select::make('relationship')
                                ->label(__('Relationship / صلة القرابة'))
                                ->options([
                                    'father' => '👨‍👦 ' . __('Father / الأب'),
                                    'mother' => '👩‍👦 ' . __('Mother / الأم'),
                                    'guardian' => '🤝 ' . __('Guardian / ولي الأمر / الوصي'),
                                    'other' => '👤 ' . __('Other / غير ذلك'),
                                ])
                                ->default('father')
                                ->required(),

                            Toggle::make('is_primary')
                                ->label(__('Primary Contact / جهة الاتصال الأساسية للطالب'))
                                ->default(true)
                                ->inline(false),
                        ]),
                ])
                ->action(function (array $data) {
                    $email = trim(strtolower($data['email']));
                    $studentUserId = $this->record->user_id;

                    $parentUser = User::where('email', $email)->first();

                    if (! $parentUser) {
                        $parentUser = User::create([
                            'name' => $data['name'],
                            'email' => $email,
                            'phone' => $data['phone'] ?? null,
                            'password' => Hash::make($data['password']),
                            'status' => AccountStatus::APPROVED,
                        ]);
                    } else {
                        $updateData = [];
                        if (!empty($data['name']) && empty($parentUser->name)) {
                            $updateData['name'] = $data['name'];
                        }
                        if (!empty($data['phone']) && empty($parentUser->phone)) {
                            $updateData['phone'] = $data['phone'];
                        }
                        if ($parentUser->status !== AccountStatus::APPROVED) {
                            $updateData['status'] = AccountStatus::APPROVED;
                        }
                        if (!empty($updateData)) {
                            $parentUser->update($updateData);
                        }
                    }

                    // Guarantee ParentProfile exists and is not trashed
                    ParentProfile::withTrashed()->firstOrCreate(['user_id' => $parentUser->id]);
                    $profile = ParentProfile::withTrashed()->where('user_id', $parentUser->id)->first();
                    if ($profile && $profile->trashed()) {
                        $profile->restore();
                    }

                    $alreadyLinked = DB::table('parent_student')
                        ->where('parent_user_id', $parentUser->id)
                        ->where('student_user_id', $studentUserId)
                        ->exists();

                    DB::table('parent_student')->updateOrInsert(
                        [
                            'parent_user_id' => $parentUser->id,
                            'student_user_id' => $studentUserId,
                        ],
                        [
                            'relationship' => $data['relationship'] ?? 'guardian',
                            'is_primary' => (bool) ($data['is_primary'] ?? false),
                            'updated_at' => now(),
                            'created_at' => now(),
                        ]
                    );

                    if (!empty($data['is_primary'])) {
                        DB::table('parent_student')
                            ->where('student_user_id', $studentUserId)
                            ->where('parent_user_id', '<>', $parentUser->id)
                            ->update(['is_primary' => false]);
                    }

                    if ($alreadyLinked) {
                        Notification::make()
                            ->title(__('Parent relationship updated successfully / تم تحديث بيانات ارتباط ولي الأمر بنجاح'))
                            ->info()
                            ->send();
                    } else {
                        Notification::make()
                            ->title(__('Parent account linked successfully / تم ربط ولي الأمر بالطالب بنجاح'))
                            ->success()
                            ->send();
                    }
                }),

            Action::make('unlinkParent')
                ->label(__('Unlink Parent / إلغاء الربط'))
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalWidth(Width::Medium)
                ->modalHeading(__('Unlink Parent / إلغاء ربط ولي الأمر'))
                ->modalDescription(__('Are you sure you want to remove this parent link from the student? / هل أنت متأكد من فك ارتباط ولي الأمر بهذا الطالب؟'))
                ->modalSubmitActionLabel(__('Yes, Unlink / نعم، فك الارتباط'))
                ->modalCancelActionLabel(__('Cancel / تراجع'))
                ->action(function (array $arguments) {
                    $parentUserId = $arguments['parent_user_id'] ?? null;
                    if ($parentUserId) {
                        DB::table('parent_student')
                            ->where('parent_user_id', $parentUserId)
                            ->where('student_user_id', $this->record->user_id)
                            ->delete();

                        Notification::make()
                            ->title(__('Parent unlinked successfully / تم إلغاء ربط ولي الأمر بنجاح'))
                            ->success()
                            ->send();
                    }
                }),

            // 2. Package & Credit Actions
            Action::make('assignPackage')
                ->label(__('Assign Package / إسناد باقة حصص'))
                ->modalHeading(__('Assign Session Package to Student / إسناد باقة حصص للطالب'))
                ->modalDescription(__('Assign session package credits to student, set validity and optional course scope. / إسناد باقة حصص وتحديد عدد الجلسات وفترة الصلاحية.'))
                ->modalWidth(Width::TwoExtraLarge)
                ->modalSubmitActionLabel(__('Confirm Package Assignment / تأكيد إسناد الباقة'))
                ->modalCancelActionLabel(__('Cancel / إلغاء'))
                ->icon('heroicon-o-ticket')
                ->color('primary')
                ->form([
                    Select::make('package_template_id')
                        ->label(__('Package Plan / قالب الباقة المعتمد'))
                        ->options(fn () => PackageTemplate::where('is_active', true)->pluck('name', 'id')->toArray())
                        ->searchable()
                        ->preload()
                        ->nullable()
                        ->live()
                        ->afterStateUpdated(function ($state, $set) {
                            if ($state) {
                                $template = PackageTemplate::find($state);
                                if ($template) {
                                    $set('total_sessions', $template->sessions_count);
                                    $set('expires_at', now()->addDays($template->validity_days ?? 90)->format('Y-m-d'));
                                }
                            }
                        })
                        ->helperText(__('Select a template to auto-fill credits, or customize manually below.')),

                    Grid::make(['default' => 1, 'sm' => 2])
                        ->schema([
                            TextInput::make('total_sessions')
                                ->label(__('Total Sessions / إجمالي عدد الحصص'))
                                ->numeric()
                                ->default(12)
                                ->minValue(1)
                                ->required()
                                ->suffix(__('sessions / حصة')),

                            Select::make('course_id')
                                ->label(__('Specific Course / تخصيص لمقرر محدد (اختياري)'))
                                ->options(fn () => Course::pluck('title', 'id')->toArray())
                                ->searchable()
                                ->preload()
                                ->nullable()
                                ->helperText(__('Leave blank if package covers general/all student sessions.')),
                        ]),

                    Grid::make(['default' => 1, 'sm' => 2])
                        ->schema([
                            DatePicker::make('expires_at')
                                ->label(__('Expiration Date / تاريخ انتهاء الصلاحية'))
                                ->default(now()->addMonths(3))
                                ->nullable(),

                            Select::make('status')
                                ->label(__('Initial Status / حالة الباقة'))
                                ->options([
                                    'active' => '🟢 ' . __('Active / نشطة ومفعلة'),
                                    'pending' => '⏳ ' . __('Pending Activation / معلقة'),
                                ])
                                ->default('active')
                                ->required(),
                        ]),
                ])
                ->action(function (array $data) {
                    StudentPackage::create([
                        'student_user_id' => $this->record->user_id,
                        'package_template_id' => $data['package_template_id'] ?? null,
                        'course_id' => $data['course_id'] ?? null,
                        'total_sessions' => (int) $data['total_sessions'],
                        'remaining_sessions' => (int) $data['total_sessions'],
                        'used_sessions' => 0,
                        'status' => $data['status'] ?? 'active',
                        'activated_at' => ($data['status'] ?? 'active') === 'active' ? now() : null,
                        'expires_at' => $data['expires_at'] ?? null,
                    ]);

                    Notification::make()
                        ->title(__("Assigned :count Session Credits to Student / تم تعيين :count حصة للطالب بنجاح", ['count' => $data['total_sessions']]))
                        ->success()
                        ->send();
                }),

            Action::make('adjustCredits')
                ->label(__('Adjust Credits / تعديل رصيد الحصص'))
                ->modalHeading(__('Adjust Sessions & Credits / تعديل رصيد الحصص للطالب'))
                ->modalDescription(__('Add or deduct sessions from active package with audit trail reason. / تعديل رصيد الحصص للباقة النشطة مع توضيح السبب.'))
                ->modalWidth(Width::ExtraLarge)
                ->modalSubmitActionLabel(__('Save Credit Adjustment / حفظ تعديل الرصيد'))
                ->modalCancelActionLabel(__('Cancel / إلغاء'))
                ->icon('heroicon-o-adjustments-horizontal')
                ->color('warning')
                ->form([
                    TextInput::make('adjustment')
                        ->label(__('Add / Deduct Sessions (إضافة أو خصم حصص)'))
                        ->numeric()
                        ->required()
                        ->helperText(__('Enter positive number to add (e.g. 3) or negative to deduct (e.g. -2) / أدخل رقماً موجباً للإضافة أو سالباً للخصم')),

                    DatePicker::make('new_expires_at')
                        ->label(__('Update Expiration Date / تعديل تاريخ انتهاء الصلاحية (اختياري)'))
                        ->nullable(),

                    TextInput::make('note')
                        ->label(__('Reason / Note (سبب التعديل)'))
                        ->placeholder(__('e.g. Compensation session, admin bonus, refund... / مثال: حصة تعويضية أو مكافأة'))
                        ->nullable(),
                ])
                ->action(function (array $data) {
                    $activePackage = StudentPackage::where('student_user_id', $this->record->user_id)
                        ->where('status', 'active')
                        ->latest()
                        ->first();

                    if (! $activePackage) {
                        Notification::make()
                            ->title(__('No active package found to adjust / لا توجد باقة نشطة لتعديل رصيدها'))
                            ->warning()
                            ->send();
                        return;
                    }

                    $adjustment = (int) $data['adjustment'];
                    $newRemaining = max(0, $activePackage->remaining_sessions + $adjustment);
                    $newTotal = max(0, $activePackage->total_sessions + ($adjustment > 0 ? $adjustment : 0));

                    $updateData = [
                        'remaining_sessions' => $newRemaining,
                        'total_sessions' => $newTotal,
                    ];

                    if (! empty($data['new_expires_at'])) {
                        $updateData['expires_at'] = $data['new_expires_at'];
                    }

                    $activePackage->update($updateData);

                    Notification::make()
                        ->title(__('Session credits adjusted successfully / تم تعديل رصيد الحصص بنجاح'))
                        ->success()
                        ->send();
                }),

            // 3. Course Enrollment Actions
            Action::make('enrollCourse')
                ->label(__('Enroll in Course / تسجيل في مقرر'))
                ->modalHeading(__('Enroll Student in Course / تسجيل الطالب في كورس'))
                ->modalDescription(__('Select the course curriculum, schedule group, and enrollment status for this student. / تحديد المقرر الدراسي وتعيين الفوج وحالة القيد.'))
                ->modalWidth(Width::TwoExtraLarge)
                ->modalSubmitActionLabel(__('Complete Enrollment / إتمام التسجيل'))
                ->modalCancelActionLabel(__('Cancel / إلغاء'))
                ->icon('heroicon-o-academic-cap')
                ->color('success')
                ->form([
                    Placeholder::make('student_card')
                        ->hiddenLabel()
                        ->content(function () {
                            $user = $this->record->user;
                            $name = e($user?->name ?? __('Student'));
                            $grade = e($this->record->gradeLevel?->name ?? __('Grade Level Unset'));
                            $code = e($this->record->student_code ?? 'STU-' . $this->record->id);

                            $activePkg = StudentPackage::where('student_user_id', $this->record->user_id)
                                ->where('status', 'active')
                                ->latest()
                                ->first();

                            $pkgText = $activePkg
                                ? ($activePkg->remaining_sessions . ' / ' . $activePkg->total_sessions . ' ' . __('Sessions'))
                                : __('No Active Package');

                            $enrollCount = CourseEnrollment::where('student_user_id', $this->record->user_id)
                                ->whereIn('status', ['active', 'enrolled', 'completed'])
                                ->count();

                            return new HtmlString('
                                <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: 1rem 1.25rem; border-radius: 1.15rem; border: 1.5px solid rgba(13, 148, 136, 0.25); background: linear-gradient(135deg, rgba(13, 148, 136, 0.08) 0%, rgba(16, 185, 129, 0.04) 100%); margin-bottom: 0.65rem; flex-wrap: wrap;">
                                    <div style="display: flex; align-items: center; gap: 0.85rem; min-width: 0;">
                                        <div style="width: 44px; height: 44px; border-radius: 9999px; background: linear-gradient(135deg, #0D9488 0%, #0F766E 100%); color: #FFF; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.15rem; box-shadow: 0 4px 14px rgba(13, 148, 136, 0.35); flex-shrink: 0;">
                                            <i class="fa-solid fa-graduation-cap"></i>
                                        </div>
                                        <div style="min-width: 0;">
                                            <div style="font-weight: 800; font-size: 0.975rem; line-height: 1.35; color: inherit; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                ' . $name . '
                                            </div>
                                            <div style="font-size: 0.775rem; opacity: 0.8; font-weight: 600; margin-top: 2px; display: flex; align-items: center; gap: 0.4rem;">
                                                <span style="font-family: monospace;">' . $code . '</span>
                                                <span>•</span>
                                                <span>' . $grade . '</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                                        <span style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.3rem 0.7rem; border-radius: 0.6rem; font-size: 0.75rem; font-weight: 800; background: rgba(13, 148, 136, 0.15); color: #0D9488; border: 1px solid rgba(13, 148, 136, 0.3);">
                                            <i class="fa-solid fa-ticket"></i> ' . $pkgText . '
                                        </span>
                                        <span style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.3rem 0.7rem; border-radius: 0.6rem; font-size: 0.75rem; font-weight: 800; background: rgba(59, 130, 246, 0.12); color: #3B82F6; border: 1px solid rgba(59, 130, 246, 0.25);">
                                            <i class="fa-solid fa-book-open"></i> ' . $enrollCount . ' ' . __('Enrolled') . '
                                        </span>
                                    </div>
                                </div>
                            ');
                        }),

                    Select::make('course_id')
                        ->label(__('Course / المقرر الدراسي'))
                        ->placeholder(__('Search & select course to enroll... / ابحث أو اختر المقرر...'))
                        ->options(function () {
                            $alreadyEnrolled = CourseEnrollment::where('student_user_id', $this->record->user_id)
                                ->whereIn('status', ['active', 'enrolled', 'completed'])
                                ->pluck('course_id')
                                ->toArray();

                            return Course::query()
                                ->whereNotIn('id', $alreadyEnrolled)
                                ->with(['teacher.user', 'gradeLevel'])
                                ->get()
                                ->mapWithKeys(function ($course) {
                                    $teacher = $course->teacher?->user?->name ? ' • أ/ ' . $course->teacher->user->name : '';
                                    $grade = $course->gradeLevel?->name ? ' (' . $course->gradeLevel->name . ')' : '';
                                    return [$course->id => $course->title . $teacher . $grade];
                                })
                                ->toArray();
                        })
                        ->searchable()
                        ->preload()
                        ->required()
                        ->helperText(__('Only available courses not currently enrolled by this student are listed / يعرض فقط المقررات غير المسجل بها الطالب حالياً')),

                    Grid::make(['default' => 1, 'sm' => 2])
                        ->schema([
                            Select::make('status')
                                ->label(__('Enrollment Status / حالة التسجيل'))
                                ->options([
                                    'active' => '🟢 ' . __('Active / نشط ومستمر'),
                                    'pending' => '⏳ ' . __('Pending Confirmation / معلق للتأكيد'),
                                    'completed' => '🎓 ' . __('Completed / مكتمل ومختتم'),
                                ])
                                ->default('active')
                                ->required(),

                            TextInput::make('cohort')
                                ->label(__('Cohort / Group (الفوج أو المجموعة)'))
                                ->placeholder(__('e.g. Group A (Sat & Wed) / مثال: مجموعة أ'))
                                ->helperText(__('Optional group name for scheduling / الفوج أو المجموعة الدراسية')),
                        ]),

                    DatePicker::make('enrolled_at')
                        ->label(__('Enrollment Date / تاريخ بدء القيد'))
                        ->default(now())
                        ->required()
                        ->helperText(__('Defaults to today / التاريخ الفعلي لبدء تسجيل الطالب')),
                ])
                ->action(function (array $data) {
                    CourseEnrollment::updateOrCreate(
                        [
                            'student_user_id' => $this->record->user_id,
                            'course_id' => $data['course_id'],
                        ],
                        [
                            'status' => $data['status'] ?? 'active',
                            'cohort' => $data['cohort'] ?? null,
                            'enrolled_at' => $data['enrolled_at'] ?? now(),
                        ]
                    );

                    Notification::make()
                        ->title(__('Student enrolled in course successfully / تم تسجيل الطالب في المقرر بنجاح'))
                        ->success()
                        ->send();
                }),

            Action::make('unenrollCourse')
                ->label(__('Unenroll Course / إلغاء التسجيل'))
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalWidth(Width::Medium)
                ->modalHeading(__('Unenroll Student from Course / إلغاء تسجيل الطالب'))
                ->modalDescription(__('Are you sure you want to remove this course enrollment? / هل أنت متأكد من إلغاء قيد الطالب في هذا المقرر؟'))
                ->modalSubmitActionLabel(__('Yes, Remove Enrollment / تأكيد إلغاء القيد'))
                ->modalCancelActionLabel(__('Cancel / تراجع'))
                ->action(function (array $arguments) {
                    $enrollmentId = $arguments['enrollment_id'] ?? null;
                    if ($enrollmentId) {
                        CourseEnrollment::where('id', $enrollmentId)
                            ->where('student_user_id', $this->record->user_id)
                            ->delete();

                        Notification::make()
                            ->title(__('Course enrollment removed successfully / تم إلغاء التسجيل بالمقرر بنجاح'))
                            ->success()
                            ->send();
                    }
                }),

            // 4. Homework & Submission Actions
            Action::make('createHomework')
                ->label(__('Assign Homework / تعيين واجب'))
                ->modalHeading(__('Create & Assign Homework / إنشاء وتعيين واجب دراسي للطالب'))
                ->modalDescription(__('Create homework assignment and dispatch worksheet directly to this student. / تعيين واجب دراسي وتكليف الطالب بحله.'))
                ->modalWidth(Width::TwoExtraLarge)
                ->modalSubmitActionLabel(__('Assign Homework / تعيين الواجب'))
                ->modalCancelActionLabel(__('Cancel / إلغاء'))
                ->icon('heroicon-o-document-plus')
                ->color('primary')
                ->form([
                    TextInput::make('title')
                        ->label(__('Homework Title / عنوان الواجب والتكليف'))
                        ->placeholder(__('e.g. Unit 3 Trigonometry Problem Set / مثال: حل مسائل الوحدة الثالثة'))
                        ->required()
                        ->maxLength(200),

                    Grid::make(['default' => 1, 'sm' => 2])
                        ->schema([
                            Select::make('course_id')
                                ->label(__('Linked Course / المقرر المرتبط'))
                                ->options(function () {
                                    $enrolledCourseIds = CourseEnrollment::where('student_user_id', $this->record->user_id)
                                        ->pluck('course_id')
                                        ->toArray();

                                    $query = Course::query();
                                    if (! empty($enrolledCourseIds)) {
                                        $query->whereIn('id', $enrolledCourseIds);
                                    }
                                    return $query->pluck('title', 'id')->toArray();
                                })
                                ->searchable()
                                ->preload()
                                ->required(),

                            Select::make('teacher_profile_id')
                                ->label(__('Assigned Teacher / المعلم المشرف'))
                                ->options(fn () => TeacherProfile::with('user')->get()->mapWithKeys(fn ($t) => [$t->id => ($t->user?->name ?: 'Teacher #' . $t->id) . ($t->specialization ? ' — ' . $t->specialization : '')])->toArray())
                                ->searchable()
                                ->preload()
                                ->required(),
                        ]),

                    Grid::make(['default' => 1, 'sm' => 2])
                        ->schema([
                            DateTimePicker::make('due_at')
                                ->label(__('Deadline / آخر موعد للتسليم'))
                                ->default(now()->addDays(3))
                                ->required(),

                            FormFileUpload::make('attachment_file')
                                ->label(__('Homework Document / Worksheet (ملف ورقة العمل أو التكليف)'))
                                ->disk('public')
                                ->directory('educational_files')
                                ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                                ->maxSize(25600),
                        ]),

                    Textarea::make('description')
                        ->label(__('Instructions / Notes (التعليمات والأسئلة)'))
                        ->placeholder(__('Additional instructions, page numbers, or guidelines... / تعليمات إضافية'))
                        ->rows(3),
                ])
                ->action(function (array $data) {
                    $filePath = $data['attachment_file'] ?? null;
                    $originalName = $filePath ? basename($filePath) : null;

                    $assignment = Assignment::create([
                        'teacher_profile_id' => $data['teacher_profile_id'],
                        'course_id' => $data['course_id'],
                        'title' => $data['title'],
                        'description' => $data['description'] ?? null,
                        'attachment_file_path' => $filePath,
                        'attachment_file_name' => $originalName,
                        'duration_minutes' => 45,
                        'due_at' => $data['due_at'],
                        'status' => 'published',
                        'passing_score' => 70.0,
                    ]);

                    FileUpload::create([
                        'user_id' => auth()->id(),
                        'student_user_id' => $this->record->user_id,
                        'course_id' => $data['course_id'],
                        'teacher_profile_id' => $data['teacher_profile_id'],
                        'assignment_id' => $assignment->id,
                        'title' => $data['title'],
                        'description' => $data['description'] ?? null,
                        'file_path' => $filePath ?: 'educational_files/homework.pdf',
                        'original_name' => $originalName ?: 'homework.pdf',
                        'file_size' => 1024,
                        'mime_type' => 'application/pdf',
                        'file_type' => 'pdf',
                        'category' => 'homework',
                        'due_at' => $data['due_at'],
                    ]);

                    Notification::make()
                        ->title(__('Homework created and assigned to student successfully / تم إنشاء وتعيين الواجب للطالب بنجاح'))
                        ->success()
                        ->send();
                }),

            Action::make('gradeSubmission')
                ->label(__('Grade Submission / تصحيح الواجب'))
                ->modalHeading(__('Evaluate Homework Submission / تقييم وتصحيح حل الطالب'))
                ->modalDescription(__('Evaluate student submission, grade score and leave constructive feedback. / تصحيح حل الطالب واعتماد النتيجة.'))
                ->modalWidth(Width::ExtraLarge)
                ->modalSubmitActionLabel(__('Save Evaluation / اعتماد التقييم'))
                ->modalCancelActionLabel(__('Cancel / إلغاء'))
                ->icon('heroicon-o-check-badge')
                ->color('warning')
                ->form([
                    Grid::make(['default' => 1, 'sm' => 2])
                        ->schema([
                            TextInput::make('grade')
                                ->label(__('Grade / Score (%) الدرجة بالنسبة المئوية'))
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(100)
                                ->required()
                                ->suffix('%'),

                            Select::make('status')
                                ->label(__('Evaluation Status / حالة التقييم'))
                                ->options([
                                    'graded' => '✅ ' . __('Graded / تم التصحيح والاعتماد'),
                                    'reviewed' => '👀 ' . __('Reviewed / تمت المراجعة'),
                                    'submitted' => '⏳ ' . __('Pending Revision / بانتظار تعديل الطالب'),
                                ])
                                ->default('graded')
                                ->required(),
                        ]),

                    Textarea::make('teacher_notes')
                        ->label(__('Evaluation Feedback / ملاحظات وتوجيهات للطالب'))
                        ->placeholder(__('Feedback, points of strength, corrections... / ملاحظات المعلم'))
                        ->rows(3),
                ])
                ->action(function (array $data, array $arguments) {
                    $subId = $arguments['submission_id'] ?? null;
                    if ($subId) {
                        $sub = AssignmentSubmission::find($subId);
                        if ($sub) {
                            $sub->update([
                                'grade' => (float) $data['grade'],
                                'score' => (float) $data['grade'],
                                'percentage' => (float) $data['grade'],
                                'status' => $data['status'],
                                'teacher_notes' => $data['teacher_notes'] ?? null,
                                'reviewed_at' => now(),
                                'reviewed_by' => auth()->id(),
                            ]);

                            Notification::make()
                                ->title(__('Submission graded successfully / تم تصحيح الواجب واعتماد الدرجة بنجاح'))
                                ->success()
                                ->send();
                        }
                    }
                }),

            DeleteAction::make()->label(__('Recycle Bin / سلة المحذوفات')),
            RestoreAction::make(),
            ForceDeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $status = $this->data['user_status'] ?? $this->form->getRawState()['user_status'] ?? null;
        if ($status && $this->record->user) {
            $this->record->user->update(['status' => $status]);
        }
    }
}
