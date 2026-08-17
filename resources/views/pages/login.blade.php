@extends('layouts.app')

@section('content')
<section class="py-12 md:py-16 px-4">
    <div class="max-w-md mx-auto bg-white rounded-3xl p-8 border border-slate-200/80 shadow-lg space-y-6">
        <div class="text-center space-y-2">
            <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center text-xl font-bold mx-auto">🔑</div>
            <h1 class="font-heading font-extrabold text-2xl text-slate-900">Elite Academy Portal</h1>
            <p class="text-xs text-slate-500">Access your courses, grades, and parent dashboard.</p>
        </div>

        {{-- Error / Alert Container --}}
        <div id="authAlert" class="hidden p-3.5 rounded-2xl text-xs font-semibold"></div>

        {{-- Pure CSS Radio Tabs --}}
        <input type="radio" id="tab-signin" name="auth-tabs" class="peer/signin hidden" checked>
        <input type="radio" id="tab-register" name="auth-tabs" class="peer/register hidden">

        <div class="flex border-b border-slate-200 text-xs font-bold text-slate-500">
            <label for="tab-signin" class="w-1/2 text-center py-3 cursor-pointer peer-checked/signin:border-b-2 peer-checked/signin:border-teal-600 peer-checked/signin:text-teal-600">
                Sign In
            </label>
            <label for="tab-register" class="w-1/2 text-center py-3 cursor-pointer peer-checked/register:border-b-2 peer-checked/register:border-teal-600 peer-checked/register:text-teal-600">
                Create Account
            </label>
        </div>

        {{-- Sign In Content --}}
        <div class="tab-content content-signin space-y-4">
            <form id="signinForm" action="{{ route('ajax.login') }}" method="POST" class="space-y-4">
                @csrf
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-700">Email Address</label>
                    <input type="email" name="email" required placeholder="student@eliteacademy.edu" class="input-mobile">
                </div>

                <div class="space-y-1.5">
                    <div class="flex justify-between items-center">
                        <label class="text-xs font-bold text-slate-700">Password</label>
                        <a href="#" class="text-xs text-teal-600 hover:underline font-bold">Forgot?</a>
                    </div>
                    <input type="password" name="password" required placeholder="••••••••" class="input-mobile">
                </div>

                <button type="submit" class="btn-mobile-lg btn-lift text-white bg-teal-600 hover:bg-teal-700 shadow-md shadow-teal-600/20 touch-press">
                    Sign In to Portal
                </button>
            </form>
        </div>

        {{-- Register Content --}}
        <div class="tab-content content-register space-y-4">
            <form id="registerForm" action="{{ route('ajax.register') }}" method="POST" class="space-y-4">
                @csrf
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-700">Full Name</label>
                    <input type="text" name="name" required placeholder="e.g. David Kovacs" class="input-mobile">
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-700">Email Address</label>
                    <input type="email" name="email" required placeholder="name@example.com" class="input-mobile">
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-700">Account Type</label>
                    <select name="user_type" class="input-mobile cursor-pointer">
                        <option value="student">Student</option>
                        <option value="parent">Parent</option>
                        <option value="teacher">Instructor</option>
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-700">Password</label>
                    <input type="password" name="password" required placeholder="••••••••" class="input-mobile">
                </div>

                <button type="submit" class="btn-mobile-lg btn-lift text-white bg-teal-600 hover:bg-teal-700 shadow-md shadow-teal-600/20 touch-press">
                    Create Account &rarr;
                </button>
            </form>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const alertBox = document.getElementById('authAlert');
    
    function showAlert(msg, isError = true) {
        alertBox.className = `p-3.5 rounded-2xl text-xs font-semibold ${isError ? 'bg-red-50 text-red-600 border border-red-200' : 'bg-emerald-50 text-emerald-600 border border-emerald-200'}`;
        alertBox.textContent = msg;
        alertBox.classList.remove('hidden');
    }

    function handleAuthSubmit(form) {
        form.addEventListener('submit', async function (e) {
            e.preventDefault();
            alertBox.classList.add('hidden');
            const formData = new FormData(form);

            try {
                const res = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();

                if (res.ok && data.success) {
                    showAlert('Success! Redirecting...', false);
                    setTimeout(() => window.location.href = data.redirect_url || '{{ route("student-portal") }}', 800);
                } else {
                    showAlert(data.message || 'An error occurred during authentication.');
                }
            } catch (err) {
                showAlert('Network connection error. Please try again.');
            }
        });
    }

    const signinForm = document.getElementById('signinForm');
    const registerForm = document.getElementById('registerForm');
    if (signinForm) handleAuthSubmit(signinForm);
    if (registerForm) handleAuthSubmit(registerForm);
});
</script>
@endsection
