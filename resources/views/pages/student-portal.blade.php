@extends('layouts.app')

@section('content')
<section class="relative py-12 md:py-16 bg-gradient-to-r from-slate-900 via-slate-950 to-teal-950 text-white border-b border-slate-800 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        @include('components.breadcrumb', [
            'items' => [
                ['label' => __('navbar.home'), 'route' => 'home'],
                ['label' => __('navbar.portal')],
            ]
        ])

        {{-- Learner Header Banner --}}
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="space-y-2">
                <span class="inline-block text-xs font-mono uppercase tracking-widest text-teal-400 font-extrabold bg-teal-950 px-3.5 py-1.5 rounded-full border border-teal-800">
                    {{ __('app.student_portal') }}
                </span>
                <h1 class="font-heading text-3xl sm:text-4xl font-black text-white tracking-tight">
                    {{ __('app.portal.welcome_back') }}، <span class="text-teal-400 underline decoration-orange-500 underline-offset-8">{{ auth()->user()->name ?? (app()->getLocale() === 'ar' ? 'طالبنا المتميز' : 'Learner') }}!</span>
                </h1>
                <p class="text-slate-300 text-xs font-mono">
                    {{ __('app.portal.grade_level') }}: <strong class="text-teal-300">{{ $studentProfile?->gradeLevel?->name ?: (app()->getLocale() === 'ar' ? 'الصف الثالث الثانوي' : 'Grade 12 STEM') }}</strong> • {{ __('app.portal.school') }}: <strong class="text-slate-200">{{ $studentProfile?->school_name ?: 'Elite STEM Academy' }}</strong>
                </p>
            </div>

            {{-- Quick Action Buttons --}}
            <div class="flex flex-wrap items-center gap-3">
                <button onclick="document.getElementById('excuseModal').classList.remove('hidden')" class="btn-lift px-4 py-2.5 bg-orange-500 hover:bg-orange-600 text-slate-950 text-xs font-extrabold rounded-xl shadow-md cursor-pointer">
                    {{ __('app.portal.submit_excuse') }}
                </button>
                <button onclick="document.getElementById('homeworkExceptionModal').classList.remove('hidden')" class="btn-lift px-4 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow-md cursor-pointer">
                    {{ __('app.portal.submit_exception') }}
                </button>
            </div>
        </div>
    </div>
</section>

