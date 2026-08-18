@extends('layouts.app')

@section('content')
<section class="py-12 md:py-16 bg-gradient-to-r from-slate-900 via-slate-950 to-teal-950 text-white border-b border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
        @include('components.breadcrumb', [
            'items' => [
                ['label' => __('navbar.home'), 'route' => 'home'],
                ['label' => __('navbar.parent_portal')],
            ]
        ])

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="space-y-2">
                <span class="inline-block text-xs font-mono uppercase tracking-widest text-teal-400 font-extrabold bg-teal-950 px-3.5 py-1.5 rounded-full border border-teal-800">
                    PARENT DASHBOARD • MULTI-CHILD MONITORING
                </span>
                <h1 class="font-heading text-3xl sm:text-4xl font-black text-white tracking-tight">
                    {{ app()->getLocale() === 'ar' ? 'حساب ولي الأمر: متابعة أبنائك الطلاب' : 'Parent Portal: Student Academic Dashboard' }}
                </h1>
                <p class="text-slate-300 text-sm font-mono">
                    {{ app()->getLocale() === 'ar' ? 'اختر أي ابن لمتابعة الحصص القادمة، الحضور والغياب، الواجبات، الباقة ورصيد الحصص المتبقي.' : "Monitor your linked children's upcoming sessions, attendance, homework, and remaining package credits." }}
                </p>
            </div>
            <div class="inline-flex items-center gap-2 bg-amber-500/20 px-4 py-2 rounded-2xl border border-amber-500/30 text-xs font-mono text-amber-300 font-bold self-start md:self-auto">
                <span>🔒</span>
                <span>{{ app()->getLocale() === 'ar' ? 'وضع المتابعة للقراءة فقط (غير مسموح للتعديل)' : 'Strict Read-Only Parent Monitoring Mode' }}</span>
            </div>
        </div>
    </div>
</section>

<section class="py-12 md:py-16 bg-[#FAFAF9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        {{-- Children Selector Grid --}}
        <div class="bg-white rounded-3xl p-8 border border-slate-200/90 shadow-xl space-y-6">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <h2 class="font-heading font-black text-2xl text-slate-900">{{ app()->getLocale() === 'ar' ? 'الأبناء المربوطين بالحساب' : 'Your Linked Children' }}</h2>
                    <p class="text-xs font-mono text-slate-500">{{ app()->getLocale() === 'ar' ? 'اضغط على بطاقة أي ابن لمتابعة أدائه ورصيده والتنبيهات بشكل مفصل.' : 'Select a child to inspect detailed academic performance, package & attendance.' }}</p>
                </div>
                <span class="text-xs font-mono font-extrabold text-teal-700 bg-teal-50 px-3 py-1 rounded-full border border-teal-200">
                    {{ count($linkedStudents) }} {{ app()->getLocale() === 'ar' ? 'أبناء مربوطين' : 'Children Linked' }}
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @forelse($linkedStudents as $st)
                    <div id="child-card-{{ $st->user_id }}" onclick="loadStudentProgress({{ $st->user_id }})" class="child-card cursor-pointer bg-slate-50 hover:bg-teal-50/60 transition-all duration-300 rounded-2xl p-6 border-2 border-slate-200 hover:border-teal-500 shadow-sm space-y-4 group">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-teal-600 text-white font-heading font-black text-xl flex items-center justify-center shadow-md">
                                {{ mb_substr($st->user?->name ?: 'S', 0, 1) }}
                            </div>
                            <div>
                                <h3 class="font-bold text-base text-slate-900 group-hover:text-teal-700 transition-colors">{{ $st->user?->name }}</h3>
                                <span class="text-[11px] font-mono font-bold bg-teal-100 text-teal-800 px-2.5 py-0.5 rounded-md">
                                    {{ $st->gradeLevel?->name ?: 'Grade Level' }}
                                </span>
                            </div>
                        </div>

                        <div class="text-xs font-mono text-slate-500 space-y-1 pt-2 border-t border-slate-200/60">
                            <p class="truncate">🏫 {{ $st->school_name ?: 'Elite STEM Academy' }}</p>
                            <p class="text-teal-600 font-bold">Independent Student Account ✔</p>
                        </div>

                        <button type="button" class="w-full py-2 bg-white group-hover:bg-teal-600 group-hover:text-white text-slate-800 rounded-xl text-xs font-bold font-mono border border-slate-200 transition-all shadow-xs">
                            {{ app()->getLocale() === 'ar' ? 'عرض تقرير الطالب الشامل ←' : 'Inspect Child Dashboard &rarr;' }}
                        </button>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-12 bg-slate-50 rounded-2xl border border-slate-200 text-slate-500 space-y-3">
                        <div class="text-4xl">👨‍👩‍👧‍👦</div>
                        <h3 class="font-bold text-base text-slate-800">No Linked Children Found</h3>
                        <p class="text-xs font-mono text-slate-500 max-w-md mx-auto">
                            If your child has created an account, ask the admin to link your parent account via Filament Admin panel.
                        </p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Selected Child Detailed Performance Panel --}}
        <div id="progressResult" class="hidden bg-white rounded-3xl p-8 border border-slate-200/90 shadow-xl space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
                <div>
                    <h3 id="selectedStudentName" class="font-heading font-black text-2xl text-slate-900">Student Academic Overview</h3>
                    <p id="selectedStudentMeta" class="text-xs font-mono text-teal-600 font-bold mt-0.5"></p>
                </div>
                <div class="flex items-center gap-3">
                    <span id="packageBadge" class="text-xs font-mono font-bold bg-teal-100 text-teal-800 px-3 py-1.5 rounded-xl border border-teal-200"></span>
                    <span id="attendanceBadge" class="text-xs font-mono font-bold bg-emerald-100 text-emerald-800 px-3 py-1.5 rounded-xl border border-emerald-200"></span>
                </div>
            </div>

            <div id="progressContent" class="space-y-6"></div>
        </div>

    </div>
