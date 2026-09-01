@extends('layouts.portal-panel')

@section('content')
{{-- Specialized Print CSS Styling --}}
<style>
@media print {
    /* Hide all web UI chrome, navigation, headers, footers, modals, & buttons */
    header, footer, nav, aside, #portalSidebar, .no-print, #section-children, #linkChildModal, .btn-lift, button, .breadcrumb, [role="navigation"], section:first-of-type {
        display: none !important;
    }
    
    body, html {
        background: #ffffff !important;
        color: #0f172a !important;
        margin: 0 !important;
        padding: 0 !important;
        font-size: 11pt !important;
        font-family: 'Cairo', sans-serif !important;
        width: 100% !important;
    }

    /* Print Container Styling */
    section {
        padding: 0 !important;
        background: #ffffff !important;
    }

    #progressResult {
        border: 2px solid #0d9488 !important;
        box-shadow: none !important;
        padding: 20px !important;
        margin: 0 auto !important;
        width: 100% !important;
        border-radius: 16px !important;
        background: #ffffff !important;
        page-break-inside: avoid;
    }

    .print-official-header {
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        border-bottom: 2px solid #0d9488 !important;
        padding-bottom: 12px !important;
        margin-bottom: 20px !important;
    }

    .print-watermark-stamp {
        display: block !important;
        border: 2px dashed #0d9488 !important;
        padding: 6px 14px !important;
        border-radius: 12px !important;
        color: #0d9488 !important;
        font-weight: 800 !important;
        font-size: 10pt !important;
        text-align: center !important;
        background: #f0fdf4 !important;
    }

    /* Preserve colors & layout in print PDF */
    * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    .grid {
        display: grid !important;
    }
}
</style>

{{-- Parent Portal Header --}}
<section id="overview" class="rounded-3xl py-8 md:py-10 px-6 md:px-8 bg-gradient-to-r from-slate-900 via-slate-950 to-teal-950 text-white border border-slate-800 shadow-xl no-print">
    <div class="space-y-4">
        @include('components.breadcrumb', [
            'items' => [
                ['label' => __('navbar.home'), 'route' => 'home'],
                ['label' => __('navbar.parent_portal')],
            ]
        ])

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="space-y-2">
                <span class="inline-block text-xs font-mono uppercase tracking-widest text-teal-400 font-extrabold bg-teal-950/80 px-3.5 py-1.5 rounded-full border border-teal-800/80 shadow-xs">
                    👨‍👧‍👦 {{ __('PARENT DASHBOARD • ACADEMIC MONITORING') }}
                </span>
                <h1 class="font-heading text-3xl sm:text-4xl font-black text-white tracking-tight">
                    {{ __('Parent Portal: Student Academic Dashboard') }}
                </h1>
                <p class="text-slate-300 text-sm font-mono max-w-2xl">
                    {{ __('Monitor your children’s live stream classes, attendance records, homework submissions, active packages, and credit usage in real-time.') }}
                </p>
            </div>
            
            <div class="flex flex-wrap items-center gap-3">
                <button type="button" onclick="openLinkChildModal()" class="btn-lift px-5 py-2.5 bg-teal-500 hover:bg-teal-400 text-slate-950 font-black text-xs rounded-2xl shadow-lg shadow-teal-500/20 transition-all flex items-center justify-center gap-2 cursor-pointer">
                    <span>➕</span> {{ __('Link New Child by Phone') }}
                </button>

                @php
                    $whatsappNumber = \App\Models\SiteSetting::get('owner_whatsapp', '+201000000000');
                    $cleanWhatsapp = preg_replace('/[^0-9]/', '', $whatsappNumber);
                @endphp
                <a href="https://wa.me/{{ $cleanWhatsapp }}?text={{ urlencode(__('Hello Elite Academy Admin, I am a parent inquiring about package renewal.')) }}" target="_blank" class="btn-lift px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-2xl border border-emerald-400/40 shadow-sm flex items-center gap-2">
                    <span>💬</span> {{ __('WhatsApp Payment & Renewal') }}
                </a>

                <div class="inline-flex items-center gap-2 bg-amber-500/20 px-3.5 py-2 rounded-2xl border border-amber-500/30 text-xs font-mono text-amber-300 font-bold">
                    <span>🔒</span>
                    <span>{{ __('Read-Only Monitoring') }}</span>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Main Dashboard Body --}}
