@php
    $record = $getRecord();
    $students = $record?->students ?? collect();
    $totalCount = $students->count();
@endphp

<style>
    .linked-children-container {
        width: 100%;
        margin-top: 8px;
        margin-bottom: 8px;
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
        background: #1e293b;
        border: 1px solid #334155;
        padding: 22px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .child-card:hover {
        transform: translateY(-5px);
        border-color: #14b8a6;
        box-shadow: 0 20px 40px -10px rgba(20, 184, 166, 0.3);
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
        border-bottom: 1px solid #334155;
    }
    .child-info {
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
    }
    .child-info:hover .child-name {
        color: #2dd4bf;
    }
    .child-avatar {
        width: 46px;
        height: 46px;
        border-radius: 16px;
        background: linear-gradient(135deg, rgba(20, 184, 166, 0.25), rgba(16, 185, 129, 0.25));
        border: 1px solid rgba(20, 184, 166, 0.4);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        flex-shrink: 0;
        transition: transform 0.3s ease;
    }
    .child-card:hover .child-avatar {
        transform: scale(1.1) rotate(4deg);
    }
    .child-name {
        font-size: 16px;
        font-weight: 800;
        color: #ffffff;
        margin: 0;
        line-height: 1.3;
        transition: color 0.2s ease;
    }
    .child-id-tag {
        display: inline-block;
        font-size: 10px;
        font-weight: 700;
        background: #334155;
        color: #cbd5e1;
        padding: 2px 8px;
        border-radius: 6px;
        margin-top: 3px;
    }
    .badge-approved {
        background: rgba(16, 185, 129, 0.15);
        color: #34d399;
        border: 1px solid rgba(16, 185, 129, 0.3);
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 800;
        white-space: nowrap;
    }
    .badge-pending {
        background: rgba(245, 158, 11, 0.15);
        color: #fbbf24;
        border: 1px solid rgba(245, 158, 11, 0.3);
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 800;
        white-space: nowrap;
    }
    .quick-stats-row {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 8px;
        margin-bottom: 12px;
    }
    .stat-chip {
        background: #0f172a;
        border: 1px solid #334155;
        border-radius: 12px;
        padding: 8px;
        text-align: center;
    }
    .stat-chip-num {
        font-size: 14px;
        font-weight: 900;
        color: #38bdf8;
    }
    .stat-chip-lbl {
        font-size: 9px;
        font-weight: 700;
        color: #94a3b8;
        text-transform: uppercase;
    }
    .detail-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }
    .detail-item {
        background: #0f172a;
        border: 1px solid #1e293b;
        border-radius: 12px;
        padding: 10px 12px;
    }
    .detail-label {
        display: block;
        font-size: 10px;
        font-weight: 800;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }
    .detail-value {
        display: block;
        font-size: 12px;
        font-weight: 700;
        color: #f1f5f9;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .detail-value-teal {
        color: #2dd4bf;
    }
    .progress-footer {
        margin-top: 14px;
        padding-top: 12px;
        border-top: 1px solid #334155;
    }
    .progress-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 11px;
        font-weight: 700;
        color: #cbd5e1;
        margin-bottom: 8px;
    }
    .progress-pill {
        background: rgba(20, 184, 166, 0.15);
        color: #2dd4bf;
        border: 1px solid rgba(20, 184, 166, 0.3);
        padding: 3px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 800;
    }
    .progress-pill-none {
        background: rgba(148, 163, 184, 0.15);
        color: #94a3b8;
        border: 1px solid rgba(148, 163, 184, 0.3);
        padding: 3px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 800;
    }
    .progress-bar-bg {
        width: 100%;
        height: 8px;
        background: #0f172a;
        border-radius: 10px;
        overflow: hidden;
        border: 1px solid #334155;
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
        background: #0f172a;
        color: #cbd5e1;
        border: 1px solid #334155;
        font-size: 11px;
        font-weight: 800;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .action-btn-secondary:hover {
        background: #334155;
        color: #ffffff;
    }
    /* Real-Time Pagination Bar Styles */
    .pagination-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 20px;
        padding: 12px 18px;
        background: #0f172a;
        border: 1px solid #334155;
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
        background: #1e293b;
        color: #14b8a6;
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
        border-color: #334155;
        color: #64748b;
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
        background: #1e293b;
        color: #cbd5e1;
        border: 1px solid #334155;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .page-number-pill:hover {
        border-color: #14b8a6;
        color: #14b8a6;
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
        color: #94a3b8;
    }
    .per-page-select {
        background: #1e293b;
        color: #14b8a6;
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
        background: #1e293b;
        border-radius: 20px;
        border: 2px dashed #334155;
    }
    .empty-icon {
        font-size: 36px;
        margin-bottom: 8px;
    }
    .empty-title {
        font-size: 15px;
        font-weight: 800;
        color: #f8fafc;
        margin: 0;
    }
    .empty-desc {
        font-size: 12px;
        color: #94a3b8;
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
            <div class="empty-icon">👨‍👩‍👧</div>
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
                                <div class="child-avatar">🎓</div>
                                <div>
                                    <h4 class="child-name">
                                        {{ $child->name }} ↗
                                    </h4>
                                    <span class="child-id-tag">ID: #{{ $child->id }}</span>
                                </div>
                            </a>

                            <div>
                                @if($isApproved)
                                    <span class="badge-approved">✅ مقبول (Approved)</span>
                                @else
                                    <span class="badge-pending">⏳ قيد المراجعة (Pending)</span>
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
                                <span class="detail-value" title="{{ $child->email }}">📧 {{ $child->email }}</span>
                            </div>

                            <div class="detail-item">
                                <span class="detail-label">الهاتف / Phone</span>
                                <span class="detail-value" title="{{ $phoneStr }}">📱 {{ $phoneStr }}</span>
                            </div>

                            <div class="detail-item">
                                <span class="detail-label">الصف / Grade</span>
                                <span class="detail-value detail-value-teal" title="{{ $gradeName }}">🏫 {{ $gradeName }}</span>
                            </div>

                            <div class="detail-item">
                                <span class="detail-label">المدرسة / School</span>
                                <span class="detail-value" title="{{ $schoolName }}">🏢 {{ $schoolName }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Package Progress Bar Footer -->
                    <div>
                        <div class="progress-footer">
                            <div class="progress-header">
                                <span>💳 رصيد الباقة / Package Balance:</span>
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
                                ⚡ <span>إدارة الطالب (Manage Student)</span>
                            </a>
                            <a href="{{ $userEditUrl }}" target="_blank" class="action-btn-secondary" title="إدارة حساب المستخدم والصلاحيات">
                                👤 <span>إدارة الحساب (User Account)</span>
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

                <div class="flex items-center gap-3">
                    <span class="pagination-info">
                        صفحة <strong x-text="page" class="text-teal-400"></strong> من <strong x-text="totalPages" class="text-teal-400"></strong> (إجمالي <span class="text-white font-extrabold">{{ $totalCount }}</span> أبناء)
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
                    <div class="flex items-center gap-1.5 ml-2">
                        <span class="text-[11px] font-bold text-slate-400">العرض:</span>
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
