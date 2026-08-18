@extends('layouts.app')

@section('content')
{{-- Ultra-Premium Dark Profile Hero Banner --}}
<section class="relative py-12 md:py-16 bg-gradient-to-r from-slate-900 via-slate-950 to-teal-950 text-white border-b border-slate-800/80 overflow-hidden shadow-2xl">
    <div class="absolute -right-20 -top-20 w-96 h-96 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute left-10 -bottom-20 w-80 h-80 bg-orange-500/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6 relative z-10">
        @include('components.breadcrumb', [
            'items' => [
                ['label' => __('navbar.home'), 'route' => 'home'],
                ['label' => __('app.student_portal'), 'route' => 'student-portal'],
                ['label' => app()->getLocale() === 'ar' ? 'الملف الشخصي' : 'Student Profile'],
            ]
        ])

        {{-- Profile Header Card --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-6">
                <div class="relative group">
                    @if($profile->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($profile->avatar))
                        <img src="{{ asset('storage/' . $profile->avatar) }}" alt="{{ $user->name }}" class="w-20 h-20 sm:w-24 sm:h-24 rounded-3xl object-cover border-4 border-teal-500/40 shadow-xl shadow-teal-500/20">
                    @else
                        <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-3xl bg-gradient-to-tr from-teal-500 to-emerald-400 text-slate-950 font-heading font-black text-3xl sm:text-4xl flex items-center justify-center shadow-xl shadow-teal-500/20 border-4 border-teal-300/40">
                            {{ mb_substr($user->name ?? 'S', 0, 1) }}
                        </div>
                    @endif
                    <button onclick="document.getElementById('avatarInput').click()" class="absolute -bottom-2 -right-2 w-8 h-8 rounded-full bg-teal-500 hover:bg-teal-400 text-slate-950 flex items-center justify-center text-xs font-bold shadow-md cursor-pointer transition-transform hover:scale-110" title="{{ app()->getLocale() === 'ar' ? 'تغيير الصورة الشخصية' : 'Change Avatar' }}">
                        📷
                    </button>
                </div>

                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="inline-block text-[11px] font-mono uppercase tracking-widest text-teal-400 font-extrabold bg-teal-950/80 px-3 py-0.5 rounded-full border border-teal-700/60">
                            {{ app()->getLocale() === 'ar' ? 'حساب طالب معتمد' : 'Verified Student Account' }}
                        </span>
                        <span class="text-xs font-mono text-emerald-400 font-bold bg-emerald-950/60 px-2.5 py-0.5 rounded-full border border-emerald-800/60">
                            ● Active Status
                        </span>
                    </div>
                    <h1 class="font-heading text-2xl sm:text-4xl font-black text-white tracking-tight">
                        {{ $user->name }}
                    </h1>
                    <p class="text-slate-300 text-xs sm:text-sm font-mono flex flex-wrap items-center gap-3 pt-0.5">
                        <span>✉️ {{ $user->email }}</span>
                        <span>•</span>
                        <span>📱 {{ $user->phone ?: (app()->getLocale() === 'ar' ? 'غير مسجل' : 'Not Provided') }}</span>
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3 self-start md:self-auto">
                <a href="{{ route('student-portal') }}" class="btn-lift px-5 py-3 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold rounded-2xl border border-slate-700 shadow-md flex items-center gap-2 transition-all">
                    <span>&larr;</span> {{ app()->getLocale() === 'ar' ? 'العودة للوحة التحكم' : 'Back to Dashboard' }}
                </a>
            </div>
        </div>
    </div>
</section>

{{-- Main Profile Content Section --}}
<section class="py-12 md:py-16 bg-[#FAFAF9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10 md:space-y-12">

        {{-- Flash Alerts --}}
        @if(session('success'))
            <div class="animate-fade-in-up p-5 bg-emerald-50 border border-emerald-200 rounded-3xl flex items-center justify-between text-emerald-950 shadow-sm">
                <div class="flex items-center gap-3">
                    <span class="text-xl">✅</span>
                    <span class="font-bold text-xs sm:text-sm font-mono">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="animate-fade-in-up p-5 bg-rose-50 border border-rose-200 rounded-3xl space-y-2 text-rose-950 shadow-sm">
                <div class="flex items-center gap-2 font-bold text-xs sm:text-sm">
                    <span>⚠️</span>
                    <span>{{ app()->getLocale() === 'ar' ? 'يرجى تصحيح الأخطاء التالية:' : 'Please correct the following errors:' }}</span>
                </div>
                <ul class="list-disc list-inside text-xs font-mono text-rose-800 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10">

            {{-- Main Profile Settings Form Column --}}
            <div class="lg:col-span-8 space-y-8 lg:space-y-10">

                {{-- 1. Personal & Academic Profile Form Card --}}
                <div class="glass-card rounded-3xl p-6 sm:p-8 md:p-9 border border-slate-200/80 shadow-sm hover:shadow-lg transition-all space-y-6 animate-fade-in-up stagger-1">
                    <div class="border-b border-slate-100 pb-4">
                        <h2 class="font-heading font-black text-xl sm:text-2xl text-slate-900 flex items-center gap-2">
                            <span>👤</span> {{ app()->getLocale() === 'ar' ? 'البيانات الشخصية والأكاديمية' : 'Personal & Academic Details' }}
                        </h2>
                        <p class="text-xs font-mono text-slate-500 mt-1">
                            {{ app()->getLocale() === 'ar' ? 'قم بتحديث اسمك، رقم الهاتف، المرحلة الدراسية، واسم المدرسة.' : 'Update your name, phone number, grade level, and school information.' }}
                        </p>
                    </div>

                    <form action="{{ route('student.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        <input type="file" id="avatarInput" name="avatar" accept="image/*" class="hidden" onchange="previewAvatar(this)">

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            {{-- Full Name --}}
                            <div class="space-y-2">
                                <label class="block text-xs font-mono font-bold text-slate-700 uppercase tracking-wider">
                                    {{ app()->getLocale() === 'ar' ? 'الاسم بالكامل' : 'Full Name' }} <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-mono text-slate-900 focus:outline-none focus:border-teal-500 focus:bg-white transition-all">
                            </div>

                            {{-- Phone Number --}}
                            <div class="space-y-2">
                                <label class="block text-xs font-mono font-bold text-slate-700 uppercase tracking-wider">
                                    {{ app()->getLocale() === 'ar' ? 'رقم الهاتف / الواتساب' : 'Phone Number' }}
                                </label>
                                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="+20 100 000 0000" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-mono text-slate-900 focus:outline-none focus:border-teal-500 focus:bg-white transition-all">
                            </div>

                            {{-- Email Address (Readonly) --}}
                            <div class="space-y-2">
                                <label class="block text-xs font-mono font-bold text-slate-700 uppercase tracking-wider">
                                    {{ app()->getLocale() === 'ar' ? 'البريد الإلكتروني (غير قابل للتعديل)' : 'Email Address (Readonly)' }}
                                </label>
                                <input type="email" value="{{ $user->email }}" disabled class="w-full px-4 py-3 bg-slate-100 border border-slate-200 rounded-2xl text-xs font-mono text-slate-500 cursor-not-allowed">
                            </div>

                            {{-- Grade Level Select --}}
                            <div class="space-y-2">
                                <label class="block text-xs font-mono font-bold text-slate-700 uppercase tracking-wider">
                                    {{ app()->getLocale() === 'ar' ? 'الصف / المرحلة الدراسية' : 'Grade Level' }}
                                </label>
                                <select name="grade_level_id" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-mono text-slate-900 focus:outline-none focus:border-teal-500 focus:bg-white transition-all">
                                    <option value="">{{ app()->getLocale() === 'ar' ? '-- اختر المرحلة الدراسية --' : '-- Select Grade Level --' }}</option>
                                    @foreach($gradeLevels as $gl)
                                        <option value="{{ $gl->id }}" {{ old('grade_level_id', $profile->grade_level_id) == $gl->id ? 'selected' : '' }}>
                                            {{ $gl->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- School Name --}}
                            <div class="space-y-2">
                                <label class="block text-xs font-mono font-bold text-slate-700 uppercase tracking-wider">
                                    {{ app()->getLocale() === 'ar' ? 'اسم المدرسة / الأكاديمية' : 'School Name' }}
                                </label>
                                <input type="text" name="school_name" value="{{ old('school_name', $profile->school_name) }}" placeholder="e.g. STEM Cairo High School" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-mono text-slate-900 focus:outline-none focus:border-teal-500 focus:bg-white transition-all">
                            </div>

                            {{-- Date of Birth --}}
                            <div class="space-y-2">
                                <label class="block text-xs font-mono font-bold text-slate-700 uppercase tracking-wider">
                                    {{ app()->getLocale() === 'ar' ? 'تاريخ الميلاد' : 'Date of Birth' }}
                                </label>
                                <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $profile->date_of_birth ? $profile->date_of_birth->format('Y-m-d') : '') }}" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-mono text-slate-900 focus:outline-none focus:border-teal-500 focus:bg-white transition-all">
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                            <button type="submit" class="btn-lift px-6 py-3 bg-teal-600 hover:bg-teal-700 text-white text-xs font-extrabold rounded-2xl shadow-md shadow-teal-600/30 flex items-center gap-2 cursor-pointer">
                                <span>💾</span> {{ app()->getLocale() === 'ar' ? 'حفظ التغييرات' : 'Save Profile Details' }}
                            </button>
                        </div>
                    </form>
                </div>

                {{-- 2. Account Security & Password Change Card --}}
                <div class="glass-card rounded-3xl p-6 sm:p-8 md:p-9 border border-slate-200/80 shadow-sm hover:shadow-lg transition-all space-y-6 animate-fade-in-up stagger-2">
                    <div class="border-b border-slate-100 pb-4">
                        <h2 class="font-heading font-black text-xl sm:text-2xl text-slate-900 flex items-center gap-2">
                            <span>🔒</span> {{ app()->getLocale() === 'ar' ? 'أمان الحساب وكلمة المرور' : 'Account Security & Password' }}
                        </h2>
                        <p class="text-xs font-mono text-slate-500 mt-1">
                            {{ app()->getLocale() === 'ar' ? 'قم بتغيير كلمة المرور الخاصة بك بحساب الطالب بانتظام لحماية بياناتك.' : 'Update your password regularly to maintain account security.' }}
                        </p>
                    </div>

                    <form action="{{ route('student.profile.password') }}" method="POST" class="space-y-6">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                            {{-- Current Password --}}
                            <div class="space-y-2">
                                <label class="block text-xs font-mono font-bold text-slate-700 uppercase tracking-wider">
                                    {{ app()->getLocale() === 'ar' ? 'كلمة المرور الحالية' : 'Current Password' }} <span class="text-rose-500">*</span>
                                </label>
                                <input type="password" name="current_password" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-mono text-slate-900 focus:outline-none focus:border-teal-500 focus:bg-white transition-all">
                            </div>

                            {{-- New Password --}}
                            <div class="space-y-2">
                                <label class="block text-xs font-mono font-bold text-slate-700 uppercase tracking-wider">
                                    {{ app()->getLocale() === 'ar' ? 'كلمة المرور الجديدة' : 'New Password' }} <span class="text-rose-500">*</span>
                                </label>
                                <input type="password" name="password" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-mono text-slate-900 focus:outline-none focus:border-teal-500 focus:bg-white transition-all">
                            </div>

                            {{-- Password Confirmation --}}
                            <div class="space-y-2">
                                <label class="block text-xs font-mono font-bold text-slate-700 uppercase tracking-wider">
                                    {{ app()->getLocale() === 'ar' ? 'تأكيد كلمة المرور' : 'Confirm Password' }} <span class="text-rose-500">*</span>
                                </label>
                                <input type="password" name="password_confirmation" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-mono text-slate-900 focus:outline-none focus:border-teal-500 focus:bg-white transition-all">
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                            <button type="submit" class="btn-lift px-6 py-3 bg-slate-900 hover:bg-slate-800 text-white text-xs font-extrabold rounded-2xl shadow-md cursor-pointer flex items-center gap-2">
                                <span>🔑</span> {{ app()->getLocale() === 'ar' ? 'تحديث كلمة المرور' : 'Update Password' }}
                            </button>
                        </div>
                    </form>
                </div>

            </div>

            {{-- Sidebar Column: Active Package & Parent Link Status --}}
            <div class="lg:col-span-4 space-y-8 lg:space-y-10">

                {{-- Active Package Subscription Summary Card --}}
                <div class="glass-card rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm hover:shadow-lg transition-all space-y-6 animate-fade-in-up stagger-1">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <h3 class="font-heading font-black text-xl text-slate-900 flex items-center gap-2">
                            <span>💳</span> {{ app()->getLocale() === 'ar' ? 'باقة الحصص النشطة' : 'Active Package' }}
                        </h3>
                        @if($activePackage)
                            <span class="text-xs font-mono font-bold text-teal-800 bg-teal-50 px-3 py-1 rounded-full border border-teal-200 shadow-2xs">
                                ● Active
                            </span>
                        @else
                            <span class="text-xs font-mono font-bold text-rose-800 bg-rose-50 px-3 py-1 rounded-full border border-rose-200 shadow-2xs">
                                ✕ No Active Package
                            </span>
                        @endif
                    </div>

                    @if($activePackage)
                        <div class="space-y-4">
                            <div class="p-5 bg-gradient-to-br from-teal-50/80 to-emerald-50/40 rounded-2xl border border-teal-200/80 space-y-3">
                                <h4 class="font-bold text-base text-slate-900">
                                    {{ $activePackage->packageTemplate?->name ?: ($activePackage->course?->title ?: 'Standard Monthly Package') }}
                                </h4>

                                <div class="space-y-1.5">
                                    <div class="flex justify-between text-xs font-mono font-bold">
                                        <span class="text-slate-600">{{ app()->getLocale() === 'ar' ? 'الحصص المتبقية' : 'Sessions Remaining' }}:</span>
                                        <span class="text-teal-700 font-extrabold text-sm">{{ $activePackage->remaining_sessions }} / {{ $activePackage->total_sessions }}</span>
                                    </div>
                                    @php
                                        $percentRemaining = $activePackage->total_sessions > 0 ? round(($activePackage->remaining_sessions / $activePackage->total_sessions) * 100) : 0;
                                    @endphp
                                    <div class="w-full bg-slate-200 rounded-full h-2.5 overflow-hidden">
                                        <div class="bg-teal-500 h-2.5 rounded-full transition-all duration-500" style="width: {{ $percentRemaining }}%"></div>
                                    </div>
                                </div>

                                <div class="pt-2 border-t border-teal-200/60 flex items-center justify-between text-[11px] font-mono text-slate-600">
                                    <span>📅 {{ app()->getLocale() === 'ar' ? 'تاريخ التفعيل' : 'Activated' }}: {{ $activePackage->activated_at ? $activePackage->activated_at->format('Y-m-d') : 'Active' }}</span>
                                    <span>⏳ {{ app()->getLocale() === 'ar' ? 'تاريخ الانتهاء' : 'Expires' }}: {{ $activePackage->expires_at ? $activePackage->expires_at->format('Y-m-d') : 'No Expiry' }}</span>
                                </div>
                            </div>

                            @if($packageTransactions->count() > 0)
                                <div class="space-y-2">
                                    <h5 class="font-bold text-xs font-mono text-slate-700 uppercase tracking-wider">{{ app()->getLocale() === 'ar' ? 'آخر المعاملات والخصومات' : 'Recent Transactions' }}</h5>
                                    <div class="space-y-2">
                                        @foreach($packageTransactions as $tx)
                                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-xs font-mono flex items-center justify-between">
                                                <div class="space-y-0.5">
                                                    <span class="font-bold text-slate-900 block">{{ ucfirst($tx->type) }}</span>
                                                    <span class="text-[10px] text-slate-400">{{ $tx->created_at->diffForHumans() }}</span>
                                                </div>
                                                <span class="font-bold {{ $tx->session_change < 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                                    {{ $tx->session_change > 0 ? "+{$tx->session_change}" : $tx->session_change }} credits
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <a href="{{ route('courses') }}" class="btn-lift w-full py-3 bg-teal-600 hover:bg-teal-700 text-white rounded-2xl font-bold text-xs shadow-md shadow-teal-600/30 text-center block">
                                🔄 {{ app()->getLocale() === 'ar' ? 'تجديد أو ترقية الباقة' : 'Renew / Upgrade Package' }}
                            </a>
                        </div>
                    @else
                        <div class="p-6 bg-slate-50 rounded-2xl border border-slate-200 text-center space-y-3">
                            <div class="text-3xl">💳</div>
                            <p class="text-xs font-mono text-slate-600">{{ app()->getLocale() === 'ar' ? 'لا توجد باقة حصص نشطة مرتبطة بحسابك حالياً.' : 'No active session package linked to your account.' }}</p>
                            <a href="{{ route('courses') }}" class="btn-lift px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl font-bold text-xs shadow-md shadow-rose-600/30 inline-block">
                                🛒 {{ app()->getLocale() === 'ar' ? 'تصفح الباقات والكورسات' : 'Browse Packages & Courses' }}
                            </a>
                        </div>
                    @endif
                </div>

                {{-- Linked Guardian / Parent Info Card --}}
                <div class="glass-card rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm hover:shadow-lg transition-all space-y-5 animate-fade-in-up stagger-2">
                    <h3 class="font-heading font-black text-xl text-slate-900 flex items-center gap-2 border-b border-slate-100 pb-3">
                        <span>👨‍👩‍👦</span> {{ app()->getLocale() === 'ar' ? 'بيانات ولي الأمر المرتبط' : 'Linked Parent / Guardian' }}
                    </h3>

                    <div class="space-y-3">
                        @forelse($parents as $parent)
                            <div class="p-4 bg-slate-50/90 rounded-2xl border border-slate-200 space-y-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-xs text-slate-900">{{ $parent->name }}</span>
                                    <span class="text-[10px] font-mono font-bold bg-teal-100 text-teal-900 px-2 py-0.5 rounded-full">Linked</span>
                                </div>
                                <p class="text-xs font-mono text-slate-500">📱 {{ $parent->phone ?: $parent->email }}</p>
                            </div>
                        @empty
                            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 text-xs font-mono text-slate-500 text-center space-y-1">
                                <div>👨‍👩‍👦</div>
                                <div>{{ app()->getLocale() === 'ar' ? 'لم يتم ربط حساب ولي أمر بهذا الحساب بعد.' : 'No parent account linked yet.' }}</div>
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>

    </div>
</section>

@push('scripts')
<script>
function previewAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const avatarImgs = document.querySelectorAll('.group img, .group div');
            avatarImgs.forEach(el => {
                if (el.tagName === 'IMG') {
                    el.src = e.target.result;
                }
            });
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
@endsection