<div class="space-y-8">

    {{-- Section 1: Children Selector Grid --}}
    <div id="section-children" class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200/90 dark:border-slate-800 shadow-xl space-y-6 scroll-mt-28 no-print">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-100 pb-4 gap-4">
                <div>
                    <h2 class="font-heading font-black text-2xl text-slate-900 flex items-center gap-2.5">
                        <span>👨‍👩‍👧‍👦</span> {{ __('Your Linked Children') }}
                    </h2>
                    <p class="text-xs font-mono text-slate-500 mt-1">{{ __('Select a child to inspect detailed academic performance, package & attendance.') }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <span id="linkedCountBadge" class="text-xs font-mono font-extrabold text-teal-700 bg-teal-50 px-3 py-1.5 rounded-full border border-teal-200">
                        {{ count($linkedStudents) }} {{ __('Children Linked') }}
                    </span>
                    <button type="button" onclick="openLinkChildModal()" class="text-xs font-bold font-mono text-teal-600 hover:text-teal-700 underline">
                        + {{ __('Link New Child') }}
                    </button>
                </div>
            </div>

            <div id="linkedChildrenGrid" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @forelse($linkedStudents as $st)
                    <div id="child-card-{{ $st->user_id }}" onclick="loadStudentProgress({{ $st->user_id }})" class="child-card cursor-pointer bg-slate-50 hover:bg-teal-50/60 transition-all duration-300 rounded-2xl p-6 border-2 border-slate-200 hover:border-teal-500 shadow-sm space-y-4 group">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-teal-600 text-white font-heading font-black text-xl flex items-center justify-center shadow-md">
                                {{ mb_substr($st->user?->name ?: 'S', 0, 1) }}
                            </div>
                            <div>
                                <h3 class="font-bold text-base text-slate-900 group-hover:text-teal-700 transition-colors">{{ $st->user?->name }}</h3>
                                <span class="text-[11px] font-mono font-bold bg-teal-100 text-teal-800 px-2.5 py-0.5 rounded-md">
                                    {{ $st->gradeLevel?->name ?: __('Grade Level') }}
                                </span>
                            </div>
                        </div>

                        <div class="text-xs font-mono text-slate-500 space-y-1 pt-2 border-t border-slate-200/60">
                            <p class="truncate">🏫 {{ $st->school_name ?: __('Elite STEM Academy') }}</p>
                            <p class="text-teal-600 font-bold">✔ {{ __('Independent Student Account') }}</p>
                        </div>

                        <button type="button" class="w-full py-2 bg-white group-hover:bg-teal-600 group-hover:text-white text-slate-800 rounded-xl text-xs font-bold font-mono border border-slate-200 transition-all shadow-xs">
                            {{ __('Inspect Child Dashboard') }} &rarr;
                        </button>
                    </div>
                @empty
                    <div id="emptyChildrenBox" class="col-span-3 text-center py-12 bg-slate-50 rounded-2xl border border-slate-200 text-slate-500 space-y-3">
                        <div class="text-4xl">👨‍👩‍👧‍👦</div>
                        <h3 class="font-bold text-base text-slate-800">{{ __('No Linked Children Found') }}</h3>
                        <p class="text-xs font-mono text-slate-500 max-w-md mx-auto">
                            {{ __('Link your child by entering their registered phone number or email address.') }}
                        </p>
                        <button type="button" onclick="openLinkChildModal()" class="btn-lift px-5 py-2.5 bg-teal-600 text-white font-bold text-xs rounded-xl shadow-md">
                            ➕ {{ __('Link Child Account Now') }}
                        </button>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Section 2: Selected Child Detailed Performance Panel --}}
        <div id="section-progress" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xl space-y-6 scroll-mt-28">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
                <div>
                    <h3 id="selectedStudentName" class="font-heading font-black text-2xl text-slate-900">{{ __('Student Academic Overview') }}</h3>
                    <p id="selectedStudentMeta" class="text-xs font-mono text-teal-600 font-bold mt-0.5"></p>
                </div>
                <div class="flex items-center gap-3 no-print">
                    <span id="packageBadge" class="text-xs font-mono font-bold bg-teal-100 text-teal-800 px-3 py-1.5 rounded-xl border border-teal-200"></span>
                    <span id="attendanceBadge" class="text-xs font-mono font-bold bg-emerald-100 text-emerald-800 px-3 py-1.5 rounded-xl border border-emerald-200"></span>
                </div>
            </div>

            <div id="progressContent" class="space-y-6">
                @if(count($linkedStudents) === 0)
                    <div class="p-8 bg-slate-50 rounded-2xl border border-slate-200 text-center space-y-3">
                        <p class="text-sm font-bold text-slate-700">{{ __('Please link a child account above using their phone number to view academic reports.') }}</p>
                        <button type="button" onclick="openLinkChildModal()" class="px-4 py-2 bg-teal-600 text-white rounded-xl text-xs font-bold">➕ {{ __('Link Child Account') }}</button>
                    </div>
                @else
                    <div class="p-8 text-center text-xs font-mono text-slate-500 font-bold">{{ __('Loading student progress metrics...') }}</div>
                @endif
            </div>
        </div>

    </div>
