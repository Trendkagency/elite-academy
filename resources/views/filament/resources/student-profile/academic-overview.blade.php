@php
    $studentProfile = $getRecord();
    if (! $studentProfile) {
        return;
    }
    $studentUser = $studentProfile->user;
    $studentUserId = $studentProfile->user_id;

    $activePackage = \App\Models\StudentPackage::where('student_user_id', $studentUserId)->where('status', 'active')->first();
    $parents = \App\Models\ParentProfile::whereHas('students', fn($q) => $q->where('student_user_id', $studentUserId))->with('user')->get();
    $submissions = \App\Models\AssignmentSubmission::where('student_user_id', $studentUserId)->with('assignment')->latest()->take(5)->get();
    $exceptions = \App\Models\ExceptionRequest::where('student_user_id', $studentUserId)->latest()->take(5)->get();
    $enrollments = \App\Models\CourseEnrollment::where('student_user_id', $studentUserId)->with('course')->latest()->take(5)->get();
@endphp

<style>
    .student-overview-wrapper {
        width: 100%;
        margin-top: 10px;
        margin-bottom: 10px;
    }
    .overview-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 20px;
    }
    .overview-card {
        background: #1e293b;
        border: 1px solid #334155;
        border-radius: 20px;
        padding: 20px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
        transition: all 0.3s ease;
    }
    .overview-card:hover {
        border-color: #14b8a6;
        box-shadow: 0 15px 30px -10px rgba(20, 184, 166, 0.2);
    }
    .card-title-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-bottom: 12px;
        margin-bottom: 14px;
        border-bottom: 1px solid #334155;
    }
    .card-title-text {
        font-size: 14px;
        font-weight: 800;
        color: #ffffff;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .card-badge {
        font-size: 11px;
        font-weight: 800;
        padding: 3px 10px;
        border-radius: 12px;
    }
    .badge-teal {
        background: rgba(20, 184, 166, 0.15);
        color: #2dd4bf;
        border: 1px solid rgba(20, 184, 166, 0.3);
    }
    .badge-gray {
        background: rgba(148, 163, 184, 0.15);
        color: #94a3b8;
        border: 1px solid rgba(148, 163, 184, 0.3);
    }
    .item-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .list-row {
        background: #0f172a;
        border: 1px solid #1e293b;
        border-radius: 14px;
        padding: 10px 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .list-row-title {
        font-size: 12px;
        font-weight: 700;
        color: #f8fafc;
    }
    .list-row-sub {
        font-size: 10px;
        font-weight: 600;
        color: #94a3b8;
        margin-top: 2px;
    }
    .pkg-progress-bar-bg {
        width: 100%;
        height: 10px;
        background: #0f172a;
        border-radius: 10px;
        overflow: hidden;
        border: 1px solid #334155;
        margin-top: 10px;
    }
    .pkg-progress-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, #14b8a6, #10b981);
        border-radius: 10px;
    }
    .empty-box {
        text-align: center;
        padding: 20px;
        background: #0f172a;
        border-radius: 14px;
        border: 1px dashed #334155;
        color: #94a3b8;
        font-size: 12px;
        font-weight: 600;
    }
</style>

