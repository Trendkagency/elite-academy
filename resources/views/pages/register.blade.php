@extends('layouts.app')

@section('content')
<section class="py-12 md:py-20 px-4 bg-[#FAFAF9] min-h-[calc(100vh-140px)] flex items-center justify-center">
    <div class="w-full max-w-md bg-white rounded-3xl p-8 sm:p-10 border border-slate-200/90 shadow-xl space-y-6">
        <div class="text-center space-y-2">
            <div class="w-14 h-14 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center text-2xl font-bold mx-auto border border-teal-100 shadow-xs">🎓</div>
            <h1 class="font-heading font-black text-2xl sm:text-3xl text-slate-900 tracking-tight">
                {{ app()->getLocale() === 'ar' ? 'إنشاء حساب جديد' : 'Create an Account' }}
            </h1>
            <p class="text-xs text-slate-500 font-mono">
                {{ app()->getLocale() === 'ar' ? 'انضم إلى أكاديمية إيليت وابدأ رحلة التعلّم التفاعلي.' : 'Join Elite Academy and start interactive learning.' }}
            </p>
        </div>

        {{-- Fallback Error / Success Alert Container --}}
        <div id="authAlert" class="hidden p-3.5 rounded-2xl text-xs font-semibold transition-all duration-200"></div>

        {{-- Dedicated Registration Form --}}
        <form id="registerForm" action="{{ route('ajax.register') }}" method="POST" class="space-y-4" novalidate>
            @csrf
            
            {{-- Full Name Input with Real-time Validation --}}
            <div class="space-y-1.5" id="group-name">
                <div class="flex justify-between items-center">
                    <label for="reg-name" class="text-xs font-bold text-slate-700">
                        {{ app()->getLocale() === 'ar' ? 'الاسم بالكامل' : 'Full Name' }}
                    </label>
                    <span id="name-live-badge" class="hidden text-[11px] font-bold px-2 py-0.5 rounded-full"></span>
                </div>
                <input 
                    type="text" 
                    id="reg-name"
                    name="name" 
                    required 
                    autocomplete="name"
                    placeholder="{{ app()->getLocale() === 'ar' ? 'مثال: أحمد محمد علي' : 'e.g. David Kovacs' }}" 
                    class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-xs font-medium text-slate-800 placeholder-slate-400 focus:bg-white focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all outline-none"
                >
                <p id="name-feedback" class="hidden text-[11px] font-semibold transition-all"></p>
            </div>

            {{-- Email Input with Real-time Availability Validation --}}
            <div class="space-y-1.5" id="group-email">
                <div class="flex justify-between items-center">
                    <label for="reg-email" class="text-xs font-bold text-slate-700">
                        {{ app()->getLocale() === 'ar' ? 'البريد الإلكتروني' : 'Email Address' }}
                    </label>
                    <span id="email-live-badge" class="hidden text-[11px] font-bold px-2 py-0.5 rounded-full"></span>
                </div>
                <div class="relative">
                    <input 
                        type="email" 
                        id="reg-email"
                        name="email" 
                        required 
                        autocomplete="email"
                        placeholder="name@example.com" 
                        class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-xs font-medium text-slate-800 placeholder-slate-400 focus:bg-white focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all outline-none"
                    >
                    <div id="email-spinner" class="hidden absolute top-1/2 -translate-y-1/2 end-3 text-teal-600">
                        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </div>
                <p id="email-feedback" class="hidden text-[11px] font-semibold transition-all"></p>
            </div>

            {{-- Phone Number Input with Real-time Availability Validation --}}
            <div class="space-y-1.5" id="group-phone">
                <div class="flex justify-between items-center">
                    <label for="reg-phone" class="text-xs font-bold text-slate-700">
                        {{ app()->getLocale() === 'ar' ? 'رقم الهاتف' : 'Phone Number' }}
                    </label>
                    <span id="phone-live-badge" class="hidden text-[11px] font-bold px-2 py-0.5 rounded-full"></span>
                </div>
                <div class="relative">
                    <input 
                        type="tel" 
                        id="reg-phone"
                        name="phone" 
                        required 
                        autocomplete="tel"
                        placeholder="01012345678" 
                        class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-xs font-medium text-slate-800 placeholder-slate-400 focus:bg-white focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all outline-none"
                    >
                    <div id="phone-spinner" class="hidden absolute top-1/2 -translate-y-1/2 end-3 text-teal-600">
                        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </div>
                <p id="phone-feedback" class="hidden text-[11px] font-semibold transition-all"></p>
            </div>

            {{-- Role Selection --}}
            <div class="space-y-3 pt-1">
                <label class="text-xs font-bold text-slate-700 flex items-center justify-between">
                    <span>{{ app()->getLocale() === 'ar' ? 'نوع الحساب' : 'Account Type' }}</span>
                    <span class="text-[10px] font-semibold text-teal-600 bg-teal-50 px-2.5 py-0.5 rounded-full border border-teal-100/80">
                        {{ app()->getLocale() === 'ar' ? 'اختر دورك' : 'Select Role' }}
                    </span>
                </label>

                <div class="grid grid-cols-3 gap-2.5 sm:gap-3 py-1">
                    <!-- Student Card (Default Checked) -->
                    <label class="relative group cursor-pointer select-none block">
                        <input type="radio" name="user_type" value="student" checked class="peer account-type-radio" style="position: absolute; opacity: 0; width: 0; height: 0; pointer-events: none;" onchange="toggleStudentGrade(this.value)">
                        <div class="h-full p-3.5 sm:p-4 rounded-2xl border-2 border-slate-200 bg-white hover:border-teal-400 transition-all duration-200 ease-out flex flex-col items-center justify-center text-center space-y-1.5 peer-checked:border-teal-600 peer-checked:bg-teal-50/50 peer-checked:shadow-md peer-checked:shadow-teal-500/10 peer-checked:ring-2 peer-checked:ring-teal-500/20 active:scale-95">
                            <div class="text-2xl sm:text-3xl transition-transform duration-200 group-hover:scale-110 peer-checked:scale-110">🎓</div>
                            <span class="text-xs font-bold text-slate-700 peer-checked:text-teal-900 transition-colors">
                                {{ app()->getLocale() === 'ar' ? 'طالب' : 'Student' }}
                            </span>
                        </div>
                    </label>

                    <!-- Parent Card -->
                    <label class="relative group cursor-pointer select-none block">
                        <input type="radio" name="user_type" value="parent" class="peer account-type-radio" style="position: absolute; opacity: 0; width: 0; height: 0; pointer-events: none;" onchange="toggleStudentGrade(this.value)">
                        <div class="h-full p-3.5 sm:p-4 rounded-2xl border-2 border-slate-200 bg-white hover:border-teal-400 transition-all duration-200 ease-out flex flex-col items-center justify-center text-center space-y-1.5 peer-checked:border-teal-600 peer-checked:bg-teal-50/50 peer-checked:shadow-md peer-checked:shadow-teal-500/10 peer-checked:ring-2 peer-checked:ring-teal-500/20 active:scale-95">
                            <div class="text-2xl sm:text-3xl transition-transform duration-200 group-hover:scale-110 peer-checked:scale-110">👨‍👩‍👧</div>
                            <span class="text-xs font-bold text-slate-700 peer-checked:text-teal-900 transition-colors">
                                {{ app()->getLocale() === 'ar' ? 'ولي أمر' : 'Parent' }}
                            </span>
                        </div>
                    </label>

                    <!-- Teacher Card -->
                    <label class="relative group cursor-pointer select-none block">
                        <input type="radio" name="user_type" value="teacher" class="peer account-type-radio" style="position: absolute; opacity: 0; width: 0; height: 0; pointer-events: none;" onchange="toggleStudentGrade(this.value)">
                        <div class="h-full p-3.5 sm:p-4 rounded-2xl border-2 border-slate-200 bg-white hover:border-teal-400 transition-all duration-200 ease-out flex flex-col items-center justify-center text-center space-y-1.5 peer-checked:border-teal-600 peer-checked:bg-teal-50/50 peer-checked:shadow-md peer-checked:shadow-teal-500/10 peer-checked:ring-2 peer-checked:ring-teal-500/20 active:scale-95">
                            <div class="text-2xl sm:text-3xl transition-transform duration-200 group-hover:scale-110 peer-checked:scale-110">👨‍🏫</div>
                            <span class="text-xs font-bold text-slate-700 peer-checked:text-teal-900 transition-colors">
                                {{ app()->getLocale() === 'ar' ? 'معلم' : 'Teacher' }}
                            </span>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Student-Specific Fields: Grade Level & School Name --}}
            <div id="studentFieldsGroup" class="space-y-4 pt-1">
                {{-- Grade Level Select --}}
                <div class="space-y-1.5" id="group-grade">
                    <label for="reg-grade" class="text-xs font-bold text-slate-700 flex items-center justify-between">
                        <span>{{ app()->getLocale() === 'ar' ? 'الصف الدراسي' : 'Grade Level' }}</span>
                        <span class="text-[10px] text-teal-600 font-bold">* {{ app()->getLocale() === 'ar' ? 'مطلوب للطالب' : 'Required for Student' }}</span>
                    </label>
                    <select 
                        id="reg-grade"
                        name="grade_level_id" 
                        class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-xs font-bold text-slate-800 focus:bg-white focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all outline-none"
                    >
                        <option value="">{{ app()->getLocale() === 'ar' ? 'اختر الصف الدراسي...' : 'Select Grade Level...' }}</option>
                        @foreach($gradeLevels ?? [] as $g)
                            <option value="{{ $g->id }}">{{ __($g->name) }}</option>
                        @endforeach
                    </select>
                    <p id="grade-feedback" class="hidden text-[11px] font-semibold transition-all"></p>
                </div>

                {{-- School Name --}}
                <div class="space-y-1.5">
                    <label for="reg-school" class="text-xs font-bold text-slate-700">
                        {{ app()->getLocale() === 'ar' ? 'اسم المدرسة (اختياري)' : 'School Name (Optional)' }}
                    </label>
                    <input 
                        type="text" 
                        id="reg-school"
                        name="school_name" 
                        placeholder="{{ app()->getLocale() === 'ar' ? 'مثال: مدرسة المتفوقين للغات' : 'e.g. Cairo International School' }}" 
                        class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-xs font-medium text-slate-800 placeholder-slate-400 focus:bg-white focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all outline-none"
                    >
                </div>
            </div>

            {{-- Password Input with Strength Meter & Show/Hide --}}
            <div class="space-y-1.5" id="group-password">
                <div class="flex justify-between items-center">
                    <label for="reg-password" class="text-xs font-bold text-slate-700">
                        {{ app()->getLocale() === 'ar' ? 'كلمة المرور' : 'Password' }}
                    </label>
                    <span id="password-strength-badge" class="hidden text-[10px] font-bold px-2 py-0.5 rounded-full"></span>
                </div>
                <div class="relative">
                    <input 
                        type="password" 
                        id="reg-password"
                        name="password" 
                        required 
                        autocomplete="new-password"
                        placeholder="••••••••" 
                        class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-xs font-medium text-slate-800 placeholder-slate-400 focus:bg-white focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all outline-none pe-10"
                    >
                    <button 
                        type="button" 
                        id="toggleRegPasswordBtn"
                        aria-label="Toggle password visibility"
                        class="absolute top-1/2 -translate-y-1/2 end-3 text-slate-400 hover:text-slate-600 p-1 rounded-lg focus:outline-none text-sm transition-colors"
                    >
                        👁️
                    </button>
                </div>
                {{-- Strength Bar --}}
                <div id="password-strength-container" class="hidden w-full bg-slate-100 rounded-full h-1 mt-1 overflow-hidden">
                    <div id="password-strength-bar" class="h-full w-0 transition-all duration-300"></div>
                </div>
                <p id="password-feedback" class="hidden text-[11px] font-semibold transition-all"></p>
            </div>

            <button type="submit" id="submitBtn" class="w-full btn-mobile-lg btn-lift text-white bg-teal-600 hover:bg-teal-700 shadow-md shadow-teal-600/20 touch-press mt-2 flex items-center justify-center gap-2 font-bold py-3.5 rounded-2xl transition-all">
                <span id="btnText">{{ app()->getLocale() === 'ar' ? 'إنشاء الحساب والبدء' : 'Create Account & Start' }}</span>
                <span id="btnIcon" class="arrow-icon">&rarr;</span>
                <svg id="btnSpinner" class="hidden animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </button>
        </form>

        {{-- Separate Login Redirection Link --}}
        <div class="pt-6 border-t border-slate-100 text-center">
            <p class="text-xs text-slate-500 font-mono">
                {{ app()->getLocale() === 'ar' ? 'لديك حساب بالفعل؟' : 'Already have an account?' }}
                <a href="{{ route('login') }}" class="font-bold text-teal-600 hover:text-teal-700 hover:underline ms-1">
                    {{ app()->getLocale() === 'ar' ? 'تسجيل الدخول للمنصة ←' : 'Log In to Portal →' }}
                </a>
            </p>
        </div>
    </div>
