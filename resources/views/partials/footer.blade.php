@php
    $locale = app()->getLocale();

    $tagline = \App\Models\SiteSetting::get('footer_tagline_' . $locale, __('footer.tagline'));
    $quickTitle = \App\Models\SiteSetting::get('footer_quick_links_title_' . $locale, __('footer.quick_links'));
    $subjectsTitle = \App\Models\SiteSetting::get('footer_subjects_title_' . $locale, __('footer.subjects'));
    $contactTitle = \App\Models\SiteSetting::get('footer_contact_title_' . $locale, __('footer.contact'));
    
    $address = \App\Models\SiteSetting::get('contact_address_' . $locale, \App\Models\SiteSetting::get('contact_address_en', __('footer.address')));
    $phone = \App\Models\SiteSetting::get('contact_phone', '+20 100 000 0000');
    $email = \App\Models\SiteSetting::get('contact_email', 'info@eliteacademy.edu.eg');
    $hours = \App\Models\SiteSetting::get('footer_working_hours_' . $locale, __('footer.hours'));
    
    $rights = \App\Models\SiteSetting::get('footer_rights_' . $locale, __('footer.rights'));

    // Dynamic Quick Links Repeater
    $quickLinksRaw = \App\Models\SiteSetting::get('footer_quick_links');
    $quickLinks = $quickLinksRaw ? json_decode($quickLinksRaw, true) : [
        ['label_ar' => 'الرئيسية', 'label_en' => 'Home', 'url' => '/'],
        ['label_ar' => 'الأسئلة الشائعة', 'label_en' => 'FAQ & Help', 'url' => '/faq'],
        ['label_ar' => 'من نحن', 'label_en' => 'About Us', 'url' => '/about'],
        ['label_ar' => 'المعلمون', 'label_en' => 'Teachers', 'url' => '/teachers'],
        ['label_ar' => 'المدونة', 'label_en' => 'Blog', 'url' => '/blog'],
        ['label_ar' => 'خريطة الموقع', 'label_en' => 'Sitemap XML', 'url' => '/sitemap.xml'],
        ['label_ar' => 'بوابة الطلاب', 'label_en' => 'Student Portal', 'url' => '/student-portal'],
    ];

    // Dynamic Subjects Links Repeater
    $subjectsLinksRaw = \App\Models\SiteSetting::get('footer_subjects_links');
    $subjectsLinks = $subjectsLinksRaw ? json_decode($subjectsLinksRaw, true) : [
        ['label_ar' => 'البرمجة', 'label_en' => 'Programming', 'url' => '/subjects'],
        ['label_ar' => 'الذكاء الاصطناعي', 'label_en' => 'Artificial Intelligence', 'url' => '/subjects'],
        ['label_ar' => 'العلوم والفيزياء', 'label_en' => 'Science & Physics', 'url' => '/subjects'],
        ['label_ar' => 'إدارة الأعمال', 'label_en' => 'Business Administration', 'url' => '/subjects'],
        ['label_ar' => 'التصميم الإبداعي', 'label_en' => 'Creative Design', 'url' => '/subjects'],
        ['label_ar' => 'الرياضيات', 'label_en' => 'Mathematics', 'url' => '/subjects'],
    ];
@endphp

<footer class="bg-slate-950 text-slate-300 pt-16 pb-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-8 sm:gap-10 text-center rtl:sm:text-right ltr:sm:text-left">
            
            {{-- Col 1: Brand & Tagline --}}
            <div class="sm:col-span-2 space-y-4 flex flex-col items-center rtl:sm:items-start ltr:sm:items-start">
<<<<<<< HEAD
                <a href="{{ route('home') }}" class="inline-block">
                    <img src="{{ asset('images/logo.webp') }}" alt="Elite Academy Logo" class="h-20 sm:h-24 lg:h-28 w-auto max-h-28 object-contain mx-auto sm:mx-0">
=======
                <a href="{{ route('home') }}" class="inline-block" aria-label="Elite Academy Homepage">
                    <img src="{{ asset('images/logo_500.webp') }}" alt="Elite Academy Logo" width="249" height="56" class="h-20 sm:h-24 lg:h-28 w-auto max-h-28 object-contain mx-auto sm:mx-0" loading="lazy">
