@extends('layouts.app')

@section('content')
<section class="py-12 md:py-20 px-4 bg-[#FAFAF9]">
    <div class="max-w-md mx-auto bg-white rounded-3xl p-8 sm:p-10 border border-slate-200/90 shadow-xl space-y-6">
        <div class="text-center space-y-2">
            <div class="w-14 h-14 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center text-2xl font-bold mx-auto border border-teal-100">🎓</div>
            <h1 class="font-heading font-black text-2xl sm:text-3xl text-slate-900 tracking-tight">
                {{ app()->getLocale() === 'ar' ? 'إنشاء حساب جديد' : 'Create an Account' }}
            </h1>
            <p class="text-xs text-slate-500 font-mono">
                {{ app()->getLocale() === 'ar' ? 'انضم إلى أكاديمية النخبة وابدأ رحلة التعلم التفاعلي.' : 'Join Elite Academy and start interactive learning.' }}
            </p>
        </div>

        {{-- Error / Success Alert Container --}}
        <div id="authAlert" class="hidden p-3.5 rounded-2xl text-xs font-semibold"></div>

        {{-- Dedicated Registration Form --}}
        <form id="registerForm" action="{{ route('ajax.register') }}" method="POST" class="space-y-4">
            @csrf
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-700">{{ app()->getLocale() === 'ar' ? 'الاسم بالكامل' : 'Full Name' }}</label>
                <input type="text" name="name" required placeholder="{{ app()->getLocale() === 'ar' ? 'مثل: أحمد محمود' : 'e.g. David Kovacs' }}" class="input-mobile">
            </div>

            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-700">{{ app()->getLocale() === 'ar' ? 'البريد الإلكتروني' : 'Email Address' }}</label>
                <input type="email" name="email" required placeholder="name@example.com" class="input-mobile">
            </div>

            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-700">{{ app()->getLocale() === 'ar' ? 'نوع الحساب' : 'Account Type' }}</label>
                <select name="user_type" class="input-mobile cursor-pointer" onchange="document.getElementById('studentGradeGroup').style.display = this.value === 'student' ? 'block' : 'none'">
                    <option value="student">{{ app()->getLocale() === 'ar' ? 'طالب' : 'Student' }}</option>
                    <option value="parent">{{ app()->getLocale() === 'ar' ? 'ولي أمر' : 'Parent' }}</option>
                    <option value="teacher">{{ app()->getLocale() === 'ar' ? 'معلم / محاضر' : 'Instructor' }}</option>
                </select>
            </div>

            <div id="studentGradeGroup" class="space-y-1.5">
                <label class="text-xs font-bold text-slate-700">{{ app()->getLocale() === 'ar' ? 'المدرسة' : 'School Name' }}</label>
                <input type="text" name="school_name" placeholder="{{ app()->getLocale() === 'ar' ? 'اسم المدرسة (اختياري)' : 'School Name (Optional)' }}" class="input-mobile">
            </div>

            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-700">{{ app()->getLocale() === 'ar' ? 'كلمة المرور' : 'Password' }}</label>
                <input type="password" name="password" required placeholder="••••••••" class="input-mobile">
            </div>

            <button type="submit" class="btn-mobile-lg btn-lift text-white bg-teal-600 hover:bg-teal-700 shadow-md shadow-teal-600/20 touch-press mt-2">
                {{ app()->getLocale() === 'ar' ? 'إنشاء الحساب والانضمام' : 'Create Account & Start' }} &rarr;
            </button>
        </form>

        {{-- Login Redirection Link --}}
        <div class="pt-6 border-t border-slate-100 text-center">
            <p class="text-xs text-slate-500 font-mono">
                {{ app()->getLocale() === 'ar' ? 'لديك حساب بالفعل؟' : 'Already have an account?' }}
                <a href="{{ route('login') }}" class="font-bold text-teal-600 hover:text-teal-700 hover:underline ml-1">
                    {{ app()->getLocale() === 'ar' ? 'تسجيل الدخول ←' : 'Log In to Portal &rarr;' }}
                </a>
            </p>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const alertBox = document.getElementById('authAlert');
    const form = document.getElementById('registerForm');
    
    function showAlert(msg, isError = true) {
        alertBox.className = `p-3.5 rounded-2xl text-xs font-semibold ${isError ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200'}`;
        alertBox.textContent = msg;
        alertBox.classList.remove('hidden');
    }

    if (form) {
        form.addEventListener('submit', async function (e) {
            e.preventDefault();
            alertBox.classList.add('hidden');
            const submitBtn = form.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-75');

            try {
                const formData = new FormData(form);
                const res = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();

                if (!res.ok || !data.success) {
                    showAlert(data.message || 'Registration failed. Please check input fields.', true);
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-75');
                    return;
                }

                showAlert(data.message || 'Account created successfully! Redirecting...', false);
                setTimeout(() => window.location.href = data.redirect_url || '/student-portal', 800);
            } catch (err) {
                showAlert('Network error. Please try again.', true);
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-75');
            }
        });
    }
});
</script>
@endsection