</section>

<script>
function toggleStudentGrade(val) {
    const studentGroup = document.getElementById('studentFieldsGroup');
    if (studentGroup) {
        studentGroup.style.display = (val === 'student') ? 'block' : 'none';
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const isAr = document.documentElement.lang === 'ar' || document.dir === 'rtl';
    const form = document.getElementById('registerForm');
    const submitBtn = document.getElementById('submitBtn');
    const btnText = document.getElementById('btnText');
    const btnIcon = document.getElementById('btnIcon');
    const btnSpinner = document.getElementById('btnSpinner');
    const alertBox = document.getElementById('authAlert');

    const nameInput = document.getElementById('reg-name');
    const nameFeedback = document.getElementById('name-feedback');
    const nameBadge = document.getElementById('name-live-badge');

    const emailInput = document.getElementById('reg-email');
    const emailBadge = document.getElementById('email-live-badge');
    const emailFeedback = document.getElementById('email-feedback');
    const emailSpinner = document.getElementById('email-spinner');

    const phoneInput = document.getElementById('reg-phone');
    const phoneBadge = document.getElementById('phone-live-badge');
    const phoneFeedback = document.getElementById('phone-feedback');
    const phoneSpinner = document.getElementById('phone-spinner');

    const gradeSelect = document.getElementById('reg-grade');
    const gradeFeedback = document.getElementById('grade-feedback');

    const passwordInput = document.getElementById('reg-password');
    const passwordFeedback = document.getElementById('password-feedback');
    const passwordStrengthBadge = document.getElementById('password-strength-badge');
    const passwordStrengthContainer = document.getElementById('password-strength-container');
    const passwordStrengthBar = document.getElementById('password-strength-bar');
    const toggleRegPasswordBtn = document.getElementById('toggleRegPasswordBtn');

    const checkEmailUrl = "{{ route('ajax.validate.email-available') }}";
    const checkPhoneUrl = "{{ route('ajax.validate.phone-available') }}";
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

    // Toggle Password Visibility
    if (toggleRegPasswordBtn && passwordInput) {
        toggleRegPasswordBtn.addEventListener('click', function () {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            toggleRegPasswordBtn.textContent = isPassword ? '🙈' : '👁️';
        });
    }

    // Helper: Toast / Alert Notification (Top Center)
    function notify(message, isDanger = true, title = null) {
        const defaultTitle = isDanger 
            ? (isAr ? 'فشل إنشاء الحساب' : 'Registration Failed') 
            : (isAr ? 'تم إنشاء الحساب' : 'Registration Successful');

        if (window.Toast) {
            if (isDanger) {
                window.Toast.danger(message, title || defaultTitle);
            } else {
                window.Toast.success(message, title || defaultTitle);
            }
        } else if (alertBox) {
            alertBox.className = `p-3.5 rounded-2xl text-xs font-semibold ${isDanger ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200'}`;
            alertBox.textContent = Array.isArray(message) ? message.join(' ') : message;
            alertBox.classList.remove('hidden');
        }
    }

    // Field Status Utilities
    function setFieldState(input, feedbackEl, badgeEl, state, message = '', badgeText = '') {
        if (input) {
            input.classList.remove('border-rose-400', 'bg-rose-50/40', 'focus:border-rose-500', 'focus:ring-rose-500/20',
                                   'border-emerald-400', 'bg-emerald-50/40', 'focus:border-emerald-500', 'focus:ring-emerald-500/20',
                                   'border-slate-200', 'bg-slate-50');

            if (state === 'error') {
                input.classList.add('border-rose-400', 'bg-rose-50/40', 'focus:border-rose-500', 'focus:ring-rose-500/20');
            } else if (state === 'success') {
                input.classList.add('border-emerald-400', 'bg-emerald-50/40', 'focus:border-emerald-500', 'focus:ring-emerald-500/20');
            } else {
                input.classList.add('border-slate-200', 'bg-slate-50');
            }
        }

        if (feedbackEl) {
            if (state === 'error') {
                feedbackEl.textContent = message;
                feedbackEl.className = 'text-[11px] font-semibold text-rose-600 block';
            } else if (state === 'success') {
                feedbackEl.textContent = message;
                feedbackEl.className = 'text-[11px] font-semibold text-emerald-600 block';
            } else {
                feedbackEl.textContent = '';
                feedbackEl.className = 'hidden';
            }
        }

        if (badgeEl) {
            if (state === 'error') {
                badgeEl.textContent = badgeText || (isAr ? '✕ غير صالح' : '✕ Invalid');
                badgeEl.className = 'text-[11px] font-bold px-2 py-0.5 rounded-full bg-rose-100 text-rose-700 block';
            } else if (state === 'success') {
                badgeEl.textContent = badgeText || (isAr ? '✓ متاح' : '✓ Available');
                badgeEl.className = 'text-[11px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 block';
            } else {
                badgeEl.textContent = '';
                badgeEl.className = 'hidden';
            }
        }
    }

    // Name Real-Time Validation
    if (nameInput) {
        nameInput.addEventListener('input', function () {
            const val = this.value.trim();
            if (!val) {
                setFieldState(nameInput, nameFeedback, nameBadge, 'idle');
            } else if (val.length < 3) {
                setFieldState(nameInput, nameFeedback, nameBadge, 'error', isAr ? 'يجب أن يكون الاسم 3 أحرف على الأقل' : 'Name must be at least 3 characters', isAr ? '✕ قصير جداً' : '✕ Too short');
            } else {
                setFieldState(nameInput, nameFeedback, nameBadge, 'success', '', isAr ? '✓ صالح' : '✓ Valid');
            }
        });
    }

    // Email Syntax Validator
    function isValidEmailSyntax(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    // Debounced Real-Time Email Availability
    let emailDebounceTimer = null;
    let lastCheckedEmail = '';

    async function checkEmailRealtime(value) {
        const email = value.trim().toLowerCase();
        if (!email) {
            setFieldState(emailInput, emailFeedback, emailBadge, 'idle');
            return null;
        }

        if (!isValidEmailSyntax(email)) {
            setFieldState(emailInput, emailFeedback, emailBadge, 'error', isAr ? 'يرجى إدخال بريد إلكتروني صحيح' : 'Please enter a valid email address', isAr ? '✕ غير صحيح' : '✕ Invalid');
            return false;
        }

        if (email === lastCheckedEmail) return true;

        if (emailSpinner) emailSpinner.classList.remove('hidden');

        try {
            const res = await fetch(checkEmailUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ email: email })
            });
            const data = await res.json();
            lastCheckedEmail = email;

            if (emailSpinner) emailSpinner.classList.add('hidden');

            if (data.available) {
                setFieldState(emailInput, emailFeedback, emailBadge, 'success', data.message || (isAr ? 'البريد متاح وجاهز للاستخدام' : 'Email is available'), isAr ? '✓ متاح' : '✓ Available');
                return true;
            } else {
                setFieldState(emailInput, emailFeedback, emailBadge, 'error', data.message || (isAr ? 'هذا البريد الإلكتروني مسجل مسبقاً' : 'This email is already registered'), isAr ? '✕ مسجل مسبقاً' : '✕ Taken');
                return false;
            }
        } catch (err) {
            if (emailSpinner) emailSpinner.classList.add('hidden');
            return null;
        }
    }

    if (emailInput) {
        emailInput.addEventListener('input', function () {
            clearTimeout(emailDebounceTimer);
            const val = this.value.trim();
            if (!val) {
                setFieldState(emailInput, emailFeedback, emailBadge, 'idle');
                return;
            }
            emailDebounceTimer = setTimeout(() => {
                checkEmailRealtime(val);
            }, 380);
        });

        emailInput.addEventListener('blur', function () {
            const val = this.value.trim();
            if (val) {
                checkEmailRealtime(val);
            }
        });
    }

    // Phone Real-Time Availability Validation
    let phoneDebounceTimer = null;
    let lastCheckedPhone = '';

    async function checkPhoneRealtime(value) {
        const phone = value.trim();
        if (!phone) {
            setFieldState(phoneInput, phoneFeedback, phoneBadge, 'idle');
            return null;
        }

        if (phone.length < 7) {
            setFieldState(phoneInput, phoneFeedback, phoneBadge, 'error', isAr ? 'يرجى إدخال رقم هاتف صحيح (7 أرقام على الأقل)' : 'Please enter a valid phone number (at least 7 digits)', isAr ? '✕ غير صالح' : '✕ Invalid');
            return false;
        }

        if (phone === lastCheckedPhone) return true;

        if (phoneSpinner) phoneSpinner.classList.remove('hidden');

        try {
            const res = await fetch(checkPhoneUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ phone: phone })
            });
            const data = await res.json();
            lastCheckedPhone = phone;

            if (phoneSpinner) phoneSpinner.classList.add('hidden');

            if (data.available) {
                setFieldState(phoneInput, phoneFeedback, phoneBadge, 'success', data.message || (isAr ? 'رقم الهاتف متاح وجاهز' : 'Phone is available'), isAr ? '✓ متاح' : '✓ Available');
                return true;
            } else {
                setFieldState(phoneInput, phoneFeedback, phoneBadge, 'error', data.message || (isAr ? 'رقم الهاتف مسجل مسبقاً' : 'This phone number is already registered'), isAr ? '✕ مسجل مسبقاً' : '✕ Taken');
                return false;
            }
        } catch (err) {
            if (phoneSpinner) phoneSpinner.classList.add('hidden');
            return null;
        }
    }

    if (phoneInput) {
        phoneInput.addEventListener('input', function () {
            clearTimeout(phoneDebounceTimer);
            const val = this.value.trim();
            if (!val) {
                setFieldState(phoneInput, phoneFeedback, phoneBadge, 'idle');
                return;
            }
            phoneDebounceTimer = setTimeout(() => {
                checkPhoneRealtime(val);
            }, 380);
        });

        phoneInput.addEventListener('blur', function () {
            const val = this.value.trim();
            if (val) {
                checkPhoneRealtime(val);
            }
        });
    }

    // Password Real-Time Strength Meter
    if (passwordInput) {
        passwordInput.addEventListener('input', function () {
            const val = this.value;
            if (!val) {
                setFieldState(passwordInput, passwordFeedback, null, 'idle');
                if (passwordStrengthContainer) passwordStrengthContainer.classList.add('hidden');
                if (passwordStrengthBadge) passwordStrengthBadge.classList.add('hidden');
                return;
            }

            if (passwordStrengthContainer) passwordStrengthContainer.classList.remove('hidden');
            if (passwordStrengthBadge) passwordStrengthBadge.classList.remove('hidden');

            let score = 0;
            if (val.length >= 8) score += 1;
            if (val.length >= 12) score += 1;
            if (/[0-9]/.test(val)) score += 1;
            if (/[A-Z]/.test(val) || /[^A-Za-z0-9]/.test(val)) score += 1;

            if (val.length < 8) {
                setFieldState(passwordInput, passwordFeedback, null, 'error', isAr ? 'يجب ألا تقل كلمة المرور عن 8 أحرف' : 'Password must be at least 8 characters');
                passwordStrengthBar.style.width = '25%';
                passwordStrengthBar.className = 'h-full bg-rose-500';
                passwordStrengthBadge.textContent = isAr ? 'ضعيفة جداً' : 'Very Weak';
                passwordStrengthBadge.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full bg-rose-100 text-rose-700 block';
            } else if (score <= 2) {
                setFieldState(passwordInput, passwordFeedback, null, 'idle');
                passwordStrengthBar.style.width = '50%';
                passwordStrengthBar.className = 'h-full bg-amber-500';
                passwordStrengthBadge.textContent = isAr ? 'متوسطة' : 'Fair';
                passwordStrengthBadge.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 block';
            } else if (score === 3) {
                setFieldState(passwordInput, passwordFeedback, null, 'success');
                passwordStrengthBar.style.width = '75%';
                passwordStrengthBar.className = 'h-full bg-teal-500';
                passwordStrengthBadge.textContent = isAr ? 'جيدة' : 'Good';
                passwordStrengthBadge.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full bg-teal-100 text-teal-700 block';
            } else {
                setFieldState(passwordInput, passwordFeedback, null, 'success');
                passwordStrengthBar.style.width = '100%';
                passwordStrengthBar.className = 'h-full bg-emerald-600';
                passwordStrengthBadge.textContent = isAr ? 'قوية جداً' : 'Strong';
                passwordStrengthBadge.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 block';
            }
        });
    }

    // Grade Select Real-Time Feedback
    if (gradeSelect) {
        gradeSelect.addEventListener('change', function () {
            if (this.value) {
                setFieldState(gradeSelect, gradeFeedback, null, 'idle');
            }
        });
    }

    // Form Submit Handler with Top-Center Toast Notifications
    if (form) {
        form.addEventListener('submit', async function (e) {
            e.preventDefault();
            if (alertBox) alertBox.classList.add('hidden');

            const name = nameInput.value.trim();
            const email = emailInput.value.trim();
            const phone = phoneInput.value.trim();
            const password = passwordInput.value;
            const userType = (document.querySelector('input[name="user_type"]:checked') || {}).value || 'student';
            const gradeLevel = gradeSelect ? gradeSelect.value : '';

            // Client-Side Pre-Validation
            if (!name || name.length < 3) {
                setFieldState(nameInput, nameFeedback, nameBadge, 'error', isAr ? 'يرجى كتابة الاسم بالكامل (3 أحرف على الأقل)' : 'Please enter your full name (minimum 3 characters)');
                notify(isAr ? 'يرجى كتابة الاسم بالكامل' : 'Please enter your full name', true);
                nameInput.focus();
                return;
            }

            if (!email || !isValidEmailSyntax(email)) {
                setFieldState(emailInput, emailFeedback, emailBadge, 'error', isAr ? 'يرجى إدخال بريد إلكتروني صحيح' : 'Please enter a valid email address');
                notify(isAr ? 'يرجى إدخال بريد إلكتروني صحيح' : 'Please enter a valid email address', true);
                emailInput.focus();
                return;
            }

            if (!phone || phone.length < 7) {
                setFieldState(phoneInput, phoneFeedback, phoneBadge, 'error', isAr ? 'يرجى إدخال رقم هاتف صحيح' : 'Please enter a valid phone number');
                notify(isAr ? 'يرجى إدخال رقم هاتف صحيح' : 'Please enter a valid phone number', true);
                phoneInput.focus();
                return;
            }

            if (userType === 'student' && !gradeLevel) {
                setFieldState(gradeSelect, gradeFeedback, null, 'error', isAr ? 'يرجى اختيار الصف الدراسي' : 'Please select a grade level');
                notify(isAr ? 'يرجى تحديد الصف الدراسي للطالب' : 'Please select a grade level for student registration', true);
                if (gradeSelect) gradeSelect.focus();
                return;
            }

            if (!password || password.length < 8) {
                setFieldState(passwordInput, passwordFeedback, null, 'error', isAr ? 'يجب ألا تقل كلمة المرور عن 8 أحرف' : 'Password must be at least 8 characters');
                notify(isAr ? 'يجب ألا تقل كلمة المرور عن 8 أحرف' : 'Password must be at least 8 characters', true);
                passwordInput.focus();
                return;
            }

            // Lock Submit Button with Spinner
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-80', 'cursor-not-allowed');
            if (btnText) btnText.textContent = isAr ? 'جاري إنشاء الحساب...' : 'Creating Account...';
            if (btnIcon) btnIcon.classList.add('hidden');
            if (btnSpinner) btnSpinner.classList.remove('hidden');

            try {
                const formData = new FormData(form);
                const res = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: { 
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                const data = await res.json();

                if (!res.ok || !data.success) {
                    let errMsg = data.message || (isAr ? 'فشل إنشاء الحساب. يرجى مراجعة البيانات.' : 'Registration failed. Please check input fields.');

                    // Specific field error highlights
                    if (data.errors) {
                        if (data.errors.name) setFieldState(nameInput, nameFeedback, nameBadge, 'error', data.errors.name[0]);
                        if (data.errors.email) setFieldState(emailInput, emailFeedback, emailBadge, 'error', data.errors.email[0], isAr ? '✕ مسجل مسبقاً' : '✕ Taken');
                        if (data.errors.phone) setFieldState(phoneInput, phoneFeedback, phoneBadge, 'error', data.errors.phone[0], isAr ? '✕ مسجل مسبقاً' : '✕ Taken');
                        if (data.errors.password) setFieldState(passwordInput, passwordFeedback, null, 'error', data.errors.password[0]);
                        if (data.errors.grade_level_id) setFieldState(gradeSelect, gradeFeedback, null, 'error', data.errors.grade_level_id[0]);

                        const errs = Object.values(data.errors).flat();
                        if (errs.length > 0) errMsg = errs;
                    }

                    // Trigger Top-Center Danger Toast
                    notify(errMsg, true);

                    // Reset button
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-80', 'cursor-not-allowed');
                    if (btnText) btnText.textContent = isAr ? 'إنشاء الحساب والبدء' : 'Create Account & Start';
                    if (btnIcon) btnIcon.classList.remove('hidden');
                    if (btnSpinner) btnSpinner.classList.add('hidden');
                    return;
                }

                // Success State: Top-Center Success Toast & Smooth Redirect
                const successMsg = data.message || (isAr ? 'تم إنشاء الحساب بنجاح! جاري التوجيه...' : 'Account created successfully! Redirecting...');
                notify(successMsg, false);

                if (btnText) btnText.textContent = isAr ? 'تم بنجاح! جاري التوجيه...' : 'Account Created! Redirecting...';

                setTimeout(() => {
                    window.location.href = data.redirect_url || '/login';
                }, 900);

            } catch (err) {
                notify(isAr ? 'حدث خطأ في الاتصال بالشبكة. يرجى المحاولة لاحقاً.' : 'Network connection error. Please try again.', true);
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-80', 'cursor-not-allowed');
                if (btnText) btnText.textContent = isAr ? 'إنشاء الحساب والبدء' : 'Create Account & Start';
                if (btnIcon) btnIcon.classList.remove('hidden');
                if (btnSpinner) btnSpinner.classList.add('hidden');
            }
        });
    }
});
</script>
@endsection
