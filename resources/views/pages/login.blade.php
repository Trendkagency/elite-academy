@extends('layouts.app')

@section('content')
<section class="py-12 md:py-20 px-4 bg-[#FAFAF9] min-h-[calc(100vh-140px)] flex items-center justify-center">
    <div class="w-full max-w-md bg-white rounded-3xl p-8 sm:p-10 border border-slate-200/90 shadow-xl space-y-6">
        <div class="text-center space-y-2">
            <div class="w-14 h-14 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center text-2xl font-bold mx-auto border border-teal-100 shadow-xs">🔑</div>
            <h1 class="font-heading font-black text-2xl sm:text-3xl text-slate-900 tracking-tight">
                {{ app()->getLocale() === 'ar' ? 'تسجيل الدخول للمنصة' : 'Sign In to Portal' }}
            </h1>
            <p class="text-xs text-slate-500 font-mono">
                {{ app()->getLocale() === 'ar' ? 'أدخل معلومات حسابك للوصول لبوابة المقررات والأداء الأكاديمي.' : 'Access your courses, grades, and academic dashboard.' }}
            </p>
        </div>

        {{-- Fallback Error / Success Alert Container --}}
        <div id="authAlert" class="hidden p-3.5 rounded-2xl text-xs font-semibold transition-all duration-200"></div>

        {{-- Dedicated Sign In Form --}}
        <form id="signinForm" action="{{ route('ajax.login') }}" method="POST" class="space-y-4" novalidate>
            @csrf
            
            {{-- Email Input with Real-time Validation --}}
            <div class="space-y-1.5" id="group-email">
                <div class="flex justify-between items-center">
                    <label for="login-email" class="text-xs font-bold text-slate-700">
                        {{ app()->getLocale() === 'ar' ? 'البريد الإلكتروني' : 'Email Address' }}
                    </label>
                    <span id="email-live-badge" class="hidden text-[11px] font-bold px-2 py-0.5 rounded-full"></span>
                </div>
                <div class="relative">
                    <input 
                        type="email" 
                        id="login-email"
                        name="email" 
                        required 
                        autocomplete="email"
                        placeholder="student@eliteacademy.edu.eg" 
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

            {{-- Password Input with Show/Hide Toggle & Real-time Validation --}}
            <div class="space-y-1.5" id="group-password">
                <div class="flex justify-between items-center">
                    <label for="login-password" class="text-xs font-bold text-slate-700">
                        {{ app()->getLocale() === 'ar' ? 'كلمة المرور' : 'Password' }}
                    </label>
                    <a href="#" class="text-xs text-teal-600 hover:underline font-bold">{{ app()->getLocale() === 'ar' ? 'نسيت كلمة المرور؟' : 'Forgot Password?' }}</a>
                </div>
                <div class="relative">
                    <input 
                        type="password" 
                        id="login-password"
                        name="password" 
                        required 
                        autocomplete="current-password"
                        placeholder="••••••••" 
                        class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-xs font-medium text-slate-800 placeholder-slate-400 focus:bg-white focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all outline-none pe-10"
                    >
                    <button 
                        type="button" 
                        id="togglePasswordBtn"
                        aria-label="Toggle password visibility"
                        class="absolute top-1/2 -translate-y-1/2 end-3 text-slate-400 hover:text-slate-600 p-1 rounded-lg focus:outline-none text-sm transition-colors"
                    >
                        👁️
                    </button>
                </div>
                <p id="password-feedback" class="hidden text-[11px] font-semibold transition-all"></p>
            </div>

            <div class="flex items-center justify-between text-xs pt-1">
                <label class="inline-flex items-center gap-2 cursor-pointer text-slate-600 font-medium">
                    <input type="checkbox" name="remember" value="1" class="rounded border-slate-300 text-teal-600 focus:ring-teal-500">
                    <span>{{ app()->getLocale() === 'ar' ? 'تذكرني على هذا الجهاز' : 'Remember me' }}</span>
                </label>
            </div>

            <button type="submit" id="submitBtn" class="w-full btn-mobile-lg btn-lift text-white bg-teal-600 hover:bg-teal-700 shadow-md shadow-teal-600/20 touch-press mt-2 flex items-center justify-center gap-2 font-bold py-3.5 rounded-2xl transition-all">
                <span id="btnText">{{ app()->getLocale() === 'ar' ? 'تسجيل الدخول' : 'Sign In to Portal' }}</span>
                <span id="btnIcon" class="arrow-icon">&rarr;</span>
                <svg id="btnSpinner" class="hidden animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </button>
        </form>

        {{-- Separate Register Link --}}
        <div class="pt-6 border-t border-slate-100 text-center">
            <p class="text-xs text-slate-500 font-mono">
                {{ app()->getLocale() === 'ar' ? 'ليس لديك حساب حتى الآن؟' : "Don't have an account yet?" }}
                <a href="{{ route('register') }}" class="font-bold text-teal-600 hover:text-teal-700 hover:underline ms-1">
                    {{ app()->getLocale() === 'ar' ? 'إنشاء حساب جديد ←' : 'Create an Account &rarr;' }}
                </a>
            </p>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const isAr = document.documentElement.lang === 'ar' || document.dir === 'rtl';
    const form = document.getElementById('signinForm');
    const submitBtn = document.getElementById('submitBtn');
    const btnText = document.getElementById('btnText');
    const btnIcon = document.getElementById('btnIcon');
    const btnSpinner = document.getElementById('btnSpinner');
    const alertBox = document.getElementById('authAlert');

    const emailInput = document.getElementById('login-email');
    const emailBadge = document.getElementById('email-live-badge');
    const emailFeedback = document.getElementById('email-feedback');
    const emailSpinner = document.getElementById('email-spinner');

    const passwordInput = document.getElementById('login-password');
    const passwordFeedback = document.getElementById('password-feedback');
    const togglePasswordBtn = document.getElementById('togglePasswordBtn');

    const checkEmailUrl = "{{ route('ajax.validate.email-exists') }}";
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

    // Toggle Password Visibility
    if (togglePasswordBtn && passwordInput) {
        togglePasswordBtn.addEventListener('click', function () {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            togglePasswordBtn.textContent = isPassword ? '🙈' : '👁️';
        });
    }

    // Helper: Toast / Alert Notification (Top Center)
    function notify(message, isDanger = true, title = null) {
        const defaultTitle = isDanger 
            ? (isAr ? 'فشل تسجيل الدخول' : 'Login Failed') 
            : (isAr ? 'تم تسجيل الدخول' : 'Login Successful');

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
    function setFieldState(input, feedbackEl, badgeEl, state, message = '') {
        // Clear previous classes
        input.classList.remove('border-rose-400', 'bg-rose-50/40', 'focus:border-rose-500', 'focus:ring-rose-500/20',
                               'border-emerald-400', 'bg-emerald-50/40', 'focus:border-emerald-500', 'focus:ring-emerald-500/20',
                               'border-slate-200', 'bg-slate-50');

        if (state === 'error') {
            input.classList.add('border-rose-400', 'bg-rose-50/40', 'focus:border-rose-500', 'focus:ring-rose-500/20');
            if (feedbackEl) {
                feedbackEl.textContent = message;
                feedbackEl.className = 'text-[11px] font-semibold text-rose-600 block';
            }
            if (badgeEl) {
                badgeEl.textContent = isAr ? '✕ غير مسجل' : '✕ Not found';
                badgeEl.className = 'text-[11px] font-bold px-2 py-0.5 rounded-full bg-rose-100 text-rose-700 block';
            }
        } else if (state === 'success') {
            input.classList.add('border-emerald-400', 'bg-emerald-50/40', 'focus:border-emerald-500', 'focus:ring-emerald-500/20');
            if (feedbackEl) {
                feedbackEl.textContent = message;
                feedbackEl.className = 'text-[11px] font-semibold text-emerald-600 block';
            }
            if (badgeEl) {
                badgeEl.textContent = isAr ? '✓ حساب موجود' : '✓ Verified';
                badgeEl.className = 'text-[11px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 block';
            }
        } else {
            input.classList.add('border-slate-200', 'bg-slate-50');
            if (feedbackEl) {
                feedbackEl.textContent = '';
                feedbackEl.className = 'hidden';
            }
            if (badgeEl) {
                badgeEl.textContent = '';
                badgeEl.className = 'hidden';
            }
        }
    }

    // Email Syntax Validator
    function isValidEmailSyntax(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    // Debounced Real-Time Email Validation
    let emailDebounceTimer = null;
    let lastCheckedEmail = '';

    async function checkEmailRealtime(value, immediate = false) {
        const email = value.trim().toLowerCase();
        if (!email) {
            setFieldState(emailInput, emailFeedback, emailBadge, 'idle');
            return null;
        }

        if (!isValidEmailSyntax(email)) {
            setFieldState(emailInput, emailFeedback, emailBadge, 'error', isAr ? 'يرجى إدخال بريد إلكتروني صحيح' : 'Please enter a valid email address');
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

            if (data.exists) {
                setFieldState(emailInput, emailFeedback, emailBadge, 'success', data.message || (isAr ? 'تم العثور على الحساب' : 'Account verified'));
                return true;
            } else {
                setFieldState(emailInput, emailFeedback, emailBadge, 'error', data.message || (isAr ? 'هذا البريد الإلكتروني غير مسجل لدينا' : 'This email is not registered in our system'));
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
                checkEmailRealtime(val, true);
            }
        });
    }

    // Password Real-Time Feedback
    if (passwordInput) {
        passwordInput.addEventListener('input', function () {
            if (this.value.length > 0) {
                setFieldState(passwordInput, passwordFeedback, null, 'idle');
            }
        });
    }

    // Form Submit Handler with Top-Center Toast Notifications
    if (form) {
        form.addEventListener('submit', async function (e) {
            e.preventDefault();
            if (alertBox) alertBox.classList.add('hidden');

            const email = emailInput.value.trim();
            const password = passwordInput.value;

            // Client-Side Pre-Validation
            if (!email) {
                setFieldState(emailInput, emailFeedback, emailBadge, 'error', isAr ? 'البريد الإلكتروني مطلوب' : 'Email is required');
                notify(isAr ? 'يرجى إدخال البريد الإلكتروني' : 'Please enter your email address', true);
                emailInput.focus();
                return;
            }

            if (!isValidEmailSyntax(email)) {
                setFieldState(emailInput, emailFeedback, emailBadge, 'error', isAr ? 'يرجى إدخال بريد إلكتروني صحيح' : 'Please enter a valid email address');
                notify(isAr ? 'صيغة البريد الإلكتروني غير صحيحة' : 'Invalid email format', true);
                emailInput.focus();
                return;
            }

            if (!password) {
                setFieldState(passwordInput, passwordFeedback, null, 'error', isAr ? 'كلمة المرور مطلوبة' : 'Password is required');
                notify(isAr ? 'يرجى إدخال كلمة المرور' : 'Please enter your password', true);
                passwordInput.focus();
                return;
            }

            // Lock Submit Button with Spinner
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-80', 'cursor-not-allowed');
            if (btnText) btnText.textContent = isAr ? 'جاري التحقق...' : 'Signing In...';
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
                    let errMsg = data.message || (isAr ? 'البيانات المدخلة غير صحيحة.' : 'Login failed. Please check your credentials.');
                    
                    // Field specific feedback
                    if (data.field === 'email' || (data.errors && data.errors.email)) {
                        const emailErr = data.errors?.email?.[0] || data.message;
                        setFieldState(emailInput, emailFeedback, emailBadge, 'error', emailErr);
                        errMsg = emailErr;
                        emailInput.focus();
                    } else if (data.field === 'password' || (data.errors && data.errors.password)) {
                        const pwdErr = data.errors?.password?.[0] || data.message;
                        setFieldState(passwordInput, passwordFeedback, null, 'error', pwdErr);
                        errMsg = pwdErr;
                        passwordInput.focus();
                    } else if (data.errors) {
                        const errs = Object.values(data.errors).flat();
                        if (errs.length > 0) errMsg = errs;
                    }

                    // Trigger Top-Center Danger Toast
                    notify(errMsg, true);

                    // Reset button
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-80', 'cursor-not-allowed');
                    if (btnText) btnText.textContent = isAr ? 'تسجيل الدخول' : 'Sign In to Portal';
                    if (btnIcon) btnIcon.classList.remove('hidden');
                    if (btnSpinner) btnSpinner.classList.add('hidden');
                    return;
                }

                // Success State: Top-Center Success Toast & Smooth Redirect
                setFieldState(emailInput, emailFeedback, emailBadge, 'success');
                setFieldState(passwordInput, passwordFeedback, null, 'success');
                
                const successMsg = data.message || (isAr ? 'تم تسجيل الدخول بنجاح! جاري التوجيه...' : 'Login successful! Redirecting...');
                notify(successMsg, false);

                if (btnText) btnText.textContent = isAr ? 'تم بنجاح! جاري التوجيه...' : 'Success! Redirecting...';

                setTimeout(() => {
                    window.location.href = data.redirect_url || '/student-portal';
                }, 800);

            } catch (err) {
                notify(isAr ? 'حدث خطأ في الاتصال بالشبكة. يرجى المحاولة لاحقاً.' : 'Network connection error. Please try again.', true);
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-80', 'cursor-not-allowed');
                if (btnText) btnText.textContent = isAr ? 'تسجيل الدخول' : 'Sign In to Portal';
                if (btnIcon) btnIcon.classList.remove('hidden');
                if (btnSpinner) btnSpinner.classList.add('hidden');
            }
        });
    }
});
</script>
@endsection
