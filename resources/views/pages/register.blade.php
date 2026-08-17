@extends('layouts.app')

@section('content')
<section class="py-12 md:py-16 px-4 sm:px-6 lg:px-8 max-w-md mx-auto w-full my-auto">
    <div class="bg-white rounded-3xl p-8 border border-slate-200/80 shadow-xl space-y-6">
        <div class="text-center space-y-2">
            <h1 class="font-heading font-extrabold text-2xl text-slate-900">Create Student Account</h1>
            <p class="text-xs text-slate-500">Join over 25,000 students on Elite Academy</p>
        </div>

        <form action="{{ route('home') }}" method="GET" class="space-y-4 text-xs">
            <div>
                <label class="block font-semibold text-slate-700 mb-1.5">Full Name</label>
                <input type="text" placeholder="e.g. Sarah Jenkins" required class="input-mobile">
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1.5">Email Address</label>
                <input type="email" placeholder="student@example.com" required class="input-mobile">
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1.5">Password</label>
                <input type="password" placeholder="••••••••" required class="input-mobile">
            </div>

            <button type="submit" class="btn-mobile-lg btn-lift text-white bg-teal-600 hover:bg-teal-700 shadow-md shadow-teal-600/20 touch-press">
                Create Account &rarr;
            </button>
        </form>

        <div class="pt-4 border-t border-slate-100 text-center text-xs text-slate-500">
            Already have an account? <a href="{{ route('login') }}" class="font-semibold text-teal-600 hover:underline">{{ __('navbar.login') }}</a>
        </div>
    </div>
</section>
@endsection
