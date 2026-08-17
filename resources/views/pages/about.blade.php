@extends('layouts.app')

@section('content')
<section class="relative py-16 md:py-24 bg-white border-b border-slate-200/80 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @include('components.breadcrumb', [
            'items' => [
                ['label' => __('navbar.home'), 'route' => 'home'],
                ['label' => 'About Elite Academy'],
            ]
        ])

        {{-- Storytelling Editorial Hero Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            <div class="lg:col-span-7 space-y-6">
                <span class="inline-block text-xs font-mono uppercase tracking-widest text-teal-600 font-extrabold bg-teal-50 px-3.5 py-1.5 rounded-full border border-teal-200/80">
                    OUR STORY & HERITAGE
                </span>

                <h1 class="font-heading text-4xl sm:text-5xl lg:text-6xl font-black text-slate-900 tracking-tight leading-tight">
                    Redefining School Education For <span class="text-teal-600 underline decoration-orange-500 underline-offset-8">Future Leaders</span>
                </h1>

                <p class="text-slate-600 text-base sm:text-lg font-medium leading-relaxed">
                    Founded in 2012, Elite Academy empowers K-12 school students across Egypt through practical learning, PhD mentorship, and STEM innovation labs.
                </p>

                <div class="grid grid-cols-3 gap-4 pt-4 border-t border-slate-100 text-center">
                    <div class="bg-[#FAFAF9] p-4 rounded-2xl border border-slate-200/80">
                        <span class="font-heading font-black text-3xl text-teal-600 block">14+</span>
                        <span class="text-[11px] font-mono text-slate-500 font-bold">Years Experience</span>
                    </div>
                    <div class="bg-[#FAFAF9] p-4 rounded-2xl border border-slate-200/80">
                        <span class="font-heading font-black text-3xl text-orange-600 block">25,000+</span>
                        <span class="text-[11px] font-mono text-slate-500 font-bold">Active Students</span>
                    </div>
                    <div class="bg-[#FAFAF9] p-4 rounded-2xl border border-slate-200/80">
                        <span class="font-heading font-black text-3xl text-teal-600 block">150+</span>
                        <span class="text-[11px] font-mono text-slate-500 font-bold">Expert Faculty</span>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-5 relative">
                <div class="rounded-3xl overflow-hidden shadow-2xl border-4 border-white h-[420px] bg-slate-950">
                    <img src="{{ asset('images/academy_campus.png') }}" alt="Elite Academy Campus" class="w-full h-full object-cover">
                </div>
                <div class="absolute -bottom-6 -left-6 bg-slate-900 text-white p-5 rounded-2xl border border-slate-800 shadow-2xl">
                    <p class="font-heading font-black text-xl text-teal-400">EST. 2012</p>
                    <p class="text-xs font-mono text-slate-300">New Cairo STEM Campus</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Mission & Vision --}}
<section class="py-20 md:py-28 bg-[#FAFAF9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-16">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-white rounded-3xl p-8 sm:p-10 border border-slate-200/90 shadow-xl space-y-4 card-lift">
                <span class="text-xs font-mono font-extrabold text-teal-600 bg-teal-50 px-3.5 py-1.5 rounded-full border border-teal-200/80">
                    OUR MISSION
                </span>
                <h2 class="font-heading text-2xl sm:text-3xl font-black text-slate-900">Empowering Every School Student</h2>
                <p class="text-slate-600 text-base leading-relaxed">
                    We empower students across Egypt with deep conceptual understanding rather than rote memorization. Through structured video tracks, live labs, and 1-on-1 mentorship, we bridge the gap between academic theory and real-world mastery.
                </p>
            </div>

            <div class="bg-white rounded-3xl p-8 sm:p-10 border border-slate-200/90 shadow-xl space-y-4 card-lift">
                <span class="text-xs font-mono font-extrabold text-orange-600 bg-orange-50 px-3.5 py-1.5 rounded-full border border-orange-200/80">
                    OUR VISION
                </span>
                <h2 class="font-heading text-2xl sm:text-3xl font-black text-slate-900">Leading Digital Learning in MENA</h2>
                <p class="text-slate-600 text-base leading-relaxed">
                    To be the premier secondary education platform in the Middle East, recognized for academic excellence, STEM innovation, and empowering students to gain admission to global top-tier universities.
                </p>
            </div>
        </div>
    </div>
</section>
@endsection