</section>

{{-- Link New Child Modal --}}
<div id="linkChildModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4 hidden no-print">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full border border-slate-200 shadow-2xl space-y-6 relative anim-lift">
        <button type="button" onclick="closeLinkChildModal()" class="absolute top-5 right-5 text-slate-400 hover:text-slate-700 text-lg font-bold">✕</button>

        <div class="space-y-2">
            <div class="w-12 h-12 bg-teal-50 text-teal-600 rounded-2xl flex items-center justify-center text-2xl font-bold border border-teal-200">
                🔗
            </div>
            <h3 class="font-heading font-black text-xl text-slate-900">{{ __('Link New Child Account') }}</h3>
            <p class="text-xs text-slate-500 font-medium">
                {{ __('Enter the phone number or registered email address of your student to link their account for monitoring.') }}
            </p>
        </div>

        <form id="linkChildForm" onsubmit="handleLinkChildSubmit(event)" class="space-y-4">
            @csrf
            <div class="space-y-1">
                <label class="block text-xs font-mono font-extrabold text-slate-600 uppercase">{{ __('Student Phone Number or Email') }}</label>
                <input type="text" id="phone_or_email" name="phone_or_email" required placeholder="e.g. 01012345678 or student@email.com" class="w-full h-11 bg-slate-50 border border-slate-300 rounded-xl px-4 text-sm font-semibold text-slate-900 focus:outline-teal-600">
            </div>

            <div id="linkChildFeedback" class="hidden text-xs font-bold p-3 rounded-xl"></div>

            <div class="pt-2 flex items-center justify-end gap-3">
                <button type="button" onclick="closeLinkChildModal()" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl border border-slate-200">
                    {{ __('Cancel') }}
                </button>
                <button type="submit" id="linkSubmitBtn" class="px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-extrabold rounded-xl shadow-md shadow-teal-600/20">
                    {{ __('Link Child Account') }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    @if(count($linkedStudents) > 0)
        loadStudentProgress({{ $linkedStudents->first()->user_id }});
    @endif
});

function openLinkChildModal() {
    const modal = document.getElementById('linkChildModal');
    if (modal) {
        modal.classList.remove('hidden');
        const input = document.getElementById('phone_or_email');
        if (input) input.focus();
    }
}

function closeLinkChildModal() {
    const modal = document.getElementById('linkChildModal');
    if (modal) modal.classList.add('hidden');
    const feedback = document.getElementById('linkChildFeedback');
    if (feedback) feedback.classList.add('hidden');
    const form = document.getElementById('linkChildForm');
    if (form) form.reset();
}

