@extends('layouts.app')

@section('content')
<section class="py-12 md:py-16 px-4">
    <div class="max-w-md mx-auto bg-white rounded-3xl p-8 border border-slate-200/80 shadow-lg space-y-6">
        <div class="text-center space-y-2">
            <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center text-xl font-bold mx-auto">🔑</div>
            <h1 class="font-heading font-extrabold text-2xl text-slate-900">Elite Academy Portal</h1>
            <p class="text-xs text-slate-500">Access your courses, grades, and parent dashboard.</p>
        </div>

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
            <form action="{{ route('student-portal') }}" method="GET" class="space-y-4">
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-700">Email Address</label>
                    <input type="email" required placeholder="student@eliteacademy.edu" class="input-mobile">
                </div>

                <div class="space-y-1.5">
                    <div class="flex justify-between items-center">
                        <label class="text-xs font-bold text-slate-700">Password</label>
                        <a href="#" class="text-xs text-teal-600 hover:underline font-bold">Forgot?</a>
                    </div>
                    <input type="password" required placeholder="••••••••" class="input-mobile">
                </div>

                <button type="submit" class="btn-mobile-lg btn-lift text-white bg-teal-600 hover:bg-teal-700 shadow-md shadow-teal-600/20 touch-press">
                    Sign In to Portal
                </button>
            </form>
        </div>

        {{-- Register Content --}}
        <div class="tab-content content-register space-y-4">
            <form action="#" method="GET" class="space-y-4">
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-700">Full Name</label>
                    <input type="text" required placeholder="e.g. David Kovacs" class="input-mobile">
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-700">Email Address</label>
                    <input type="email" required placeholder="name@example.com" class="input-mobile">
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-700">Account Type</label>
                    <select class="input-mobile cursor-pointer">
                        <option value="student">Student</option>
                        <option value="parent">Parent</option>
                        <option value="teacher">Instructor</option>
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-700">Password</label>
                    <input type="password" required placeholder="••••••••" class="input-mobile">
                </div>

                <button type="submit" class="btn-mobile-lg btn-lift text-white bg-teal-600 hover:bg-teal-700 shadow-md shadow-teal-600/20 touch-press">
                    Create Account &rarr;
                </button>
            </form>
        </div>
    </div>
</section>
@endsection