</section>

<script>
async function loadStudentProgress(studentId) {
    const resBox = document.getElementById('progressResult');
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

    resBox.classList.remove('hidden');
    resBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    content.innerHTML = '<div class="p-8 text-center text-xs font-mono text-slate-500 font-bold">Loading student progress metrics...</div>';

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

        nameEl.textContent = `تقرير الأداء الأكاديمي للطالب: ${data.student.name}`;
        metaEl.textContent = `${data.student.grade} • المدرسه: ${data.student.school}`;
        pkgBadge.textContent = `💳 ${data.package.name} (${data.package.remaining_sessions} حصص متبقية)`;
        attBadge.textContent = `🎯 الحضور والغياب: ${data.attendance.rate}`;

        let html = `
            <div class="p-4 bg-amber-50 rounded-2xl border border-amber-200 text-xs font-semibold text-amber-900 flex items-center justify-between">
                <span>🔒 هذا التقرير مخصص للمتابعة والقراءة فقط. لا تملك صلاحية تعديل الحضور أو الباقة أو تأكيد الدفع.</span>
                <span class="font-mono text-[11px] bg-amber-200 text-amber-900 px-2.5 py-0.5 rounded-md font-bold">Read-Only</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="p-5 bg-teal-50 rounded-2xl border border-teal-200/80 space-y-1">
                    <span class="text-xs font-mono font-bold text-teal-800">💳 الباقة الحالية والرصيد</span>
                    <p class="font-black text-xl text-teal-950">${data.package.remaining_sessions} حصة متبقية</p>
                    <p class="text-xs text-teal-700 font-mono">${data.package.name}</p>
                </div>
                <div class="p-5 bg-emerald-50 rounded-2xl border border-emerald-200/80 space-y-1">
                    <span class="text-xs font-mono font-bold text-emerald-800">🎯 الحضور والغياب</span>
                    <p class="font-black text-xl text-emerald-950">${data.attendance.rate} نسبة الحضور</p>
                    <p class="text-xs text-emerald-700 font-mono">${data.attendance.attended_count} حصة حضور • ${data.attendance.absences_count} غياب بعذر</p>
                </div>
                <div class="p-5 bg-blue-50 rounded-2xl border border-blue-200/80 space-y-1">
                    <span class="text-xs font-mono font-bold text-blue-800">📅 الحصص القادمة</span>
                    <p class="font-black text-xl text-blue-950">${data.upcoming_sessions.length} حصص معتمدة</p>
                    <p class="text-xs text-blue-700 font-mono">مواعيد البث المباشر القادمة</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                {{-- Upcoming Sessions Column --}}
                <div class="space-y-4">
                    <h4 class="font-bold text-sm text-slate-900 uppercase tracking-wider font-mono flex items-center gap-2">
                        <span>📅</span> الحصص القادمة ومواعيد البث
                    </h4>
                    <div class="space-y-3">
        `;

        if (data.upcoming_sessions.length > 0) {
            data.upcoming_sessions.forEach(s => {
                html += `
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200/80 space-y-1">
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-sm text-slate-900">${s.title}</span>
                            <span class="text-[11px] font-mono font-bold bg-blue-100 text-blue-800 px-2.5 py-0.5 rounded-md">${s.scheduled_at}</span>
                        </div>
                        <p class="text-xs font-mono text-slate-500">المدرس: ${s.teacher_name} • المادة: ${s.subject_name}</p>
                    </div>
                `;
            });
        } else {
            html += `<div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 text-xs text-slate-500">لا توجد حصص قادمة حالياً.</div>`;
        }

        html += `
                </div>
            </div>

            {{-- Homework Submissions Column --}}
            <div class="space-y-4">
                <h4 class="font-bold text-sm text-slate-900 uppercase tracking-wider font-mono flex items-center gap-2">
                    <span>📝</span> الواجبات وحالة التسليم
                </h4>
                <div class="space-y-3">
        `;

        if (data.submissions.length > 0) {
            data.submissions.forEach(s => {
                html += `
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200/80 space-y-1">
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-sm text-slate-900">${s.assignment_title}</span>
                            <span class="text-[11px] font-mono font-bold bg-emerald-100 text-emerald-800 px-2.5 py-0.5 rounded-md">${s.grade}</span>
                        </div>
                        <p class="text-xs font-mono text-slate-500">تاريخ التسليم: ${s.submitted_at}</p>
                    </div>
                `;
            });
        } else {
            html += `<div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 text-xs text-slate-500">لا توجد تسليمات واجبات مسجلة.</div>`;
        }

        html += `
                </div>
            </div>
        </div>

        {{-- Notifications Section --}}
        <div class="space-y-4 pt-4 border-t border-slate-100">
            <h4 class="font-bold text-sm text-slate-900 uppercase tracking-wider font-mono flex items-center gap-2">
                <span>🔔</span> الإشعارات الخاصة بالابن
            </h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        `;

        data.notifications.forEach(n => {
            html += `
                <div class="p-3.5 bg-teal-50/60 rounded-2xl border border-teal-200/80 space-y-1">
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
    } catch (e) {
        content.innerHTML = '<div class="p-6 bg-red-50 text-red-700 rounded-2xl border border-red-200 text-xs font-bold">Network error while fetching student progress data.</div>';
    }
}
</script>
@endsection