>>>>>>> f41ff34d4a05c9f714f7c0c0a30c0717447e9f57
                </a>
                <p class="text-sm text-slate-300 leading-relaxed max-w-sm text-center rtl:sm:text-right ltr:sm:text-left">
                    {{ $tagline }}
                </p>
            </div>

            {{-- Col 2: Dynamic Quick Links --}}
            <div class="space-y-3">
                <h3 class="font-heading font-extrabold text-sm text-white uppercase tracking-wider">{{ $quickTitle }}</h3>
                <ul class="space-y-2 text-sm text-slate-300">
                    @foreach($quickLinks as $link)
                        @php
                            $label = ($locale === 'ar' ? ($link['label_ar'] ?? null) : null) ?: ($link['label_en'] ?? '');
                            $url = $link['url'] ?? '#';
                            if (str_starts_with($url, '/')) {
                                $url = url($url);
                            }
                        @endphp
                        <li>
                            <a href="{{ $url }}" class="hover:text-teal-400 transition-colors link-underline">{{ $label }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Col 3: Dynamic Subjects / Services Links --}}
            <div class="space-y-3">
                <h3 class="font-heading font-extrabold text-sm text-white uppercase tracking-wider">{{ $subjectsTitle }}</h3>
                <ul class="space-y-2 text-sm text-slate-300">
                    @foreach($subjectsLinks as $link)
                        @php
                            $label = ($locale === 'ar' ? ($link['label_ar'] ?? null) : null) ?: ($link['label_en'] ?? '');
                            $url = $link['url'] ?? '#';
                            if (str_starts_with($url, '/')) {
                                $url = url($url);
                            }
                        @endphp
                        <li>
                            <a href="{{ $url }}" class="hover:text-teal-400 transition-colors link-underline">{{ $label }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Col 4: Dynamic Contact Information --}}
            <div class="space-y-3">
                <h3 class="font-heading font-extrabold text-sm text-white uppercase tracking-wider">{{ $contactTitle }}</h3>
                <ul class="space-y-2 text-sm text-slate-300">
                    <li class="flex items-center justify-center rtl:sm:justify-start ltr:sm:justify-start gap-2">📍 <span>{{ $address }}</span></li>
                    <li class="flex items-center justify-center rtl:sm:justify-start ltr:sm:justify-start gap-2">📞 <span>{{ $phone }}</span></li>
                    <li class="flex items-center justify-center rtl:sm:justify-start ltr:sm:justify-start gap-2">✉️ <span>{{ $email }}</span></li>
                    <li class="flex items-center justify-center rtl:sm:justify-start ltr:sm:justify-start gap-2">🕒 <span>{{ $hours }}</span></li>
                </ul>
            </div>
        </div>

        {{-- Bottom Footer: Social Links & Copyright Notice --}}
        <div class="pt-8 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-6 text-xs text-slate-400">
            <div class="flex items-center gap-4">
                <a href="{{ \App\Models\SiteSetting::get('social_facebook', '#') }}" target="_blank" rel="noopener noreferrer" class="social-icon w-9 h-9 rounded-full bg-slate-800 hover:bg-teal-600 text-white flex items-center justify-center font-bold text-sm transition-all duration-300" aria-label="Follow Elite Academy on Facebook">f</a>
                <a href="{{ \App\Models\SiteSetting::get('social_twitter', '#') }}" target="_blank" rel="noopener noreferrer" class="social-icon w-9 h-9 rounded-full bg-slate-800 hover:bg-teal-600 text-white flex items-center justify-center font-bold text-sm transition-all duration-300" aria-label="Follow Elite Academy on Twitter">𝕏</a>
                <a href="{{ \App\Models\SiteSetting::get('social_instagram', '#') }}" target="_blank" rel="noopener noreferrer" class="social-icon w-9 h-9 rounded-full bg-slate-800 hover:bg-teal-600 text-white flex items-center justify-center font-bold text-sm transition-all duration-300" aria-label="Follow Elite Academy on Instagram">ig</a>
                <a href="{{ \App\Models\SiteSetting::get('social_linkedin', '#') }}" target="_blank" rel="noopener noreferrer" class="social-icon w-9 h-9 rounded-full bg-slate-800 hover:bg-teal-600 text-white flex items-center justify-center font-bold text-sm transition-all duration-300" aria-label="Connect with Elite Academy on LinkedIn">in</a>
                <a href="{{ \App\Models\SiteSetting::get('social_youtube', '#') }}" target="_blank" rel="noopener noreferrer" class="social-icon w-9 h-9 rounded-full bg-slate-800 hover:bg-teal-600 text-white flex items-center justify-center font-bold text-sm transition-all duration-300" aria-label="Subscribe to Elite Academy on YouTube">yt</a>
            </div>
            <p>{{ $rights }}</p>
        </div>
    </div>
</footer>