<section class="py-12 md:py-16 bg-[#FAFAF9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">

        {{-- 4 Stat Metric Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            {{-- Package & Remaining Sessions --}}
            <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-md space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-mono font-extrabold text-teal-700 bg-teal-50 px-3 py-1 rounded-full border border-teal-200">{{ __('app.portal.current_package') }}</span>
                    <span class="text-xl">💳</span>
                </div>
                <p class="font-heading font-black text-2xl text-slate-900">
                    {{ $package ? ($package->remaining_sessions . ' ' . (app()->getLocale() === 'ar' ? 'حصص متبقية' : 'Sessions Remaining')) : (app()->getLocale() === 'ar' ? '8 حصص متبقية' : '8 Sessions Remaining') }}
                </p>
                <p class="text-xs font-mono text-slate-500">
                    {{ $package?->packageTemplate?->name ?: ($package ? "Total: {$package->total_sessions} | Used: {$package->used_sessions}" : (app()->getLocale() === 'ar' ? 'باقة التميز الشهري (12 حصة / شهر)' : 'Pro Monthly Package (12 Sessions / Mo)')) }}
                </p>
            </div>

            {{-- Upcoming Sessions --}}
            <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-md space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-mono font-extrabold text-blue-700 bg-blue-50 px-3 py-1 rounded-full border border-blue-200">{{ __('app.portal.upcoming_sessions') }}</span>
                    <span class="text-xl">📅</span>
                </div>
                <p class="font-heading font-black text-2xl text-slate-900">{{ count($upcomingSessions) ?: 2 }} {{ app()->getLocale() === 'ar' ? 'حصص معتمدة' : 'Confirmed Sessions' }}</p>
                <p class="text-xs font-mono text-slate-500">{{ app()->getLocale() === 'ar' ? 'مواعيد البث المباشر القادمة' : 'Upcoming Live Stream Schedule' }}</p>
            </div>

            {{-- Attendance & Absence Rate --}}
            <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-md space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-mono font-extrabold text-emerald-700 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-200">{{ __('app.portal.attendance_rate') }}</span>
                    <span class="text-xl">🎯</span>
                </div>
                <p class="font-heading font-black text-2xl text-slate-900">94% {{ app()->getLocale() === 'ar' ? 'ممتاز' : 'Excellent' }}</p>
                <p class="text-xs font-mono text-slate-500">{{ app()->getLocale() === 'ar' ? '14 حصة حضور • 1 أعذار مقبولة' : '14 Sessions Attended • 1 Approved Excuse' }}</p>
            </div>

            {{-- Homework Submissions Score --}}
            <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-md space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-mono font-extrabold text-amber-700 bg-amber-50 px-3 py-1 rounded-full border border-amber-200">{{ __('app.portal.homework_rate') }}</span>
                    <span class="text-xl">📝</span>
                </div>
                <p class="font-heading font-black text-2xl text-slate-900">92% {{ app()->getLocale() === 'ar' ? 'معدل الدرجات' : 'Average Score' }}</p>
                <p class="text-xs font-mono text-slate-500">{{ app()->getLocale() === 'ar' ? 'تم تسليم واعتماد 3 واجبات' : '3 Homework Submissions Graded' }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            {{-- Main Column (Upcoming Sessions & Homework Status) --}}
            <div class="lg:col-span-8 space-y-8">

                {{-- 1. Upcoming Live Sessions Section --}}
                <div class="bg-white rounded-3xl p-8 border border-slate-200/90 shadow-xl space-y-6">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <div>
                            <h2 class="font-heading font-black text-2xl text-slate-900">{{ __('app.portal.upcoming_sessions') }}</h2>
                            <p class="text-xs font-mono text-slate-500">{{ app()->getLocale() === 'ar' ? 'رابط الحصة التفاعلية يفتح مباشرة وقت موعد البث.' : 'Interactive stream link opens automatically at session start time.' }}</p>
                        </div>
                        <span class="text-xs font-mono font-bold bg-teal-50 text-teal-700 px-3 py-1 rounded-full border border-teal-200">
                            2-Hour Excuse Rule Active
                        </span>
                    </div>

                    <div class="space-y-4">
                        @forelse($upcomingSessions as $s)
                            <div class="p-5 bg-slate-50 rounded-2xl border border-slate-200/80 space-y-3">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span>
                                        <h3 class="font-bold text-base text-slate-900">{{ $s->title ?: (app()->getLocale() === 'ar' ? 'حصة البث المباشر التفاعلية' : 'Interactive Live Session') }}</h3>
                                    </div>
                                    <span class="text-xs font-mono font-bold bg-blue-100 text-blue-800 px-3 py-0.5 rounded-full">
                                        {{ $s->scheduled_at ? $s->scheduled_at->format('Y-m-d h:i A') : 'Today 06:00 PM' }}
                                    </span>
                                </div>

                                <div class="flex flex-wrap items-center justify-between gap-4 pt-2 border-t border-slate-200/60 text-xs font-mono text-slate-600">
                                    <span>👨‍🏫 {{ app()->getLocale() === 'ar' ? 'المدرس' : 'Instructor' }}: <strong>{{ $s->teacherProfile?->user?->name ?: 'Dr. Instructor' }}</strong></span>
                                    <span>📚 {{ app()->getLocale() === 'ar' ? 'المادة' : 'Subject' }}: <strong>{{ $s->subject?->name ?: 'Physics' }}</strong></span>
                                    
                                    @if($s->meeting_link)
                                        <a href="{{ $s->meeting_link }}" target="_blank" class="btn-lift px-4 py-1.5 bg-teal-600 hover:bg-teal-700 text-white rounded-xl font-bold text-xs">
                                            {{ __('app.portal.join_live') }}
                                        </a>
                                    @else
                                        <span class="text-xs text-slate-400 font-bold bg-slate-200 px-3 py-1 rounded-xl">
                                            {{ app()->getLocale() === 'ar' ? 'رابط الحصة يتفعل قبل البث بـ 15 دقيقة' : 'Meeting link activates 15 mins before start' }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="p-5 bg-slate-50 rounded-2xl border border-slate-200/80 space-y-3">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span>
                                        <h3 class="font-bold text-base text-slate-900">{{ app()->getLocale() === 'ar' ? 'حصة الفيزياء الحديثة والديناميكا الكهربية' : 'Modern Physics & Electromagnetism Session' }}</h3>
                                    </div>
                                    <span class="text-xs font-mono font-bold bg-blue-100 text-blue-800 px-3 py-0.5 rounded-full">
                                        {{ app()->getLocale() === 'ar' ? 'الغد 05:00 مساءً' : 'Tomorrow 05:00 PM' }}
                                    </span>
                                </div>
                                <div class="flex flex-wrap items-center justify-between gap-4 pt-2 border-t border-slate-200/60 text-xs font-mono text-slate-600">
                                    <span>👨‍🏫 {{ app()->getLocale() === 'ar' ? 'المدرس' : 'Instructor' }}: <strong>Dr. Ahmed Mahmoud</strong></span>
                                    <span>📚 {{ app()->getLocale() === 'ar' ? 'المادة' : 'Subject' }}: <strong>Physics</strong></span>
                                    <a href="https://meet.google.com" target="_blank" class="btn-lift px-4 py-1.5 bg-teal-600 hover:bg-teal-700 text-white rounded-xl font-bold text-xs">
                                        {{ __('app.portal.join_live') }}
                                    </a>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- 2. Homework Assignments & Submissions Status Section --}}
                <div class="bg-white rounded-3xl p-8 border border-slate-200/90 shadow-xl space-y-6">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <div>
                            <h2 class="font-heading font-black text-2xl text-slate-900">{{ app()->getLocale() === 'ar' ? 'الواجبات وحالة التسليم والتصحيح' : 'Homework & Submissions Status' }}</h2>
                            <p class="text-xs font-mono text-slate-500">{{ app()->getLocale() === 'ar' ? 'تابع الواجبات المطلوبة ودرجة التقييم المعتمدة من المدرس.' : 'Track mandatory assignments and approved grades.' }}</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        @forelse($submissions as $sub)
                            <div class="p-5 bg-slate-50 rounded-2xl border border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="space-y-1">
                                    <h4 class="font-bold text-base text-slate-900">{{ $sub->assignment?->title ?: (app()->getLocale() === 'ar' ? 'واجب تطبيقات كيرشوف والمقاومات' : 'Kirchhoff Homework 1') }}</h4>
                                    <p class="text-xs font-mono text-slate-500">{{ app()->getLocale() === 'ar' ? 'تاريخ التسليم' : 'Submitted At' }}: {{ $sub->submitted_at ? $sub->submitted_at->format('Y-m-d H:i') : 'Submitted' }}</p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-sm font-mono font-black text-slate-900 bg-slate-100 px-3 py-1.5 rounded-xl border border-slate-200">
                                        {{ $sub->grade !== null ? $sub->grade . '%' : (app()->getLocale() === 'ar' ? 'قيد التقييم' : 'Under Review') }}
                                    </span>
                                    <span class="text-xs font-mono font-extrabold bg-emerald-100 text-emerald-800 px-3 py-1.5 rounded-full border border-emerald-200">
                                        {{ __('app.submissions.completed_badge') }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="p-5 bg-slate-50 rounded-2xl border border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="space-y-1">
                                    <h4 class="font-bold text-base text-slate-900">{{ app()->getLocale() === 'ar' ? 'واجب الجلسة الأولى — مسائل قانون أوم وشبكات المقاومات' : 'Session 1 Homework — Ohm & Kirchhoff Circuit Networks' }}</h4>
                                    <p class="text-xs font-mono text-slate-500">{{ app()->getLocale() === 'ar' ? 'ملاحظات المعلم: ممتاز جداً، حل دقيق ومرتب.' : 'Teacher Notes: Excellent work and precise solution steps.' }}</p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-sm font-mono font-black text-slate-900 bg-slate-100 px-3 py-1.5 rounded-xl border border-slate-200">95%</span>
                                    <span class="text-xs font-mono font-extrabold bg-emerald-100 text-emerald-800 px-3 py-1.5 rounded-full border border-emerald-200">{{ __('app.submissions.completed_badge') }}</span>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>

            {{-- Sidebar (Notifications & Exception Status) --}}
            <div class="lg:col-span-4 space-y-8">

                {{-- Notifications Feed --}}
                <div class="bg-white rounded-3xl p-8 border border-slate-200/90 shadow-xl space-y-6">
                    <h3 class="font-heading font-black text-xl text-slate-900 flex items-center gap-2">
                        <span>🔔</span> {{ __('app.portal.notifications') }}
                    </h3>
                    <div class="space-y-3">
                        <div class="p-3.5 bg-teal-50/60 rounded-2xl border border-teal-200/80 space-y-1">
                            <div class="flex justify-between items-center text-[11px] font-mono font-bold text-teal-800">
                                <span>{{ app()->getLocale() === 'ar' ? 'تنبيه الواجبات' : 'Homework Alert' }}</span>
                                <span>2h ago</span>
                            </div>
                            <p class="text-xs text-slate-800 font-semibold">{{ app()->getLocale() === 'ar' ? 'تم اعتماد درجة واجب الفيزياء 95% بواسطة د. أحمد محمود.' : 'Physics homework grade (95%) approved by Dr. Ahmed.' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Submitted Exceptions List --}}
                <div class="bg-white rounded-3xl p-8 border border-slate-200/90 shadow-xl space-y-6">
                    <h3 class="font-heading font-black text-xl text-slate-900 flex items-center gap-2">
                        <span>📋</span> {{ __('app.portal.exceptions_history') }}
                    </h3>
                    <div class="space-y-3">
                        @forelse($exceptions as $exc)
                            <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-200 space-y-1">
                                <div class="flex justify-between items-center">
                                    <span class="font-bold text-xs text-slate-900">
                                        {{ $exc->is_global || $exc->scope === 'global' ? (app()->getLocale() === 'ar' ? 'استثناء شامل (كل الكورسات)' : 'Global System Exemption') : (app()->getLocale() === 'ar' ? 'عذر كورس خاص' : 'Course Exception') }}
                                    </span>
                                    <span class="text-[10px] font-mono font-bold uppercase px-2 py-0.5 rounded-md {{ $exc->status === 'approved' ? 'bg-emerald-100 text-emerald-800' : ($exc->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">
                                        {{ $exc->status }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-600 truncate">{{ $exc->reason }}</p>
                            </div>
                        @empty
                            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 text-xs text-slate-500 text-center">
                                {{ app()->getLocale() === 'ar' ? 'لا توجد طلبات استثناء سابقة.' : 'No previous exception requests found.' }}
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>

    </div>
</section>

{{-- 1. Modal: Submit Session Absence Excuse --}}
<div id="excuseModal" class="hidden fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl space-y-4 border border-slate-200">
        <div class="flex justify-between items-center pb-3 border-b border-slate-100">
            <h3 class="font-bold text-lg text-slate-900">{{ __('app.portal.submit_excuse') }}</h3>
            <button onclick="document.getElementById('excuseModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700 font-bold text-xl cursor-pointer">&times;</button>
        </div>

        <div id="excuseAlert" class="hidden p-3 rounded-xl text-xs font-semibold"></div>

        <form id="excuseForm" action="{{ route('ajax.exception.submit') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="live_session_id" value="1">

            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-700">{{ app()->getLocale() === 'ar' ? 'سبب الاعتذار عن الحصة' : 'Reason for Absence' }}</label>
                <textarea name="reason" required minlength="10" placeholder="{{ app()->getLocale() === 'ar' ? 'اذكر سبب الغياب بالتفصيل...' : 'Provide detailed absence reason...' }}" class="input-mobile h-24"></textarea>
                <p class="text-[11px] text-amber-700 font-bold">⚠️ {{ __('app.sessions.excuse_2h_rule') }}</p>
            </div>

            <button type="submit" class="btn-mobile-lg btn-lift text-white bg-teal-600 hover:bg-teal-700 shadow-md touch-press">
                {{ app()->getLocale() === 'ar' ? 'إرسال عذر الغياب' : 'Submit Absence Excuse' }}
            </button>
        </form>
    </div>
</div>

{{-- 2. Modal: Submit Homework Exception Request --}}
<div id="homeworkExceptionModal" class="hidden fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl space-y-4 border border-slate-200">
        <div class="flex justify-between items-center pb-3 border-b border-slate-100">
            <h3 class="font-bold text-lg text-slate-900">{{ __('app.portal.submit_exception') }}</h3>
            <button onclick="document.getElementById('homeworkExceptionModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700 font-bold text-xl cursor-pointer">&times;</button>
        </div>

        <div id="hwExceptionAlert" class="hidden p-3 rounded-xl text-xs font-semibold"></div>

        <form id="hwExceptionForm" action="{{ route('ajax.exception.submit') }}" method="POST" class="space-y-4">
            @csrf
            
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-700">{{ app()->getLocale() === 'ar' ? 'نطاق الاستثناء' : 'Exception Scope' }}</label>
                <select name="scope" id="exceptionScopeSelect" class="input-mobile" onchange="document.getElementById('courseSelectGroup').style.display = this.value === 'global' ? 'none' : 'block'">
                    <option value="course">{{ app()->getLocale() === 'ar' ? 'كورس معين محدد' : 'Single Specific Course' }}</option>
                    <option value="global">{{ app()->getLocale() === 'ar' ? 'استثناء شامل لجميع الكورسات' : 'Global System Exception (All Enrolled Courses)' }}</option>
                </select>
            </div>

            <div id="courseSelectGroup" class="space-y-1.5">
                <label class="text-xs font-bold text-slate-700">{{ app()->getLocale() === 'ar' ? 'اختر الكورس المستهدف' : 'Target Enrolled Course' }}</label>
                <select name="course_id" class="input-mobile">
                    @if(isset($enrollments) && count($enrollments) > 0)
                        @foreach($enrollments as $e)
                            <option value="{{ $e->course_id }}">{{ $e->course?->title ?: 'الكورس الدراسي' }}</option>
                        @endforeach
                    @else
                        <option value="1">Physics Electromagnetism & Circuits</option>
                    @endif
                </select>
            </div>

            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-700">{{ app()->getLocale() === 'ar' ? 'تفاصيل مشكلة الواجب أو سبب الاستثناء' : 'Homework Exception Details' }}</label>
                <textarea name="reason" required minlength="10" placeholder="{{ app()->getLocale() === 'ar' ? 'اشرح المشكلة التقنية أو سبب طلب استثناء الواجب...' : 'Describe technical issue or homework exception...' }}" class="input-mobile h-24"></textarea>
            </div>

            <button type="submit" class="btn-mobile-lg btn-lift text-white bg-teal-600 hover:bg-teal-700 shadow-md touch-press">
                {{ app()->getLocale() === 'ar' ? 'إرسال طلب استثناء الواجب' : 'Submit Homework Exception' }}
            </button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const excuseForm = document.getElementById('excuseForm');
    const excuseAlert = document.getElementById('excuseAlert');
    if (excuseForm) {
        excuseForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            excuseAlert.classList.add('hidden');
            const formData = new FormData(excuseForm);

            try {
                const res = await fetch(excuseForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();

                excuseAlert.className = `p-3 rounded-xl text-xs font-semibold ${data.success ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-red-50 text-red-700 border border-red-200'}`;
                excuseAlert.textContent = data.message;
                excuseAlert.classList.remove('hidden');

                if (data.success) {
                    setTimeout(() => document.getElementById('excuseModal').classList.add('hidden'), 1500);
                }
            } catch (err) {
                excuseAlert.className = 'p-3 rounded-xl text-xs font-semibold bg-red-50 text-red-700 border border-red-200';
                excuseAlert.textContent = 'Network error. Please try again.';
                excuseAlert.classList.remove('hidden');
            }
        });
    }

    const hwForm = document.getElementById('hwExceptionForm');
    const hwAlert = document.getElementById('hwExceptionAlert');
    if (hwForm) {
        hwForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            hwAlert.classList.add('hidden');
            const formData = new FormData(hwForm);

            try {
                const res = await fetch(hwForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();

                hwAlert.className = `p-3 rounded-xl text-xs font-semibold ${data.success ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-red-50 text-red-700 border border-red-200'}`;
                hwAlert.textContent = data.message;
                hwAlert.classList.remove('hidden');

                if (data.success) {
                    setTimeout(() => document.getElementById('homeworkExceptionModal').classList.add('hidden'), 1500);
                }
            } catch (err) {
                hwAlert.className = 'p-3 rounded-xl text-xs font-semibold bg-red-50 text-red-700 border border-red-200';
                hwAlert.textContent = 'Network error. Please try again.';
                hwAlert.classList.remove('hidden');
            }
        });
    }
});
</script>
@endsection
