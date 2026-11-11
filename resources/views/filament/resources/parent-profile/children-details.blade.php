@php
    $record = $getRecord();
    $students = $record?->students ?? collect();
    $totalCount = $students->count();
@endphp

<style>
    .linked-children-container {
        /* Light Theme Tokens */
        --ch-bg-card: #FFFFFF;
        --ch-bg-subtle: #F8FAFC;
        --ch-bg-elevated: #F1F5F9;
        --ch-border: #E2E8F0;
        --ch-text-primary: #0F172A;
        --ch-text-secondary: #334155;
        --ch-text-muted: #64748B;
        --ch-teal: #0D9488;
        --ch-sky: #0284C7;
        --ch-shadow: 0 6px 20px -4px rgba(15, 23, 42, 0.08);
        --ch-shadow-hover: 0 16px 32px -8px rgba(13, 148, 136, 0.18);

        width: 100%;
        margin-top: 8px;
        margin-bottom: 8px;
    }

    html.dark .linked-children-container,
    .dark .linked-children-container,
    [data-theme="dark"] .linked-children-container {
        /* Dark Theme Tokens */
        --ch-bg-card: #1E293B;
        --ch-bg-subtle: #0F172A;
        --ch-bg-elevated: #334155;
        --ch-border: #334155;
        --ch-text-primary: #F8FAFC;
        --ch-text-secondary: #CBD5E1;
        --ch-text-muted: #94A3B8;
        --ch-teal: #2DD4BF;
        --ch-sky: #38BDF8;
        --ch-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.35);
        --ch-shadow-hover: 0 20px 40px -10px rgba(20, 184, 166, 0.3);
    }

    .child-card-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
        gap: 20px;
    }
    .child-card {
        position: relative;
        overflow: hidden;
        border-radius: 24px;
        background: var(--ch-bg-card);
        border: 1.5px solid var(--ch-border);
        padding: 22px;
        box-shadow: var(--ch-shadow);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .child-card:hover {
        transform: translateY(-5px);
        border-color: #14b8a6;
        box-shadow: var(--ch-shadow-hover);
    }
    .child-card-top-bar {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #14b8a6, #10b981, #6366f1);
    }
    .child-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-bottom: 14px;
        margin-bottom: 12px;
        border-bottom: 1px solid var(--ch-border);
    }
    .child-info {
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
    }
    .child-info:hover .child-name {
        color: var(--ch-teal);
    }
    .child-avatar {
        width: 46px;
        height: 46px;
        border-radius: 16px;
        background: linear-gradient(135deg, rgba(20, 184, 166, 0.18), rgba(16, 185, 129, 0.18));
        border: 1px solid rgba(20, 184, 166, 0.35);
        color: var(--ch-teal);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
        transition: transform 0.3s ease;
    }
    .child-card:hover .child-avatar {
        transform: scale(1.1) rotate(4deg);
    }
    .child-name {
        font-size: 16px;
        font-weight: 800;
        color: var(--ch-text-primary);
        margin: 0;
        line-height: 1.3;
        transition: color 0.2s ease;
    }
    .child-id-tag {
        display: inline-block;
        font-size: 10px;
        font-weight: 700;
        background: var(--ch-bg-elevated);
        color: var(--ch-text-secondary);
        padding: 2px 8px;
        border-radius: 6px;
        margin-top: 3px;
    }
    .badge-approved {
        background: rgba(16, 185, 129, 0.15);
        color: #10b981;
        border: 1px solid rgba(16, 185, 129, 0.35);
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 800;
        white-space: nowrap;
    }
    .badge-pending {
        background: rgba(245, 158, 11, 0.15);
        color: #d97706;
        border: 1px solid rgba(245, 158, 11, 0.35);
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 800;
        white-space: nowrap;
    }
    html.dark .badge-pending {
        color: #fbbf24;
    }
    .quick-stats-row {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 8px;
        margin-bottom: 12px;
    }
    .stat-chip {
        background: var(--ch-bg-subtle);
        border: 1px solid var(--ch-border);
        border-radius: 12px;
        padding: 8px;
        text-align: center;
    }
    .stat-chip-num {
        font-size: 14px;
        font-weight: 900;
        color: var(--ch-sky);
    }
    .stat-chip-lbl {
        font-size: 9px;
        font-weight: 700;
        color: var(--ch-text-muted);
        text-transform: uppercase;
    }
    .detail-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }
    .detail-item {
        background: var(--ch-bg-subtle);
        border: 1px solid var(--ch-border);
        border-radius: 12px;
        padding: 10px 12px;
    }
    .detail-label {
        display: block;
        font-size: 10px;
        font-weight: 800;
        color: var(--ch-text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }
    .detail-value {
        display: block;
        font-size: 12px;
        font-weight: 700;
        color: var(--ch-text-primary);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .detail-value-teal {
        color: var(--ch-teal);
    }
    .progress-footer {
        margin-top: 14px;
        padding-top: 12px;
        border-top: 1px solid var(--ch-border);
    }
    .progress-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 11px;
        font-weight: 700;
        color: var(--ch-text-secondary);
        margin-bottom: 8px;
    }
    .progress-pill {
        background: rgba(20, 184, 166, 0.15);
        color: var(--ch-teal);
        border: 1px solid rgba(20, 184, 166, 0.3);
        padding: 3px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 800;
    }
    .progress-pill-none {
        background: rgba(148, 163, 184, 0.15);
        color: var(--ch-text-muted);
        border: 1px solid rgba(148, 163, 184, 0.3);
        padding: 3px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 800;
    }
    .progress-bar-bg {
        width: 100%;
        height: 8px;
        background: var(--ch-bg-subtle);
        border-radius: 10px;
        overflow: hidden;
        border: 1px solid var(--ch-border);
    }
    .progress-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, #14b8a6, #10b981);
        border-radius: 10px;
        transition: width 0.5s ease;
    }
    /* Action Buttons Row */
    .card-actions-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
        margin-top: 14px;
    }
    .action-btn-primary {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 8px 12px;
        border-radius: 12px;
        background: linear-gradient(135deg, #14b8a6, #0d9488);
        color: #ffffff;
        font-size: 11px;
        font-weight: 800;
        text-decoration: none;
        box-shadow: 0 4px 12px rgba(20, 184, 166, 0.3);
        transition: all 0.2s ease;
    }
    .action-btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(20, 184, 166, 0.5);
    }
    .action-btn-secondary {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 8px 12px;
        border-radius: 12px;
        background: var(--ch-bg-subtle);
        color: var(--ch-text-secondary);
        border: 1px solid var(--ch-border);
        font-size: 11px;
        font-weight: 800;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .action-btn-secondary:hover {
        background: var(--ch-bg-elevated);
        color: var(--ch-text-primary);
    }
    /* Real-Time Pagination Bar Styles */
    .pagination-toolbar {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: center;
        justify-content: space-between;
        margin-top: 20px;
        padding: 12px 18px;
        background: var(--ch-bg-card);
        border: 1px solid var(--ch-border);
        border-radius: 16px;
    }
    .pagination-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 800;
        background: var(--ch-bg-subtle);
        color: var(--ch-teal);
        border: 1px solid rgba(20, 184, 166, 0.4);
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .pagination-btn:hover:not(:disabled) {
        background: #14b8a6;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(20, 184, 166, 0.3);
    }
    .pagination-btn:disabled {
        opacity: 0.4;
        cursor: not-allowed;
        border-color: var(--ch-border);
        color: var(--ch-text-muted);
    }
    .pagination-numbers {
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .page-number-pill {
        width: 32px;
        height: 32px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 800;
        background: var(--ch-bg-subtle);
        color: var(--ch-text-secondary);
        border: 1px solid var(--ch-border);
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .page-number-pill:hover {
        border-color: #14b8a6;
        color: var(--ch-teal);
    }
    .page-number-pill.active {
        background: #14b8a6;
        color: #ffffff;
        border-color: #14b8a6;
        box-shadow: 0 4px 12px rgba(20, 184, 166, 0.4);
    }
    .pagination-info {
        font-size: 12px;
        font-weight: 700;
        color: var(--ch-text-muted);
    }
    .pagination-accent {
        color: var(--ch-teal);
    }
    .pagination-strong {
        color: var(--ch-text-primary);
        font-weight: 800;
    }
    .per-page-select {
        background: var(--ch-bg-subtle);
        color: var(--ch-teal);
        border: 1px solid rgba(20, 184, 166, 0.4);
        border-radius: 10px;
        padding: 4px 8px;
        font-size: 11px;
        font-weight: 800;
        outline: none;
        cursor: pointer;
    }
    .empty-state {
        padding: 40px 20px;
        text-align: center;
        background: var(--ch-bg-subtle);
        border-radius: 20px;
        border: 2px dashed var(--ch-border);
    }
    .empty-icon {
        font-size: 36px;
        margin-bottom: 8px;
        color: var(--ch-text-muted);
    }
    .empty-title {
        font-size: 15px;
        font-weight: 800;
        color: var(--ch-text-primary);
        margin: 0;
    }
    .empty-desc {
        font-size: 12px;
        color: var(--ch-text-muted);
        margin-top: 4px;
    }
</style>

<div class="linked-children-container"
     x-data="{
        page: 1,
        perPage: 2,
        total: {{ $totalCount }},
        get totalPages() {
            return Math.max(1, Math.ceil(this.total / this.perPage));
        },
        nextPage() {
            if (this.page < this.totalPages) this.page++;
        },
        prevPage() {
            if (this.page > 1) this.page--;
        },
        setPage(p) {
            this.page = p;
        }
     }">

    @if($students->isEmpty())
        <div class="empty-state">
            <div class="empty-icon"><i class="fa-solid fa-people-roof"></i></div>
            <h3 class="empty-title">لم يتم ربط أي أبناء بولي الأمر بعد</h3>
            <p class="empty-desc">
                No linked children for this parent profile yet. Select students from the dropdown above to link them.
            </p>
        </div>
    @else
        <div class="child-card-grid">
            @foreach($students as $index => $child)
                @php
                    $studentProfile = $child->studentProfile;
                    $gradeName = $studentProfile?->gradeLevel?->name ?: 'غير محدد';
                    $schoolName = $studentProfile?->school_name ?: 'غير محددة';
                    $phoneStr = $child->phone ?: 'غير متوفر';

                    $isApproved = $child->status === \App\Enums\AccountStatus::APPROVED || $child->status === 'approved';

                    $activePkg = \App\Models\StudentPackage::where('student_user_id', $child->id)->where('status', 'active')->first();
                    $remaining = $activePkg?->remaining_sessions ?? 0;
                    $total = $activePkg?->total_sessions ?? 0;
                    $percent = $total > 0 ? min(100, round(($remaining / $total) * 100)) : 0;

                    $submissionsCount = \App\Models\AssignmentSubmission::where('student_user_id', $child->id)->count();
                    $exceptionsCount = \App\Models\ExceptionRequest::where('student_user_id', $child->id)->count();
                    $enrollmentsCount = \App\Models\CourseEnrollment::where('student_user_id', $child->id)->count();

                    $studentProfileUrl = $studentProfile ? route('filament.admin.resources.student-profiles.edit', ['record' => $studentProfile->id]) : '#';
                    $userEditUrl = route('filament.admin.resources.users.edit', ['record' => $child->id]);
                @endphp

                <div class="child-card"
                     x-show="{{ $index }} >= (page - 1) * perPage && {{ $index }} < page * perPage"
                     x-transition:enter="transition ease-out duration-300 transform"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100">
                    <div class="child-card-top-bar"></div>

                    <div>
                        <!-- Header: Clickable Link to Student Profile -->
                        <div class="child-header">
                            <a href="{{ $studentProfileUrl }}" target="_blank" class="child-info" title="انقر لعرض وإدارة ملف الطالب بالكامل">
                                <div class="child-avatar"><i class="fa-solid fa-graduation-cap"></i></div>
                                <div>
                                    <h4 class="child-name">
                                        {{ $child->name }} ↗
                                    </h4>
                                    <span class="child-id-tag">ID: #{{ $child->id }}</span>
                                </div>
                            </a>

                            <div>
                                @if($isApproved)
                                    <span class="badge-approved"><i class="fa-solid fa-circle-check text-emerald-500"></i> مقبول (Approved)</span>
                                @else
                                    <span class="badge-pending"><i class="fa-solid fa-hourglass-half"></i> قيد المراجعة (Pending)</span>
                                @endif
                            </div>
                        </div>

                        <!-- Quick Metrics Chips -->
                        <div class="quick-stats-row">
                            <div class="stat-chip">
                                <div class="stat-chip-num">{{ $enrollmentsCount }}</div>
                                <div class="stat-chip-lbl">الكورسات (Courses)</div>
                            </div>
                            <div class="stat-chip">
                                <div class="stat-chip-num">{{ $submissionsCount }}</div>
                                <div class="stat-chip-lbl">الواجبات (Homework)</div>
                            </div>
                            <div class="stat-chip">
                                <div class="stat-chip-num">{{ $exceptionsCount }}</div>
                                <div class="stat-chip-lbl">الأعذار (Exceptions)</div>
                            </div>
                        </div>

                        <!-- Details 2x2 Grid -->
                        <div class="detail-grid">
                            <div class="detail-item">
                                <span class="detail-label">البريد / Email</span>
                                <span class="detail-value" title="{{ $child->email }}"><i class="fa-solid fa-envelope"></i> {{ $child->email }}</span>
                            </div>

                            <div class="detail-item">
                                <span class="detail-label">الهاتف / Phone</span>
                                <span class="detail-value" title="{{ $phoneStr }}"><i class="fa-solid fa-mobile-screen"></i> {{ $phoneStr }}</span>
                            </div>

                            <div class="detail-item">
                                <span class="detail-label">الصف / Grade</span>
                                <span class="detail-value detail-value-teal" title="{{ $gradeName }}"><i class="fa-solid fa-school"></i> {{ $gradeName }}</span>
                            </div>

                            <div class="detail-item">
                                <span class="detail-label">المدرسة / School</span>
                                <span class="detail-value" title="{{ $schoolName }}"><i class="fa-solid fa-building"></i> {{ $schoolName }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Package Progress Bar Footer -->
                    <div>
                        <div class="progress-footer">
                            <div class="progress-header">
                                <span><i class="fa-solid fa-credit-card"></i> رصيد الباقة / Package Balance:</span>
                                @if($activePkg)
                                    <span class="progress-pill">{{ $remaining }} / {{ $total }} Sessions</span>
                                @else
                                    <span class="progress-pill-none">لا توجد باقة نشطة</span>
                                @endif
                            </div>

                            @if($activePkg && $total > 0)
                                <div class="progress-bar-bg">
                                    <div class="progress-bar-fill" style="width: {{ $percent }}%;"></div>
                                </div>
                            @endif
                        </div>

                        <!-- Action Buttons Row -->
                        <div class="card-actions-row">
                            <a href="{{ $studentProfileUrl }}" target="_blank" class="action-btn-primary" title="الانتقال لبروفايل الطالب وإدارته بالكامل">
                                <i class="fa-solid fa-bolt"></i> <span>إدارة الطالب (Manage Student)</span>
                            </a>
                            <a href="{{ $userEditUrl }}" target="_blank" class="action-btn-secondary" title="إدارة حساب المستخدم والصلاحيات">
                                <i class="fa-solid fa-user"></i> <span>إدارة الحساب (User Account)</span>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Real-Time Interactive Pagination Toolbar -->
        @if($totalCount > 2)
            <div class="pagination-toolbar">
                <button class="pagination-btn"
                        @click="prevPage()"
                        :disabled="page === 1">
                    <span>◀</span>
                    <span>السابق (Prev)</span>
                </button>

                <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 12px;">
                    <span class="pagination-info">
                        صفحة <strong x-text="page" class="pagination-accent"></strong> من <strong x-text="totalPages" class="pagination-accent"></strong> (إجمالي <span class="pagination-strong">{{ $totalCount }}</span> أبناء)
                    </span>

                    <!-- Page Number Pills -->
                    <div class="pagination-numbers">
                        <template x-for="p in totalPages" :key="p">
                            <div class="page-number-pill"
                                 :class="{ 'active': page === p }"
                                 @click="setPage(p)"
                                 x-text="p"></div>
                        </template>
                    </div>

                    <!-- Cards Per Page Selector -->
                    <div style="display: flex; align-items: center; gap: 6px; margin-inline-start: 8px;">
                        <span style="font-size: 11px; font-weight: 700; color: var(--ch-text-muted);">العرض:</span>
                        <select class="per-page-select" x-model.number="perPage" @change="page = 1">
                            <option value="2">2 كروت / صفحة</option>
                            <option value="4">4 كروت / صفحة</option>
                            <option value="6">6 كروت / صفحة</option>
                        </select>
                    </div>
                </div>

                <button class="pagination-btn"
                        @click="nextPage()"
                        :disabled="page === totalPages">
                    <span>التالي (Next)</span>
                    <span>▶</span>
                </button>
            </div>
        @endif
    @endif
</div>
