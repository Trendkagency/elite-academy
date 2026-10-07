@php
    $studentProfile = $getRecord();
    if (! $studentProfile) {
        return;
    }
    $studentUser = $studentProfile->user;
    $studentUserId = $studentProfile->user_id;

    $packages = \App\Models\StudentPackage::where('student_user_id', $studentUserId)->with(['packageTemplate', 'course'])->latest()->get();
    $activePackage = $packages->firstWhere('status', 'active') ?? $packages->first();

    $linkedParents = \Illuminate\Support\Facades\DB::table('parent_student')
        ->where('student_user_id', $studentUserId)
        ->join('users', 'users.id', '=', 'parent_student.parent_user_id')
        ->leftJoin('parent_profiles', 'parent_profiles.user_id', '=', 'users.id')
        ->select(
            'parent_student.*',
            'users.id as user_id',
            'users.name as parent_name',
            'users.email as parent_email',
            'users.phone as parent_phone',
            'parent_profiles.id as parent_profile_id'
        )
        ->get();

    $enrollments = \App\Models\CourseEnrollment::where('student_user_id', $studentUserId)->with('course.teacher.user')->latest()->get();
    $submissions = \App\Models\AssignmentSubmission::where('student_user_id', $studentUserId)->with(['assignment.course', 'assignment.teacher.user'])->latest()->take(6)->get();
@endphp

