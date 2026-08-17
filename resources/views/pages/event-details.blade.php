@extends('layouts.app')

@section('content')
<section class="py-12 bg-slate-900 text-white border-b border-slate-800 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6 relative z-10">
        @include('components.breadcrumb', [
            'items' => [
                ['label' => __('navbar.home'), 'route' => 'home'],
                ['label' => __('navbar.events'), 'route' => 'events'],
                ['label' => 'Computer Vision Lab'],
            ]
        ])

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            <div class="lg:col-span-8 space-y-4">
                <div class="flex flex-wrap items-center gap-3">
                    <span class="px-3.5 py-1 rounded-full bg-orange-500 text-white text-xs font-mono font-bold">
                        Live Workshop
                    </span>
                    <span class="px-3.5 py-1 rounded-full bg-teal-800/90 text-teal-200 text-xs font-mono font-semibold border border-teal-500/30">
                        Sat, Oct 12 • 10:00 AM EST
                    </span>
                </div>

                <h1 class="font-heading text-3xl sm:text-5xl font-extrabold text-white tracking-tight">
                    Applied Computer Vision & PyTorch Workshop
                </h1>

                <p class="text-slate-300 text-base leading-relaxed max-w-2xl">
                    A hands-on 3-hour intensive lab building real-time object tracking algorithms and neural networks with Marcus Vance.
                </p>

                <div class="flex flex-wrap items-center gap-6 pt-2 text-xs font-mono text-slate-300">
                    <span>📍 Location: Main Innovation Lab & Zoom Live</span>
                    <span>🎟️ Capacity: 50 Students</span>
                    <span>🔥 Seats Left: 12 Remaining</span>
                </div>
            </div>

            <div class="lg:col-span-4">
                <div class="bg-white rounded-3xl p-6 text-slate-900 border border-slate-200/80 shadow-2xl space-y-4">
                    <div class="space-y-1">
                        <span class="text-xs font-mono uppercase tracking-wider text-slate-400">Registration Fee</span>
                        <p class="font-mono text-3xl font-extrabold text-slate-900">FREE <span class="text-xs text-teal-600 font-normal">(Verified Students)</span></p>
                    </div>

                    <a href="#register" class="btn-lift w-full block text-center py-3.5 px-6 font-semibold text-white bg-orange-500 hover:bg-orange-600 rounded-xl shadow-md">
                        Reserve Seat Now
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Content Body --}}
<section class="py-12 md:py-16 bg-[#FAFAF9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
            <div class="lg:col-span-8 space-y-12">
                <div class="bg-white rounded-3xl p-8 border border-slate-200/80 shadow-2xs space-y-6">
                    <h2 class="font-heading font-bold text-2xl text-slate-900">Workshop Overview</h2>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        Computer vision is driving key innovations in autonomous vehicles, medical diagnostics, and spatial computing. In this workshop, students will construct real-time video analytics pipelines using OpenCV and PyTorch.
                    </p>
                </div>

                {{-- Agenda --}}
                <div class="bg-white rounded-3xl p-8 border border-slate-200/80 shadow-2xs space-y-6">
                    <h2 class="font-heading font-bold text-2xl text-slate-900">Interactive Agenda Schedule</h2>
                    <div class="space-y-4">
                        <div class="p-4 rounded-2xl bg-[#FAFAF9] border border-slate-200/80 space-y-1">
                            <span class="text-xs font-mono font-bold text-teal-600">10:00 AM - 11:00 AM</span>
                            <h3 class="font-heading font-bold text-base text-slate-900">Convolutional Filters & Edge Detection</h3>
                            <p class="text-xs text-slate-600">Understanding matrix transformations, Sobel filters, and spatial convolutions.</p>
                        </div>
                        <div class="p-4 rounded-2xl bg-[#FAFAF9] border border-slate-200/80 space-y-1">
                            <span class="text-xs font-mono font-bold text-teal-600">11:00 AM - 12:30 PM</span>
                            <h3 class="font-heading font-bold text-base text-slate-900">Live PyTorch Neural Object Tracking</h3>
                            <p class="text-xs text-slate-600">Implementing pretrained YOLO models and custom bounding box classification.</p>
                        </div>
                    </div>
                </div>

                {{-- Keynote Leaders --}}
                <div class="bg-white rounded-3xl p-8 border border-slate-200/80 shadow-2xs space-y-4">
                    <h2 class="font-heading font-bold text-2xl text-slate-900">Workshop Keynote Leaders</h2>
                    <div class="flex items-center gap-4 pt-2">
                        <img src="{{ asset('images/instructor_male.png') }}" alt="Marcus Vance" class="w-16 h-16 rounded-2xl object-cover border-2 border-purple-500">
                        <div>
                            <h3 class="font-heading font-bold text-lg text-slate-900">Marcus Vance</h3>
                            <p class="text-xs font-semibold text-purple-600">AI Research Lead • Neural Networks Chair</p>
                            <p class="text-xs text-slate-500 mt-1">10+ years in computer vision research and deep learning models.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Reservation Form --}}
            <div class="lg:col-span-4 space-y-6">
                <div id="register" class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-lg sticky top-24 space-y-6">
                    <div class="space-y-2">
                        <h3 class="font-heading font-bold text-xl text-slate-900">Reserve Your Seat</h3>
                        <p class="text-xs text-slate-500">Fill in your details to receive access credentials.</p>
                    </div>

                    <form action="{{ route('events') }}" method="GET" class="space-y-4 text-xs">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Full Name</label>
                            <input type="text" placeholder="e.g. Alex Johnson" required class="input-mobile">
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Email Address</label>
                            <input type="email" placeholder="alex@example.com" required class="input-mobile">
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Attendance Mode</label>
                            <select class="input-mobile cursor-pointer">
                                <option>In-Person (Campus Lab)</option>
                                <option>Live Zoom Stream</option>
                            </select>
                        </div>

                        <button type="submit" class="btn-lift w-full py-3.5 px-4 text-sm font-semibold text-white bg-teal-600 hover:bg-teal-700 rounded-xl shadow-md">
                            Confirm Seat Registration
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
