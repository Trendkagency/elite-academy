@extends('layouts.app')

@section('content')
<section class="relative py-16 md:py-24 bg-white border-b border-slate-200/80 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @include('components.breadcrumb', [
            'items' => [
                ['label' => __('navbar.home'), 'route' => 'home'],
                ['label' => __('contact.page_badge')],
            ]
        ])

        {{-- Premium Split Hero Layout --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            <div class="lg:col-span-6 space-y-6">
                <span class="inline-block text-xs font-mono uppercase tracking-widest text-teal-600 font-extrabold bg-teal-50 px-3.5 py-1.5 rounded-full border border-teal-200/80">
                    STUDENT & PARENT SUPPORT
                </span>

                <h1 class="font-heading text-4xl sm:text-5xl font-black text-slate-900 tracking-tight leading-tight">
                    We Are Always <span class="text-teal-600 underline decoration-orange-500 underline-offset-8">Here To Help</span>
                </h1>

                <p class="text-slate-600 text-base font-medium leading-relaxed">
                    Have questions regarding curriculum enrollment, parent progress dashboards, or scheduling a campus visit? Reach out to our dedicated support advisors.
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <div class="bg-[#FAFAF9] p-4 rounded-2xl border border-slate-200/80 space-y-1">
                        <span class="text-xs font-mono font-bold text-teal-600 uppercase">Phone Support</span>
                        <p class="font-extrabold text-slate-900 text-sm">+20 100 123 4567</p>
                    </div>

                    <div class="bg-[#FAFAF9] p-4 rounded-2xl border border-slate-200/80 space-y-1">
                        <span class="text-xs font-mono font-bold text-teal-600 uppercase">WhatsApp Help</span>
                        <p class="font-extrabold text-slate-900 text-sm">+20 100 123 4568</p>
                    </div>

                    <div class="bg-[#FAFAF9] p-4 rounded-2xl border border-slate-200/80 space-y-1">
                        <span class="text-xs font-mono font-bold text-teal-600 uppercase">Support Email</span>
                        <p class="font-extrabold text-slate-900 text-sm truncate">support@elite-academy.edu.eg</p>
                    </div>

                    <div class="bg-[#FAFAF9] p-4 rounded-2xl border border-slate-200/80 space-y-1">
                        <span class="text-xs font-mono font-bold text-teal-600 uppercase">Campus Location</span>
                        <p class="font-extrabold text-slate-900 text-sm truncate">New Cairo Hub, Egypt</p>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-6 relative">
                <div class="rounded-3xl overflow-hidden shadow-2xl border-4 border-white h-[420px] bg-slate-950">
                    <img src="{{ asset('images/academy_campus.png') }}" alt="Campus Support Desk" class="w-full h-full object-cover">
                </div>
                <div class="absolute -bottom-6 -left-6 bg-teal-600 text-white p-5 rounded-2xl shadow-2xl flex items-center gap-3">
                    <span class="text-3xl">🎧</span>
                    <div>
                        <p class="font-heading font-black text-lg">24/7 Parent Support</p>
                        <p class="text-xs font-mono text-teal-100">Live Chat & Direct Consultation</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Contact Form & Map --}}
<section class="py-20 md:py-28 bg-[#FAFAF9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-2 gap-12">
        <div class="bg-white rounded-3xl overflow-hidden border border-slate-200/90 shadow-xl h-96 relative">
            <iframe title="Campus Location Map" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d55251.33660578643!2d31.470000000000002!3d30.030000000000005!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x145822ee00000001%3A0x1000000000000000!2sNew%20Cairo%2C%20Cairo%20Governorate%2C%20Egypt!5e0!3m2!1sen!2seg!4v1700000000000!5m2!1sen!2seg" class="w-full h-full border-0" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>

        <div class="bg-white rounded-3xl p-8 sm:p-10 border border-slate-200/90 shadow-xl space-y-6">
            <div class="space-y-2">
                <h2 class="font-heading text-2xl sm:text-3xl font-black text-slate-900">Send Us a Message</h2>
                <p class="text-xs font-mono text-slate-500">Our student advisors will respond within 24 hours.</p>
            </div>

            <form action="#" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('contact.label_name') }}</label>
                    <input type="text" placeholder="e.g. Ahmed Ali" required class="input-mobile">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">Phone Number</label>
                        <input type="tel" placeholder="+20 100..." required class="input-mobile">
                    </div>
                    <div>
                        <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">I Am A...</label>
                        <select class="input-mobile cursor-pointer">
                            <option value="student">Student</option>
                            <option value="parent">Parent</option>
                            <option value="teacher">Teacher applicant</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">Subject of Inquiry</label>
                    <input type="text" placeholder="e.g. Grade 10 Mathematics Enrollment" class="input-mobile">
                </div>

                <div>
                    <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('contact.label_message') }}</label>
                    <textarea rows="4" placeholder="How can we help you?" required class="w-full bg-white border border-slate-200 rounded-xl p-4 text-[16px] font-medium focus:outline-none focus:border-teal-600 focus:ring-2 focus:ring-teal-500/20"></textarea>
                </div>

                <button type="submit" class="btn-mobile-lg btn-lift bg-teal-600 hover:bg-teal-700 text-white font-extrabold shadow-lg shadow-teal-600/20 cursor-pointer touch-press">
                    Submit Inquiry &rarr;
                </button>
            </form>
        </div>
    </div>
</section>
@endsection