<div class="student-overview-wrapper">
    <div class="overview-grid">
        <!-- 💳 Active Package & Credit Balance -->
        <div class="overview-card">
            <div class="card-title-bar">
                <span class="card-title-text">💳 Active Session Package & Credits</span>
                @if($activePackage)
                    <span class="card-badge badge-teal">✅ Active Package</span>
                @else
                    <span class="card-badge badge-gray">No Active Package</span>
                @endif
            </div>

            @if($activePackage)
                @php
                    $rem = $activePackage->remaining_sessions;
                    $tot = $activePackage->total_sessions;
                    $pct = $tot > 0 ? round(($rem / $tot) * 100) : 0;
                @endphp
                <div style="background: #0f172a; padding: 14px; border-radius: 14px; border: 1px solid #1e293b;">
                    <div style="display: flex; justify-content: space-between; font-size: 13px; font-weight: 800; color: #ffffff;">
                        <span>{{ $activePackage->packageTemplate?->name ?? 'Custom Package' }}</span>
                        <span style="color: #2dd4bf;">{{ $rem }} / {{ $tot }} Sessions Remaining</span>
                    </div>
                    <div class="pkg-progress-bar-bg">
                        <div class="pkg-progress-bar-fill" style="width: {{ $pct }}%;"></div>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 10px; font-weight: 700; color: #94a3b8; margin-top: 8px;">
                        <span>Activated: {{ $activePackage->activated_at ? $activePackage->activated_at->format('d M Y') : 'N/A' }}</span>
                        <span>Expires: {{ $activePackage->expires_at ? $activePackage->expires_at->format('d M Y') : 'No Expiry' }}</span>
                    </div>
                </div>
            @else
                <div class="empty-box">
                    No active package assigned to this student yet. Use the "Assign Package" button in the header actions above to issue credits.
                </div>
            @endif
        </div>

        <!-- 👨‍👩‍👧 Linked Parent Account(s) -->
        <div class="overview-card">
            <div class="card-title-bar">
                <span class="card-title-text">👨‍👩‍👧 Linked Parent / Guarding Accounts</span>
                <span class="card-badge badge-teal">{{ $parents->count() }} Linked</span>
            </div>

            @if($parents->isNotEmpty())
                <div class="item-list">
                    @foreach($parents as $parent)
                        @php $parentUser = $parent->user; @endphp
                        <div class="list-row">
                            <div>
                                <div class="list-row-title">👨‍👩‍👧 {{ $parentUser?->name ?? 'Unknown Parent' }}</div>
                                <div class="list-row-sub">📧 {{ $parentUser?->email ?? 'N/A' }} | 📱 {{ $parentUser?->phone ?? 'N/A' }}</div>
                            </div>
                            <a href="{{ route('filament.admin.resources.parent-profiles.edit', ['record' => $parent->id]) }}" target="_blank" style="font-size: 11px; font-weight: 800; color: #38bdf8; text-decoration: none;">
                                View ↗
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-box">
                    No parent account linked to this student profile yet.
                </div>
            @endif
        </div>

        <!-- 📚 Course Enrollments -->
        <div class="overview-card">
            <div class="card-title-bar">
                <span class="card-title-text">📚 Enrolled Courses</span>
                <span class="card-badge badge-teal">{{ $enrollments->count() }} Courses</span>
            </div>

            @if($enrollments->isNotEmpty())
                <div class="item-list">
                    @foreach($enrollments as $enrollment)
                        @php
                            $eStatus = is_object($enrollment->status) ? ($enrollment->status->value ?? (string)$enrollment->status) : (string)$enrollment->status;
                        @endphp
                        <div class="list-row">
                            <div>
                                <div class="list-row-title">📘 {{ $enrollment->course?->title ?? 'Course' }}</div>
                                <div class="list-row-sub">Status: {{ ucfirst($eStatus) }} | Date: {{ $enrollment->created_at->format('d M Y') }}</div>
                            </div>
                            <span style="font-size: 11px; font-weight: 800; color: #2dd4bf;">Enrolled</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-box">
                    Student has not enrolled in any courses yet.
                </div>
            @endif
        </div>

        <!-- 📝 Homework Submissions -->
        <div class="overview-card">
            <div class="card-title-bar">
                <span class="card-title-text">📝 Recent Homework Submissions</span>
                <span class="card-badge badge-teal">{{ $submissions->count() }} Submissions</span>
            </div>

            @if($submissions->isNotEmpty())
                <div class="item-list">
                    @foreach($submissions as $sub)
                        @php
                            $sStatus = is_object($sub->status) ? ($sub->status->value ?? (string)$sub->status) : (string)$sub->status;
                        @endphp
                        <div class="list-row">
                            <div>
                                <div class="list-row-title">📝 {{ $sub->assignment?->title ?? 'Assignment' }}</div>
                                <div class="list-row-sub">Score: {{ $sub->grade ?? 'Pending Review' }}% | Submitted: {{ $sub->submitted_at ? $sub->submitted_at->format('d M Y') : 'N/A' }}</div>
                            </div>
                            <span style="font-size: 11px; font-weight: 800; color: #fbbf24;">{{ ucfirst($sStatus) }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-box">
                    No homework submissions recorded yet.
                </div>
            @endif
        </div>
    </div>
</div>