async function handleLinkChildSubmit(e) {
    e.preventDefault();
    const input = document.getElementById('phone_or_email').value;
    const feedback = document.getElementById('linkChildFeedback');
    const submitBtn = document.getElementById('linkSubmitBtn');

    submitBtn.disabled = true;
    submitBtn.textContent = 'Linking...';
    feedback.classList.add('hidden');

    try {
        const res = await fetch("{{ route('ajax.parent.link-child') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                'Accept': 'application/json'
            },
            body: JSON.stringify({ phone_or_email: input })
        });
        const data = await res.json();

        if (data.success) {
            feedback.className = 'text-xs font-bold p-3 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200 block';
            feedback.textContent = data.message;
            
            setTimeout(() => {
                closeLinkChildModal();
                window.location.reload();
            }, 1200);
        } else {
            feedback.className = 'text-xs font-bold p-3 rounded-xl bg-rose-50 text-rose-800 border border-rose-200 block';
            feedback.textContent = data.message || 'Error linking student.';
        }
    } catch (err) {
        feedback.className = 'text-xs font-bold p-3 rounded-xl bg-rose-50 text-rose-800 border border-rose-200 block';
        feedback.textContent = 'Network error. Please try again.';
    } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = "{{ __('Link Child Account') }}";
    }
}

