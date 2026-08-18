@extends('layouts.app')

@section('content')
<section class="py-12 md:py-20 px-4 bg-[#FAFAF9]">
    <div class="max-w-md mx-auto bg-white rounded-3xl p-8 sm:p-10 border border-slate-200/90 shadow-xl space-y-6">
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
        <div id="authAlert" class="hidden p-3.5 rounded-2xl text-xs font-semibold"></div>

        {{-- Dedicated Sign In Form --}}
        <form id="signinForm" action="{{ route('ajax.login') }}" method="POST" class="space-y-4">
            @csrf
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-700">{{ app()->getLocale() === 'ar' ? 'البريد الإلكتروني' : 'Email Address' }}</label>
                <input type="email" name="email" required placeholder="student@eliteacademy.edu.eg" class="input-mobile">
            </div>

            <div class="space-y-1.5">
                <div class="flex justify-between items-center">
                    <label class="text-xs font-bold text-slate-700">{{ app()->getLocale() === 'ar' ? 'كلمة المرور' : 'Password' }}</label>
                    <a href="#" class="text-xs text-teal-600 hover:underline font-bold">{{ app()->getLocale() === 'ar' ? 'نسيت كلمة المرور؟' : 'Forgot Password?' }}</a>
                </div>
                <input type="password" name="password" required placeholder="••••••••" class="input-mobile">
            </div>

            <div class="flex items-center justify-between text-xs pt-1">
                <label class="inline-flex items-center gap-2 cursor-pointer text-slate-600 font-medium">
                    <input type="checkbox" name="remember" value="1" class="rounded border-slate-300 text-teal-600 focus:ring-teal-500">
                    <span>{{ app()->getLocale() === 'ar' ? 'تذكرني على هذا الجهاز' : 'Remember me' }}</span>
                </label>
            </div>

            <button type="submit" id="submitBtn" class="w-full btn-mobile-lg btn-lift text-white bg-teal-600 hover:bg-teal-700 shadow-md shadow-teal-600/20 touch-press mt-2 flex items-center justify-center gap-2 font-bold py-3.5 rounded-2xl">
                <span>{{ app()->getLocale() === 'ar' ? 'تسجيل الدخول' : 'Sign In to Portal' }}</span>
                <span class="arrow-icon">&rarr;</span>
            </button>
        </form>

        {{-- Separate Register Link --}}
        <div class="pt-6 border-t border-slate-100 text-center">
            <p class="text-xs text-slate-500 font-mono">
                {{ app()->getLocale() === 'ar' ? 'ليس لديك حساب حتى الآن؟' : "Don't have an account yet?" }}
                <a href="{{ route('register') }}" class="font-bold text-teal-600 hover:text-teal-700 hover:underline ml-1">
                    {{ app()->getLocale() === 'ar' ? 'إنشاء حساب جديد ←' : 'Create an Account &rarr;' }}
                </a>
            </p>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const alertBox = document.getElementById('authAlert');
    const form = document.getElementById('signinForm');
    const submitBtn = document.getElementById('submitBtn');
    
    function showNotification(msg, isError = true, title = null) {
        if (window.Toast) {
            if (isError) {
                window.Toast.error(msg, title || (document.documentElement.lang === 'ar' ? 'فشل تسجيل الدخول' : 'Login Failed'));
            } else {
                window.Toast.success(msg, title || (document.documentElement.lang === 'ar' ? 'تم تسجيل الدخول' : 'Login Successful'));
            }
        } else if (alertBox) {
            alertBox.className = `p-3.5 rounded-2xl text-xs font-semibold ${isError ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200'}`;
            alertBox.textContent = msg;
            alertBox.classList.remove('hidden');
        }
    }

    if (form) {
        form.addEventListener('submit', async function (e) {
            e.preventDefault();
            if (alertBox) alertBox.classList.add('hidden');
            
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-75', 'cursor-not-allowed');

            try {
                const formData = new FormData(form);
                const res = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();

                if (!res.ok || !data.success) {
                    let errMsg = data.message || (document.documentElement.lang === 'ar' ? 'البيانات المدخلة غير صحيحة.' : 'Login failed. Please check your credentials.');
                    if (data.errors) {
                        const errs = Object.values(data.errors).flat();
                        if (errs.length > 0) errMsg = errs;
                    }
                    showNotification(errMsg, true);
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
                    return;
                }

                showNotification(data.message || (document.documentElement.lang === 'ar' ? 'تم تسجيل الدخول بنجاح! جاري التوجيه...' : 'Login successful! Redirecting...'), false);
                
                // Smooth role-based redirection delay
                setTimeout(() => {
                    window.location.href = data.redirect_url || '/student-portal';
                }, 900);
            } catch (err) {
                showNotification(document.documentElement.lang === 'ar' ? 'حدث خطأ في الاتصال بالشبكة. يرجى المحاولة لاحقاً.' : 'Network connection error. Please try again.', true);
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
            }
        });
    }
});
</script>
@endsection
