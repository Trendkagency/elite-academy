@php
    $locale = app()->getLocale();

    $tagline = \App\Models\SiteSetting::get('footer_tagline_' . $locale, __('footer.tagline'));
    $quickTitle = \App\Models\SiteSetting::get('footer_quick_links_title_' . $locale, __('footer.quick_links'));
    $subjectsTitle = \App\Models\SiteSetting::get('footer_subjects_title_' . $locale, __('footer.subjects'));
    $contactTitle = \App\Models\SiteSetting::get('footer_contact_title_' . $locale, __('footer.contact'));

    $address = \App\Models\SiteSetting::get(
        'contact_address_' . $locale,
        \App\Models\SiteSetting::get('contact_address_en', __('footer.address')),
    );
    $phone = \App\Models\SiteSetting::get('contact_phone', '+20 100 000 0000');
    $email = \App\Models\SiteSetting::get('contact_email', 'info@eliteacademy.edu.eg');
    $hours = \App\Models\SiteSetting::get('footer_working_hours_' . $locale, __('footer.hours'));

    $rights = \App\Models\SiteSetting::get('footer_rights_' . $locale, __('footer.rights'));

    // Dynamic Quick Links Repeater
    $quickLinksRaw = \App\Models\SiteSetting::get('footer_quick_links');
    $quickLinks = $quickLinksRaw
        ? json_decode($quickLinksRaw, true)
        : [
            ['label_ar' => 'الرئيسية', 'label_en' => 'Home', 'url' => '/'],
            ['label_ar' => 'التقييمات والآراء', 'label_en' => 'Reviews & Ratings', 'url' => '/reviews'],
            ['label_ar' => 'الأسئلة الشائعة', 'label_en' => 'FAQ & Help', 'url' => '/faq'],
            ['label_ar' => 'من نحن', 'label_en' => 'About Us', 'url' => '/about'],
            ['label_ar' => 'المعلمون', 'label_en' => 'Teachers', 'url' => '/teachers'],
            ['label_ar' => 'المدونة', 'label_en' => 'Blog', 'url' => '/blog'],
            ['label_ar' => 'خريطة الموقع', 'label_en' => 'Sitemap XML', 'url' => '/sitemap.xml'],
            ['label_ar' => 'بوابة الطلاب', 'label_en' => 'Student Portal', 'url' => '/student-portal'],
        ];

    // Dynamic Subjects Links Repeater
    $subjectsLinksRaw = \App\Models\SiteSetting::get('footer_subjects_links');
    $subjectsLinks = $subjectsLinksRaw
        ? json_decode($subjectsLinksRaw, true)
        : [
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
        <div
            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-8 sm:gap-10 text-center rtl:sm:text-right ltr:sm:text-left">

            {{-- Col 1: Brand & Tagline --}}
            <div class="sm:col-span-2 space-y-4 flex flex-col items-center rtl:sm:items-start ltr:sm:items-start">
                <a href="{{ route('home') }}" class="inline-block" aria-label="Elite Academy Homepage">
                    <img src="{{ asset('images/logo_500.webp') }}" alt="Elite Academy Logo" width="249" height="56"
                        class="h-20 sm:h-24 lg:h-28 w-auto max-h-28 object-contain mx-auto sm:mx-0" loading="lazy">
                </a>
                <p
                    class="text-sm text-slate-300 leading-relaxed max-w-sm text-center rtl:sm:text-right ltr:sm:text-left">
                    {{ $tagline }}
                </p>
            </div>

            {{-- Col 2: Dynamic Quick Links --}}
            <div class="space-y-3">
                <h3 class="font-heading font-extrabold text-sm text-white uppercase tracking-wider">{{ $quickTitle }}
                </h3>
                <ul class="space-y-2 text-sm text-slate-300">
                    @foreach ($quickLinks as $link)
                        @php
                            $label = ($locale === 'ar' ? ($link['label_ar'] ?? null) : null) ?: ($link['label_en'] ?? '');
                            $url = $link['url'] ?? '#';
                            if (str_starts_with($url, '/')) {
                                $url = url($url);
                            }
                        @endphp
                        <li>
                            <a href="{{ $url }}"
                                class="hover:text-teal-400 transition-colors link-underline">{{ $label }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Col 3: Dynamic Subjects / Services Links --}}
            <div class="space-y-3">
                <h3 class="font-heading font-extrabold text-sm text-white uppercase tracking-wider">{{ $subjectsTitle }}
                </h3>
                <ul class="space-y-2 text-sm text-slate-300">
                    @foreach ($subjectsLinks as $link)
                        @php
                            $label = ($locale === 'ar' ? ($link['label_ar'] ?? null) : null) ?: ($link['label_en'] ?? '');
                            $url = $link['url'] ?? '#';
                            if (str_starts_with($url, '/')) {
                                $url = url($url);
                            }
                        @endphp
                        <li>
                            <a href="{{ $url }}"
                                class="hover:text-teal-400 transition-colors link-underline">{{ $label }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Col 4: Dynamic Contact Information --}}
            <div class="space-y-3">
                <h3 class="font-heading font-extrabold text-sm text-white uppercase tracking-wider">{{ $contactTitle }}
                </h3>
                <ul class="space-y-2 text-sm text-slate-300">
                    <li class="flex items-center justify-center rtl:sm:justify-start ltr:sm:justify-start gap-2"><i
                            class="fa-solid fa-location-dot"></i> <span>{{ $address }}</span></li>
                    <li class="flex items-center justify-center rtl:sm:justify-start ltr:sm:justify-start gap-2"><i
                            class="fa-solid fa-phone"></i> <span>{{ $phone }}</span></li>
                    <li class="flex items-center justify-center rtl:sm:justify-start ltr:sm:justify-start gap-2"><i
                            class="fa-solid fa-envelope"></i> <span>{{ $email }}</span></li>
                    <li class="flex items-center justify-center rtl:sm:justify-start ltr:sm:justify-start gap-2"><i
                            class="fa-solid fa-clock"></i> <span>{{ $hours }}</span></li>
                </ul>
            </div>
        </div>

        {{-- Bottom Footer: Social Links & Copyright Notice --}}
        @php
            $socialNetworks = [
                ['key' => 'social_facebook', 'icon' => 'fa-brands fa-facebook-f', 'label' => 'Facebook', 'hover' => 'hover:bg-[#1877F2] hover:border-[#1877F2]'],
                ['key' => 'social_twitter', 'icon' => 'fa-brands fa-x-twitter', 'label' => 'Twitter / X', 'hover' => 'hover:bg-[#0F1419] hover:border-slate-500'],
                ['key' => 'social_instagram', 'icon' => 'fa-brands fa-instagram', 'label' => 'Instagram', 'hover' => 'hover:bg-gradient-to-tr hover:from-[#F58529] hover:via-[#DD2A7B] hover:to-[#8134AF] hover:border-transparent'],
                ['key' => 'social_linkedin', 'icon' => 'fa-brands fa-linkedin-in', 'label' => 'LinkedIn', 'hover' => 'hover:bg-[#0A66C2] hover:border-[#0A66C2]'],
                ['key' => 'social_youtube', 'icon' => 'fa-brands fa-youtube', 'label' => 'YouTube', 'hover' => 'hover:bg-[#FF0000] hover:border-[#FF0000]'],
                ['key' => 'social_whatsapp', 'icon' => 'fa-brands fa-whatsapp', 'label' => 'WhatsApp', 'hover' => 'hover:bg-[#25D366] hover:border-[#25D366]'],
                ['key' => 'social_tiktok', 'icon' => 'fa-brands fa-tiktok', 'label' => 'TikTok', 'hover' => 'hover:bg-[#000000] hover:border-slate-500'],
                ['key' => 'social_telegram', 'icon' => 'fa-brands fa-telegram', 'label' => 'Telegram', 'hover' => 'hover:bg-[#229ED9] hover:border-[#229ED9]'],
            ];
        @endphp
        <div
            class="pt-8 border-t border-slate-800/80 flex flex-col sm:flex-row items-center justify-between gap-6 text-xs text-slate-400">
            <div class="flex items-center flex-wrap justify-center gap-3">
                @foreach ($socialNetworks as $net)
                    @php
                        $linkUrl = \App\Models\SiteSetting::get($net['key']);
                    @endphp
                    @if (! empty($linkUrl) && $linkUrl !== '#')
                        <a href="{{ $linkUrl }}" target="_blank"
                            rel="noopener noreferrer"
                            class="social-icon w-10 h-10 rounded-full bg-slate-900 border border-slate-700/60 {{ $net['hover'] }} text-slate-300 hover:text-white flex items-center justify-center text-sm shadow-md transition-all duration-300 hover:scale-110"
                            aria-label="{{ $net['label'] }}">
                            <i class="{{ $net['icon'] }}"></i>
                        </a>
                    @endif
                @endforeach
            </div>
            <p>{{ $rights }}</p>
        </div>
    </div>
</footer>
