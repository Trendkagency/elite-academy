@php
    $categoryBg = $categoryBg ?? 'bg-teal-600';
    $instructorBorder = $instructorBorder ?? 'border-teal-500';
    $route = $route ?? route('course-details');
    $isEnrolled = $isEnrolled ?? false;
    $hasFreeDemo = $hasFreeDemo ?? true;
    $isArabic = app()->getLocale() === 'ar';
@endphp

<div class="bg-white rounded-3xl border border-slate-200/80 overflow-hidden shadow-2xs hover-lift flex flex-col justify-between group">
    <div>
        <div class="relative h-48 overflow-hidden bg-slate-100">
            <img src="{{ media_url($image, 'images/course_ai.png') }}" alt="{{ $title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
            <span class="absolute top-4 left-4 {{ $categoryBg }} text-white text-xs font-bold px-3 py-1 rounded-full shadow-xs">{{ $category }}</span>
            @if($isEnrolled)
                <span class="absolute top-4 right-4 bg-teal-600 text-white text-xs font-bold px-3 py-1 rounded-full shadow-xs flex items-center gap-1">
                    <span>✓</span> {{ $isArabic ? 'مشترك' : 'Enrolled' }}
                </span>
            @elseif($hasFreeDemo)
                <span class="absolute top-4 right-4 bg-orange-500 text-white text-xs font-bold px-2.5 py-1 rounded-full shadow-xs flex items-center gap-1">
                    <span>▶</span> {{ $isArabic ? 'حصة مجانية' : 'Free Demo' }}
                </span>
            @else
                <span class="absolute top-4 right-4 bg-rose-700 text-white text-xs font-bold px-2.5 py-1 rounded-full shadow-xs flex items-center gap-1 shadow-rose-700/20">
                    <span>🔒</span> {{ $isArabic ? 'باقة مطلوب' : 'Package Required' }}
                </span>
            @endif
        </div>
        <div class="p-6 space-y-3">
            <div class="flex items-center gap-2">
                <img src="{{ media_url($instructorPhoto, 'images/instructor_portrait.png') }}" alt="{{ $instructor }}" class="w-7 h-7 rounded-full object-cover border {{ $instructorBorder }}">
                <span class="text-xs font-bold text-slate-900">{{ $instructor }}</span>
            </div>
            <h3 class="font-heading font-bold text-xl text-slate-900 group-hover:text-teal-600 transition-colors">
                <a href="{{ $route }}">{{ $title }}</a>
            </h3>
            <p class="text-slate-600 text-xs leading-relaxed line-clamp-2">{{ $description }}</p>
        </div>
    </div>
    <div class="p-6 pt-0 border-t border-slate-100 flex items-center justify-between mt-4">
        <span class="font-mono font-bold text-lg text-slate-900">{{ $price }}</span>
        <div class="flex items-center gap-2">
            @if($isEnrolled)
                <a href="{{ route('student-portal') }}" class="text-xs font-bold text-white bg-teal-600 hover:bg-teal-700 px-4 py-2 rounded-xl transition-colors">
                    {{ $isArabic ? 'الانتقال للبوابة ←' : 'Go to Portal →' }}
                </a>
            @else
                @if($hasFreeDemo)
                    <a href="{{ $route }}#demo" class="text-xs font-semibold text-orange-600 bg-orange-50 hover:bg-orange-100 px-3 py-2 rounded-xl transition-colors border border-orange-200">
                        {{ $isArabic ? 'حصة تجريبية' : 'Free Demo' }}
                    </a>
                @endif
                <a href="{{ $route }}" class="text-xs font-semibold text-white bg-teal-600 hover:bg-teal-700 px-4 py-2 rounded-xl transition-colors">
                    {{ $isArabic ? 'تسجيل بالدورة' : 'Enroll' }}
                </a>
            @endif
        </div>
    </div>
</div>