async function loadStudentProgress(studentId) {
    const content = document.getElementById('progressContent');
    const nameEl = document.getElementById('selectedStudentName');
    const metaEl = document.getElementById('selectedStudentMeta');
    const pkgBadge = document.getElementById('packageBadge');
    const attBadge = document.getElementById('attendanceBadge');

    document.querySelectorAll('.child-card').forEach(card => card.classList.remove('ring-4', 'ring-teal-500/40', 'border-teal-500', 'bg-teal-50/40'));
    const activeCard = document.getElementById(`child-card-${studentId}`);
    if (activeCard) {
        activeCard.classList.add('ring-4', 'ring-teal-500/40', 'border-teal-500', 'bg-teal-50/40');
    }

    content.innerHTML = '<div class="p-8 text-center text-xs font-mono text-slate-500 font-bold">{{ __("Loading student progress metrics...") }}</div>';

    try {
        const baseUrl = "{{ url('/ajax/parent/student') }}";
        const res = await fetch(`${baseUrl}/${studentId}/progress`, {
            headers: { 'Accept': 'application/json' }
        });
        const data = await res.json();

        if (!data.success) {
            content.innerHTML = `<div class="p-6 bg-red-50 text-red-700 rounded-2xl border border-red-200 text-xs font-bold">${data.message}</div>`;
            return;
        }

        const isAr = "{{ app()->getLocale() }}" === "ar";
        const todayStr = new Date().toLocaleDateString(isAr ? 'ar-EG' : 'en-US');

        nameEl.textContent = isAr ? `تقرير الأداء الأكاديمي الشامل: ${data.student.name}` : `Comprehensive Academic Performance Report: ${data.student.name}`;
        metaEl.textContent = `${data.student.grade} • ${isAr ? 'المدرسة' : 'School'}: ${data.student.school} • ${data.student.phone ? ('📞 ' + data.student.phone) : ''}`;
        pkgBadge.textContent = `💳 ${data.package.name} (${data.package.remaining_sessions} ${isAr ? 'حصص متبقية' : 'sessions remaining'})`;
        attBadge.textContent = `🎯 ${isAr ? 'نسبة الحضور' : 'Attendance'}: ${data.attendance.rate}`;

        const pkgPct = Math.round((data.package.remaining_sessions / data.package.total_sessions) * 100);

        let html = `
            {{-- Official Print Report Header --}}
            <div class="print-official-header hidden flex justify-between items-center pb-4 mb-4 border-b-2 border-teal-600">
                <div class="space-y-1">
                    <h2 class="font-heading font-black text-xl text-teal-950">أكاديمية إيليت — ELITE ACADEMY</h2>
                    <p class="text-xs font-mono text-slate-600">${isAr ? 'كشف تقرير الأداء الأكاديمي الرسمي والمتابعة الشاملة' : 'Official Student Academic Performance & Monitoring Report'}</p>
                </div>
                <div class="print-watermark-stamp text-xs font-mono font-bold text-teal-800 bg-teal-50 px-3 py-1.5 rounded-xl border border-teal-300">
                    ✔ ${isAr ? 'مستند معتمد' : 'Verified Report'} — ${todayStr}
                </div>
            </div>

            {{-- Security & Official Print Header --}}
            <div class="p-4 bg-amber-50 rounded-2xl border border-amber-200 text-xs font-semibold text-amber-900 flex items-center justify-between flex-wrap gap-2 no-print">
                <div class="flex items-center gap-2">
                    <span class="text-base">🔒</span>
                    <span>${isAr ? 'لوحة المتابعة الأكاديمية الرسمية لولي الأمر (محدثة لحظياً بروابط مباشرة مع سجلات المعلمين والدروس).' : 'Official Parent Academic Monitoring Dashboard (Synced in real-time with teacher gradebooks and lecture logs).'}</span>
                </div>
                <button type="button" onclick="window.print()" class="btn-lift px-4 py-2 bg-slate-900 hover:bg-teal-600 text-white text-xs font-mono rounded-xl font-bold transition-all flex items-center gap-1.5 cursor-pointer shadow-md">
                    <span>🖨️</span> ${isAr ? 'طباعة التقرير والشهادة' : 'Print Official Certificate'}
                </button>
            </div>

            {{-- 4 Primary Academic KPI Summary Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                {{-- 1. Average Academic Grade --}}
                <div class="p-5 bg-gradient-to-br from-teal-50 to-emerald-50 rounded-3xl border border-teal-200/80 space-y-2 shadow-xs">
                    <div class="flex justify-between items-center text-xs font-mono font-bold text-teal-800">
                        <span>🌟 ${isAr ? 'المعدل العام للدرجات' : 'Overall Academic GPA'}</span>
                        <span class="bg-teal-200 text-teal-900 px-2 py-0.5 rounded-md text-[10px] font-extrabold">${data.average_grade >= 85 ? (isAr ? 'ممتاز' : 'Excellent') : (isAr ? 'جيد جداً' : 'Good')}</span>
                    </div>
                    <p class="font-heading font-black text-3xl text-teal-950">${data.average_grade}%</p>
                    <p class="text-[11px] font-mono text-teal-700 font-semibold leading-relaxed">
                        ${isAr ? 'متوسط درجات جميع الواجبات والاختبارات المنجزة' : 'Average score across all graded homework & exams'}
                    </p>
                </div>

                {{-- 2. Attendance Index --}}
                <div class="p-5 bg-gradient-to-br from-emerald-50 to-teal-50 rounded-3xl border border-emerald-200/80 space-y-2 shadow-xs">
                    <div class="flex justify-between items-center text-xs font-mono font-bold text-emerald-800">
                        <span>🎯 ${isAr ? 'نسبة حضور البث المباشر' : 'Live Stream Attendance'}</span>
                        <span class="bg-emerald-200 text-emerald-900 px-2 py-0.5 rounded-md text-[10px] font-extrabold">${data.attendance.rate}</span>
                    </div>
                    <p class="font-heading font-black text-3xl text-emerald-950">${data.attendance.rate}</p>
                    <p class="text-[11px] font-mono text-emerald-700 font-semibold leading-relaxed">
                        ${data.attendance.attended_count} ${isAr ? 'حصة حضور' : 'attended'} • ${data.attendance.absences_count} ${isAr ? 'غياب' : 'absent'}
                    </p>
                </div>

                {{-- 3. Homework Submissions Completed --}}
                <div class="p-5 bg-gradient-to-br from-indigo-50 to-slate-50 rounded-3xl border border-indigo-200/80 space-y-2 shadow-xs">
                    <div class="flex justify-between items-center text-xs font-mono font-bold text-indigo-800">
                        <span>📝 ${isAr ? 'الواجبات والتسليمات' : 'Assignments & MSQs'}</span>
                        <span class="bg-indigo-200 text-indigo-900 px-2 py-0.5 rounded-md text-[10px] font-extrabold">${data.submissions_count} ${isAr ? 'تسليم' : 'done'}</span>
                    </div>
                    <p class="font-heading font-black text-3xl text-indigo-950">${data.submissions_count}</p>
                    <p class="text-[11px] font-mono text-indigo-700 font-semibold leading-relaxed">
                        ${isAr ? 'تم تسليمها وتصحيحها ومراجعتها بنجاح' : 'Submitted, graded and reviewed by mentors'}
                    </p>
                </div>

                {{-- 4. Active Package & Credits --}}
                <div class="p-5 bg-gradient-to-br from-amber-50 to-orange-50 rounded-3xl border border-amber-200/80 space-y-2 shadow-xs">
                    <div class="flex justify-between items-center text-xs font-mono font-bold text-amber-800">
                        <span>💳 ${isAr ? 'رصيد الحصص المتبقية' : 'Session Credits'}</span>
                        <span class="bg-amber-200 text-amber-900 px-2 py-0.5 rounded-md text-[10px] font-extrabold capitalize">${data.package.status}</span>
                    </div>
                    <p class="font-heading font-black text-3xl text-amber-950">${data.package.remaining_sessions} <span class="text-xs font-mono font-bold text-amber-800">${isAr ? 'حصة متبقية' : 'left'}</span></p>
                    <div class="space-y-1 pt-0.5">
                        <div class="flex justify-between text-[10px] font-mono text-amber-800 font-bold">
                            <span>${isAr ? 'المستخدم' : 'Used'}: ${data.package.used_sessions}/${data.package.total_sessions}</span>
                            <span>${pkgPct}%</span>
                        </div>
                        <div class="w-full h-1.5 bg-amber-200 rounded-full overflow-hidden">
                            <div class="h-full bg-amber-600 rounded-full transition-all duration-500" style="width: ${pkgPct}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section: Enrolled Courses & Detailed Module Progress --}}
            <div id="section-courses" class="space-y-4 pt-4 scroll-mt-28">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h4 class="font-heading font-black text-lg text-slate-900 flex items-center gap-2">
                        <span>📚</span> ${isAr ? 'المقررات الدراسية ونسب إنجاز المنهج' : 'Enrolled Courses & Curriculum Completion'}
                    </h4>
                    <span class="text-xs font-mono font-bold bg-teal-100 text-teal-800 px-3 py-1 rounded-full border border-teal-200">
                        ${data.courses ? data.courses.length : 0} ${isAr ? 'كورسات مسجلة' : 'Active Courses'}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        `;

        if (data.courses && data.courses.length > 0) {
            data.courses.forEach(c => {
                html += `
                    <div class="p-5 bg-slate-50 hover:bg-white rounded-3xl border border-slate-200/90 shadow-xs transition-all space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="bg-teal-600 text-white text-[11px] font-bold px-2.5 py-0.5 rounded-full shadow-2xs">${c.subject}</span>
                            <span class="text-xs font-mono font-bold text-teal-700 bg-teal-50 px-2.5 py-0.5 rounded-full border border-teal-200">${c.progress_pct}% ${isAr ? 'إنجاز' : 'Completed'}</span>
                        </div>
                        <div>
                            <h5 class="font-bold text-base text-slate-900 leading-snug">${c.title}</h5>
                            <p class="text-xs font-mono text-slate-500 mt-0.5">👨‍🏫 ${isAr ? 'المحاضر' : 'Instructor'}: <strong>${c.teacher}</strong></p>
                        </div>
                        <div class="space-y-1.5 pt-1">
                            <div class="w-full h-2 bg-slate-200 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-teal-500 to-emerald-400 rounded-full transition-all duration-500" style="width: ${Math.max(8, c.progress_pct)}%"></div>
                            </div>
                            <div class="flex justify-between text-[11px] font-mono text-slate-500">
                                <span>📹 ${c.completed_sessions} ${isAr ? 'من أصل' : 'of'} ${c.total_sessions} ${isAr ? 'دروس مكتملة' : 'modules finished'}</span>
                                <span>📅 ${isAr ? 'مشترك منذ' : 'Enrolled'}: ${c.enrolled_at || todayStr}</span>
                            </div>
                        </div>
                    </div>
                `;
            });
        } else {
            html += `<div class="col-span-2 p-6 bg-slate-50 rounded-2xl border border-slate-200 text-xs font-mono text-slate-500 text-center">${isAr ? 'لا توجد كورسات مسجلة لهذا الطالب حالياً.' : 'No active course enrollments found.'}</div>`;
        }

        html += `
                </div>
            </div>

            {{-- 2 Columns: Graded Homework History & Live Attendance Logs --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 pt-4">

                {{-- Column 1: Detailed Graded Assignments & Evaluations --}}
                <div id="section-assignments" class="space-y-4 bg-slate-50 p-6 rounded-3xl border border-slate-200/80 scroll-mt-28">
                    <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                        <h4 class="font-heading font-black text-base text-slate-900 flex items-center gap-2">
                            <span>📝</span> ${isAr ? 'سجل درجات الواجبات وتقييم المدرس' : 'Graded Assignment History & Feedback'}
                        </h4>
                        <span class="text-xs font-mono font-bold text-slate-500">${data.submissions.length} ${isAr ? 'واجب' : 'records'}</span>
                    </div>
                    <div class="space-y-3 max-h-[420px] overflow-y-auto pr-1">
        `;

        if (data.submissions.length > 0) {
            data.submissions.forEach(s => {
                const isPassed = s.is_passed;
                const badgeBg = isPassed ? 'bg-emerald-100 text-emerald-800 border-emerald-300' : 'bg-rose-100 text-rose-800 border-rose-300';
                html += `
                    <div class="p-4 bg-white rounded-2xl border border-slate-200 shadow-2xs space-y-2">
                        <div class="flex justify-between items-start gap-2">
                            <div>
                                <span class="font-bold text-xs text-slate-900 leading-snug block">${s.assignment_title}</span>
                                <span class="text-[11px] font-mono text-teal-700 font-semibold">${s.course_title} • 👨‍🏫 ${s.teacher_name}</span>
                            </div>
                            <span class="text-xs font-mono font-extrabold px-2.5 py-1 rounded-xl border ${badgeBg} whitespace-nowrap">${s.grade}</span>
                        </div>
                        <div class="p-2.5 bg-slate-50 rounded-xl text-[11px] font-mono text-slate-700 border border-slate-100 leading-relaxed">
                            💬 <strong class="text-slate-900">${isAr ? 'ملاحظة المعلم' : 'Feedback'}:</strong> ${s.teacher_notes}
                        </div>
                        <div class="flex justify-between items-center text-[10px] font-mono text-slate-400 pt-0.5">
                            <span>📅 ${isAr ? 'تاريخ التسليم' : 'Submitted'}: ${s.submitted_at}</span>
                            <span class="font-bold ${isPassed ? 'text-emerald-700' : 'text-rose-700'}">● ${isPassed ? (isAr ? 'اجتياز بنجاح' : 'Passed') : (isAr ? 'يحتاج تحسين' : 'Needs Work')}</span>
                        </div>
                    </div>
                `;
            });
        } else {
            html += `<div class="p-4 bg-white rounded-2xl border border-slate-200 text-xs text-slate-500">${isAr ? 'لا توجد واجبات مسجلة بعد.' : 'No homework submissions recorded.'}</div>`;
        }

        html += `
                    </div>
                </div>

                {{-- Column 2: Live Stream Attendance Logs & Schedule --}}
                <div id="section-attendance" class="space-y-4 bg-slate-50 p-6 rounded-3xl border border-slate-200/80 scroll-mt-28">
                    <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                        <h4 class="font-heading font-black text-base text-slate-900 flex items-center gap-2">
                            <span>🟢</span> ${isAr ? 'سجل حضور البث المباشر والحصص القادمة' : 'Live Stream Attendance Log & Upcoming'}
                        </h4>
                        <span class="text-xs font-mono font-bold text-slate-500">${data.attendance.logs ? data.attendance.logs.length : 0} ${isAr ? 'جلسات مسجلة' : 'sessions'}</span>
                    </div>
                    <div class="space-y-3 max-h-[420px] overflow-y-auto pr-1">
        `;

        if (data.upcoming_sessions && data.upcoming_sessions.length > 0) {
            data.upcoming_sessions.forEach(s => {
                html += `
                    <div class="p-3.5 bg-blue-50/80 rounded-2xl border border-blue-200 shadow-2xs space-y-1">
                        <div class="flex justify-between items-center gap-2">
                            <span class="font-bold text-xs text-blue-950 flex items-center gap-1"><span>🟢</span> ${s.title}</span>
                            <span class="text-[10px] font-mono font-extrabold bg-blue-200 text-blue-900 px-2 py-0.5 rounded-lg whitespace-nowrap">${s.scheduled_at}</span>
                        </div>
                        <p class="text-[11px] font-mono text-blue-800">${s.subject_name} • 👨‍🏫 ${s.teacher_name}</p>
                    </div>
                `;
            });
        }

        if (data.attendance.logs && data.attendance.logs.length > 0) {
            data.attendance.logs.forEach(att => {
                html += `
                    <div class="p-3.5 bg-white rounded-2xl border border-slate-200 shadow-2xs space-y-1">
                        <div class="flex justify-between items-center gap-2">
                            <span class="font-bold text-xs text-slate-900">${att.session_title}</span>
                            <span class="text-[10px] font-mono font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200 px-2 py-0.5 rounded-md">${isAr ? 'حضور مؤكد ✓' : 'Attended ✓'}</span>
                        </div>
                        <div class="flex justify-between items-center text-[10px] font-mono text-slate-500">
                            <span>👨‍🏫 ${att.teacher} (${att.subject})</span>
                            <span>⏱️ ${att.duration_minutes} ${isAr ? 'دقيقة' : 'mins'} • 📅 ${att.joined_at}</span>
                        </div>
                    </div>
                `;
            });
        } else if (!data.upcoming_sessions || data.upcoming_sessions.length === 0) {
            html += `<div class="p-4 bg-white rounded-2xl border border-slate-200 text-xs text-slate-500">${isAr ? 'لا توجد سجلات حضور حالياً.' : 'No attendance logs recorded.'}</div>`;
        }

        html += `
                    </div>
                </div>
            </div>

            {{-- Academic Notifications & Alerts Section --}}
            <div id="section-notifications" class="space-y-4 pt-6 border-t border-slate-100 scroll-mt-28">
                <h4 class="font-heading font-black text-base text-slate-900 flex items-center gap-2">
                    <span>🔔</span> ${isAr ? 'التنبيهات الأكاديمية الخاصة بالطالب' : 'Student Academic Notifications & Alerts'}
                </h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        `;

        data.notifications.forEach(n => {
            html += `
                <div class="p-4 bg-teal-50/70 rounded-2xl border border-teal-200/80 space-y-1">
                    <div class="flex justify-between items-center text-[11px] font-mono font-bold text-teal-800">
                        <span>${n.title}</span>
                        <span>${n.time}</span>
                    </div>
                    <p class="text-xs text-slate-800 font-semibold">${n.message}</p>
                </div>
            `;
        });

        html += `
                </div>
            </div>
        `;

        content.innerHTML = html;

        // Scroll to target hash if requested
        const hash = window.location.hash.replace('#', '');
        if (hash) {
            setTimeout(() => {
                const el = document.getElementById(hash);
                if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 200);
        }
    } catch (e) {
        content.innerHTML = '<div class="p-6 bg-red-50 text-red-700 rounded-2xl border border-red-200 text-xs font-bold">Network error while fetching student progress data.</div>';
    }
}

function switchParentSection(sectionId) {
    const cleanId = sectionId.replace('section-', '');
    let target = document.getElementById('section-' + cleanId) || document.getElementById(cleanId);

    if (target) {
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    document.querySelectorAll('.portal-nav-item').forEach(item => {
        item.classList.remove('active');
        if (item.getAttribute('data-section') === cleanId || item.getAttribute('href') === `#section-${cleanId}` || item.getAttribute('href') === `#${cleanId}`) {
            item.classList.add('active');
        }
    });

    // Only close drawer on mobile screen (< 1024px)
    if (window.innerWidth < 1024 && typeof togglePortalSidebar === 'function') {
        togglePortalSidebar(false);
    }
}
</script>
@endsection