<style>
    .student-overview-wrapper {
        width: 100%;
        margin-top: 14px;
        margin-bottom: 14px;
        font-family: 'Cairo', sans-serif;
    }

    .overview-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(min(100%, 320px), 1fr));
        gap: 1.25rem;
    }

    /* Card Shell */
    .overview-card {
        border-radius: 1.25rem;
        padding: 1.25rem;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    html:not(.dark) .overview-card {
        background: #FFFFFF;
        border: 1.5px solid #E2E8F0;
        box-shadow: 0 4px 16px -2px rgba(15, 23, 42, 0.06);
    }

    html:not(.dark) .overview-card:hover {
        border-color: #0D9488;
        box-shadow: 0 12px 28px -4px rgba(13, 148, 136, 0.12);
    }

    html.dark .overview-card {
        background: #111C35;
        border: 1.5px solid #233354;
        box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.45);
    }

    html.dark .overview-card:hover {
        border-color: #14B8A6;
        box-shadow: 0 14px 35px -8px rgba(20, 184, 166, 0.22);
    }

    /* Card Header Bar */
    .card-title-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-bottom: 0.85rem;
        margin-bottom: 1rem;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    html:not(.dark) .card-title-bar {
        border-bottom: 1.5px solid #F1F5F9;
    }

    html.dark .card-title-bar {
        border-bottom: 1.5px solid #1E293B;
    }

    .card-title-group {
        display: flex;
        align-items: center;
        gap: 0.6rem;
    }

    .card-title-icon-box {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
    }

    html:not(.dark) .card-title-icon-box {
        background: rgba(13, 148, 136, 0.1);
        color: #0F766E;
    }

    html.dark .card-title-icon-box {
        background: rgba(20, 184, 166, 0.18);
        color: #2DD4BF;
    }

    .card-title-text {
        font-size: 0.925rem;
        font-weight: 800;
        line-height: 1.3;
    }

    html:not(.dark) .card-title-text {
        color: #0F172A;
    }

    html.dark .card-title-text {
        color: #F8FAFC;
    }

    .card-actions-toolbar {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        flex-wrap: wrap;
    }

    /* Quick Action Buttons */
    .btn-card-action {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        font-size: 0.775rem;
        font-weight: 700;
        padding: 0.38rem 0.65rem;
        border-radius: 0.6rem;
        cursor: pointer;
        transition: all 0.18s ease;
        border: none;
        text-decoration: none !important;
        line-height: 1.2;
    }

    .btn-card-action:active {
        transform: scale(0.97);
    }

    .btn-action-teal {
        background: linear-gradient(135deg, #0D9488 0%, #0F766E 100%);
        color: #FFFFFF !important;
        box-shadow: 0 2px 6px rgba(13, 148, 136, 0.25);
    }

    .btn-action-teal:hover {
        background: linear-gradient(135deg, #14B8A6 0%, #0D9488 100%);
        box-shadow: 0 4px 10px rgba(13, 148, 136, 0.35);
        color: #FFFFFF !important;
    }

    .btn-action-outline {
        border: 1px solid;
    }

    html:not(.dark) .btn-action-outline {
        border-color: #CBD5E1;
        background: #F8FAFC;
        color: #334155 !important;
    }

    html:not(.dark) .btn-action-outline:hover {
        border-color: #0D9488;
        color: #0F766E !important;
        background: rgba(13, 148, 136, 0.06);
    }

    html.dark .btn-action-outline {
        border-color: #334155;
        background: #1E293B;
        color: #CBD5E1 !important;
    }

    html.dark .btn-action-outline:hover {
        border-color: #2DD4BF;
        color: #5EEAD4 !important;
        background: rgba(20, 184, 166, 0.12);
    }

    .btn-action-amber {
        background: rgba(245, 158, 11, 0.15);
        border: 1px solid rgba(245, 158, 11, 0.35);
        color: #F59E0B !important;
    }

    .btn-action-amber:hover {
        background: rgba(245, 158, 11, 0.25);
        color: #D97706 !important;
    }

    /* Badges */
    .card-badge {
        font-size: 0.725rem;
        font-weight: 800;
        padding: 0.25rem 0.6rem;
        border-radius: 0.5rem;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
    }

    .badge-teal {
        background: rgba(13, 148, 136, 0.12);
        color: #0D9488;
        border: 1px solid rgba(13, 148, 136, 0.25);
    }

    html.dark .badge-teal {
        background: rgba(20, 184, 166, 0.18);
        color: #2DD4BF;
        border-color: rgba(20, 184, 166, 0.35);
    }

    .badge-amber {
        background: rgba(245, 158, 11, 0.12);
        color: #D97706;
        border: 1px solid rgba(245, 158, 11, 0.25);
    }

    html.dark .badge-amber {
        background: rgba(245, 158, 11, 0.2);
        color: #FBBF24;
        border-color: rgba(245, 158, 11, 0.35);
    }

    .badge-gray {
        background: rgba(148, 163, 184, 0.12);
        color: #64748B;
        border: 1px solid rgba(148, 163, 184, 0.25);
    }

    html.dark .badge-gray {
        background: rgba(148, 163, 184, 0.18);
        color: #94A3B8;
        border-color: rgba(148, 163, 184, 0.3);
    }

    /* Item List & Rows */
    .item-list {
        display: flex;
        flex-direction: column;
        gap: 0.65rem;
    }

    .list-row {
        border-radius: 0.85rem;
        padding: 0.75rem 0.95rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        transition: all 0.18s ease;
    }

    html:not(.dark) .list-row {
        background: #F8FAFC;
        border: 1px solid #E2E8F0;
    }

    html:not(.dark) .list-row:hover {
        background: #F1F5F9;
        border-color: #CBD5E1;
    }

    html.dark .list-row {
        background: #0D1629;
        border: 1px solid #1A2844;
    }

    html.dark .list-row:hover {
        background: #14203B;
        border-color: #27395F;
    }

    .list-row-title {
        font-size: 0.825rem;
        font-weight: 700;
        line-height: 1.35;
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }

    html:not(.dark) .list-row-title {
        color: #0F172A;
    }

    html.dark .list-row-title {
        color: #F8FAFC;
    }

    .list-row-sub {
        font-size: 0.725rem;
        font-weight: 600;
        margin-top: 0.25rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    html:not(.dark) .list-row-sub {
        color: #64748B;
    }

    html.dark .list-row-sub {
        color: #94A3B8;
    }

    .row-actions-group {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        flex-shrink: 0;
    }

    /* Small row buttons */
    .btn-row-sm {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.725rem;
        font-weight: 700;
        padding: 0.3rem 0.55rem;
        border-radius: 0.5rem;
        cursor: pointer;
        border: 1px solid transparent;
        text-decoration: none !important;
        transition: all 0.15s ease;
    }

    .btn-row-sm-danger {
        background: rgba(239, 68, 68, 0.1);
        border-color: rgba(239, 68, 68, 0.25);
        color: #EF4444 !important;
    }

    .btn-row-sm-danger:hover {
        background: #EF4444;
        color: #FFFFFF !important;
    }

    .btn-row-sm-info {
        background: rgba(14, 165, 233, 0.1);
        border-color: rgba(14, 165, 233, 0.25);
        color: #0EA5E9 !important;
    }

    .btn-row-sm-info:hover {
        background: #0EA5E9;
        color: #FFFFFF !important;
    }

    .btn-row-sm-warning {
        background: rgba(245, 158, 11, 0.12);
        border-color: rgba(245, 158, 11, 0.3);
        color: #F59E0B !important;
    }

    .btn-row-sm-warning:hover {
        background: #F59E0B;
        color: #FFFFFF !important;
    }

    /* Progress bar */
    .pkg-progress-bar-bg {
        width: 100%;
        height: 10px;
        border-radius: 9999px;
        overflow: hidden;
        margin-top: 10px;
    }

    html:not(.dark) .pkg-progress-bar-bg {
        background: #E2E8F0;
        border: 1px solid #CBD5E1;
    }

    html.dark .pkg-progress-bar-bg {
        background: #090E1A;
        border: 1px solid #1E293B;
    }

    .pkg-progress-bar-fill {
        height: 100%;
        border-radius: 9999px;
        background: linear-gradient(90deg, #0D9488 0%, #10B981 100%);
        transition: width 0.4s ease;
    }

    /* Empty state box */
    .empty-box {
        text-align: center;
        padding: 1.5rem 1rem;
        border-radius: 0.85rem;
        border: 1.5px dashed;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.65rem;
    }

    html:not(.dark) .empty-box {
        background: #F8FAFC;
        border-color: #CBD5E1;
        color: #64748B;
    }

    html.dark .empty-box {
        background: #0D1629;
        border-color: #233354;
        color: #94A3B8;
    }

    .empty-box-text {
        font-size: 0.8rem;
        font-weight: 600;
        line-height: 1.4;
    }

    /* Responsive Breakpoints */
    @media (max-width: 768px) {
        .overview-grid {
            grid-template-columns: 1fr !important;
            gap: 1rem !important;
        }

        .overview-card {
            padding: 1rem !important;
            border-radius: 1.15rem !important;
        }

        .card-title-bar {
            flex-direction: column !important;
            align-items: flex-start !important;
            gap: 0.75rem !important;
        }

        .card-actions-toolbar {
            width: 100% !important;
            display: flex !important;
            justify-content: flex-start !important;
            flex-wrap: wrap !important;
            gap: 0.5rem !important;
        }

        .list-row {
            padding: 0.65rem 0.75rem !important;
            flex-direction: column !important;
            align-items: flex-start !important;
            gap: 0.5rem !important;
        }

        .row-actions-group {
            width: 100% !important;
            justify-content: flex-end !important;
        }
    }
</style>

<div class="student-overview-wrapper">
    <div class="overview-grid">

        <!-- ========================================== -->
        <!-- 1. ENROLLED COURSES CARD (المقررات المسجلة) -->
        <!-- ========================================== -->
        <div class="overview-card">
            <div>
                <div class="card-title-bar">
                    <div class="card-title-group">
                        <div class="card-title-icon-box">
                            <i class="fa-solid fa-graduation-cap"></i>
                        </div>
                        <div>
                            <div class="card-title-text">{{ __('Enrolled Courses / المقررات المسجلة') }}</div>
                            <span class="card-badge badge-teal" style="margin-top: 2px;">{{ $enrollments->count() }} {{ __('Courses') }}</span>
                        </div>
                    </div>
                    <div class="card-actions-toolbar">
                        <button type="button" class="btn-card-action btn-action-teal" wire:click="mountAction('enrollCourse')">
                            <i class="fa-solid fa-plus"></i> {{ __('Enroll Course') }}
                        </button>
                        <a href="{{ route('filament.admin.resources.courses.create') }}" target="_blank" class="btn-card-action btn-action-outline" title="{{ __('Create New Course in system') }}">
                            <i class="fa-solid fa-square-plus"></i> {{ __('New Course ↗') }}
                        </a>
                    </div>
                </div>

                @if($enrollments->isNotEmpty())
                    <div class="item-list">
                        @foreach($enrollments as $enrollment)
                            @php
                                $eStatus = is_object($enrollment->status) ? ($enrollment->status->value ?? (string)$enrollment->status) : (string)$enrollment->status;
                            @endphp
                            <div class="list-row">
                                <div style="min-width: 0; flex: 1;">
                                    <div class="list-row-title" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        <i class="fa-solid fa-book text-emerald-500"></i>
                                        <span>{{ $enrollment->course?->title ?? __('Course') }}</span>
                                    </div>
                                    <div class="list-row-sub">
                                        <span><i class="fa-solid fa-chalkboard-user"></i> {{ $enrollment->course?->teacher?->user?->name ?? __('Staff') }}</span>
                                        <span>•</span>
                                        <span>{{ __('Enrolled') }}: {{ $enrollment->enrolled_at ? \Carbon\Carbon::parse($enrollment->enrolled_at)->format('d M Y') : $enrollment->created_at->format('d M Y') }}</span>
                                    </div>
                                </div>
                                <div class="row-actions-group">
                                    <span class="card-badge badge-teal" style="text-transform: capitalize;">{{ $eStatus }}</span>
                                    @if($enrollment->course_id)
                                        <a href="{{ route('filament.admin.resources.courses.edit', ['record' => $enrollment->course_id]) }}" target="_blank" class="btn-row-sm btn-row-sm-info" title="{{ __('View Course') }}">
                                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                        </a>
                                    @endif
                                    <button type="button" class="btn-row-sm btn-row-sm-danger" wire:click="mountAction('unenrollCourse', { enrollment_id: {{ $enrollment->id }} })" title="{{ __('Drop / Unenroll') }}">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-box">
                        <i class="fa-solid fa-book-open" style="font-size: 24px; opacity: 0.6;"></i>
                        <span class="empty-box-text">{{ __('Student has not enrolled in any courses yet / لم يسجل الطالب في أي كورس بعد.') }}</span>
                        <button type="button" class="btn-card-action btn-action-teal" wire:click="mountAction('enrollCourse')">
                            <i class="fa-solid fa-plus"></i> {{ __('Enroll Student Now') }}
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <!-- ========================================================== -->
        <!-- 2. LINKED PARENT ACCOUNTS CARD (أولياء الأمور المرتبطين)   -->
        <!-- ========================================================== -->
        <div class="overview-card">
            <div>
                <div class="card-title-bar">
                    <div class="card-title-group">
                        <div class="card-title-icon-box">
                            <i class="fa-solid fa-people-roof"></i>
                        </div>
                        <div>
                            <div class="card-title-text">{{ __('Linked Parents / أولياء الأمور') }}</div>
                            <span class="card-badge badge-teal" style="margin-top: 2px;">{{ $linkedParents->count() }} {{ __('Linked') }}</span>
                        </div>
                    </div>
                    <div class="card-actions-toolbar">
                        <button type="button" class="btn-card-action btn-action-teal" wire:click="mountAction('linkParent')">
                            <i class="fa-solid fa-link"></i> {{ __('Link Parent') }}
                        </button>
                        <button type="button" class="btn-card-action btn-action-outline" wire:click="mountAction('createAndLinkParent')">
                            <i class="fa-solid fa-user-plus"></i> {{ __('Add & Link') }}
                        </button>
                    </div>
                </div>

                @if($linkedParents->isNotEmpty())
                    <div class="item-list">
                        @foreach($linkedParents as $parent)
                            <div class="list-row">
                                <div style="min-width: 0; flex: 1;">
                                    <div class="list-row-title">
                                        <i class="fa-solid fa-user-shield text-cyan-500"></i>
                                        <span>{{ $parent->parent_name }}</span>
                                        @if($parent->is_primary)
                                            <span class="card-badge badge-amber" style="padding: 1px 6px; font-size: 10px;">★ {{ __('Primary') }}</span>
                                        @endif
                                        <span class="card-badge badge-gray" style="padding: 1px 6px; font-size: 10px; text-transform: capitalize;">{{ $parent->relationship }}</span>
                                    </div>
                                    <div class="list-row-sub">
                                        @if($parent->parent_phone)
                                            <span><i class="fa-solid fa-phone"></i> {{ $parent->parent_phone }}</span>
                                            <span>•</span>
                                        @endif
                                        <span><i class="fa-solid fa-envelope"></i> {{ $parent->parent_email }}</span>
                                    </div>
                                </div>
                                <div class="row-actions-group">
                                    @if($parent->parent_profile_id)
                                        <a href="{{ route('filament.admin.resources.parent-profiles.edit', ['record' => $parent->parent_profile_id]) }}" target="_blank" class="btn-row-sm btn-row-sm-info" title="{{ __('View Parent Profile') }}">
                                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                        </a>
                                    @endif
                                    <button type="button" class="btn-row-sm btn-row-sm-danger" wire:click="mountAction('unlinkParent', { parent_user_id: {{ $parent->parent_user_id }} })" title="{{ __('Unlink Parent') }}">
                                        <i class="fa-solid fa-link-slash"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-box">
                        <i class="fa-solid fa-users" style="font-size: 24px; opacity: 0.6;"></i>
                        <span class="empty-box-text">{{ __('No parent accounts linked to this student / لا يوجد ولي أمر مرتبط بهذا الطالب.') }}</span>
                        <div style="display: flex; gap: 6px; flex-wrap: wrap; justify-content: center;">
                            <button type="button" class="btn-card-action btn-action-teal" wire:click="mountAction('linkParent')">
                                <i class="fa-solid fa-link"></i> {{ __('Link Existing Parent') }}
                            </button>
                            <button type="button" class="btn-card-action btn-action-outline" wire:click="mountAction('createAndLinkParent')">
                                <i class="fa-solid fa-user-plus"></i> {{ __('Add New Parent') }}
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- =================================================================== -->
        <!-- 3. ACTIVE SESSION PACKAGE & CREDITS (باقات الحصص والرصيد)           -->
        <!-- =================================================================== -->
        <div class="overview-card">
            <div>
                <div class="card-title-bar">
                    <div class="card-title-group">
                        <div class="card-title-icon-box">
                            <i class="fa-solid fa-ticket"></i>
                        </div>
                        <div>
                            <div class="card-title-text">{{ __('Active Package & Credits / باقة الحصص والرصيد') }}</div>
                            @if($activePackage)
                                <span class="card-badge badge-teal" style="margin-top: 2px;">
                                    <i class="fa-solid fa-circle-check"></i> {{ __('Active Package') }}
                                </span>
                            @else
                                <span class="card-badge badge-gray" style="margin-top: 2px;">{{ __('No Active Package') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="card-actions-toolbar">
                        <button type="button" class="btn-card-action btn-action-teal" wire:click="mountAction('assignPackage')">
                            <i class="fa-solid fa-plus"></i> {{ __('Assign Package') }}
                        </button>
                        @if($activePackage)
                            <button type="button" class="btn-card-action btn-action-amber" wire:click="mountAction('adjustCredits')">
                                <i class="fa-solid fa-sliders"></i> {{ __('Adjust Credits') }}
                            </button>
                        @endif
                    </div>
                </div>

                @if($activePackage)
                    @php
                        $rem = $activePackage->remaining_sessions;
                        $tot = $activePackage->total_sessions;
                        $pct = $tot > 0 ? min(100, round(($rem / $tot) * 100)) : 0;
                    @endphp
                    <div class="list-row" style="flex-direction: column; align-items: stretch; gap: 0.5rem; padding: 1rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.875rem; font-weight: 800;">
                            <span>{{ $activePackage->packageTemplate?->name ?? ($activePackage->course?->title ?? __('Custom Session Package')) }}</span>
                            <span style="color: #0D9488; font-weight: 900;">{{ $rem }} / {{ $tot }} {{ __('Sessions Remaining') }}</span>
                        </div>

                        <div class="pkg-progress-bar-bg">
                            <div class="pkg-progress-bar-fill" style="width: {{ $pct }}%;"></div>
                        </div>

                        <div style="display: flex; justify-content: space-between; font-size: 0.725rem; font-weight: 700; margin-top: 4px;" class="list-row-sub">
                            <span><i class="fa-solid fa-calendar-check"></i> {{ __('Activated') }}: {{ $activePackage->activated_at ? \Carbon\Carbon::parse($activePackage->activated_at)->format('d M Y') : 'N/A' }}</span>
                            <span><i class="fa-solid fa-hourglass-end"></i> {{ __('Expires') }}: {{ $activePackage->expires_at ? \Carbon\Carbon::parse($activePackage->expires_at)->format('d M Y') : __('No Expiry') }}</span>
                        </div>
                    </div>
                @else
                    <div class="empty-box">
                        <i class="fa-solid fa-credit-card" style="font-size: 24px; opacity: 0.6;"></i>
                        <span class="empty-box-text">{{ __('No active session package assigned to this student / لا توجد باقة حصص نشطة للطالب.') }}</span>
                        <button type="button" class="btn-card-action btn-action-teal" wire:click="mountAction('assignPackage')">
                            <i class="fa-solid fa-plus"></i> {{ __('Assign Package Now') }}
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <!-- =================================================================== -->
        <!-- 4. RECENT HOMEWORK SUBMISSIONS (الواجبات والتكليفات المدرسية)         -->
        <!-- =================================================================== -->
        <div class="overview-card">
            <div>
                <div class="card-title-bar">
                    <div class="card-title-group">
                        <div class="card-title-icon-box">
                            <i class="fa-solid fa-file-signature"></i>
                        </div>
                        <div>
                            <div class="card-title-text">{{ __('Homework Submissions / الواجبات والتسليمات') }}</div>
                            <span class="card-badge badge-teal" style="margin-top: 2px;">{{ $submissions->count() }} {{ __('Submissions') }}</span>
                        </div>
                    </div>
                    <div class="card-actions-toolbar">
                        <button type="button" class="btn-card-action btn-action-teal" wire:click="mountAction('createHomework')">
                            <i class="fa-solid fa-plus"></i> {{ __('Assign Homework') }}
                        </button>
                        <a href="{{ url('/admin/file-uploads') }}?tableFilters[student_user_id][value]={{ $studentUserId }}" target="_blank" class="btn-card-action btn-action-outline" title="{{ __('View Educational Files Hub') }}">
                            <i class="fa-solid fa-folder-open"></i> {{ __('Files Hub ↗') }}
                        </a>
                    </div>
                </div>

                @if($submissions->isNotEmpty())
                    <div class="item-list">
                        @foreach($submissions as $sub)
                            @php
                                $sStatus = is_object($sub->status) ? ($sub->status->value ?? (string)$sub->status) : (string)$sub->status;
                            @endphp
                            <div class="list-row">
                                <div style="min-width: 0; flex: 1;">
                                    <div class="list-row-title" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        <i class="fa-solid fa-file-pen text-amber-500"></i>
                                        <span>{{ $sub->assignment?->title ?? __('Assignment') }}</span>
                                    </div>
                                    <div class="list-row-sub">
                                        <span><i class="fa-solid fa-book"></i> {{ $sub->assignment?->course?->title ?? __('General') }}</span>
                                        <span>•</span>
                                        <span>{{ __('Score') }}: <strong style="color: #0D9488;">{{ $sub->grade !== null ? $sub->grade . '%' : __('Pending Review') }}</strong></span>
                                    </div>
                                </div>
                                <div class="row-actions-group">
                                    <span class="card-badge {{ $sStatus === 'graded' ? 'badge-teal' : 'badge-amber' }}" style="text-transform: capitalize;">{{ $sStatus }}</span>
                                    <button type="button" class="btn-row-sm btn-row-sm-warning" wire:click="mountAction('gradeSubmission', { submission_id: {{ $sub->id }} })" title="{{ __('Evaluate / Grade') }}">
                                        <i class="fa-solid fa-check-double"></i> {{ __('Grade') }}
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-box">
                        <i class="fa-solid fa-clipboard-list" style="font-size: 24px; opacity: 0.6;"></i>
                        <span class="empty-box-text">{{ __('No homework submissions recorded yet / لم يتم تسجيل أي تسليمات للواجبات بعد.') }}</span>
                        <button type="button" class="btn-card-action btn-action-teal" wire:click="mountAction('createHomework')">
                            <i class="fa-solid fa-plus"></i> {{ __('Assign Homework Now') }}
                        </button>
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
