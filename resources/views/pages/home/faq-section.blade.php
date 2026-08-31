@php
    $locale = app()->getLocale();
    $isAr = $locale === 'ar';

    $homeFaqs = [
        [
            'q' => $isAr ? 'ما هي أكاديمية إيليت التعليمية وكيف تختلف عن المنصات الأخرى؟' : 'What is Elite Academy LMS and how does it differ from other platforms?',
            'a' => $isAr 
                ? 'أكاديمية إيليت هي المنصة التعليمية الرقمية المعتمدة الأولى في مصر المخصصة لمراحل الثانوية العامة واللغات. تجمع المنصة بين البث المباشر التفاعلي المشفر، الواجبات التفاعلية المصححة تلقائياً، وبوابة ولي الأمر اللحظية لمتابعة الحضور والنتائج.' 
                : 'Elite Academy LMS is Egypt premier accredited interactive K-12 learning platform for secondary tracks. It combines encrypted live streaming sessions, auto-graded interactive assignment solvers, and real-time parent progress portals.'
        ],
        [
            'q' => $isAr ? 'كيف تعمل حصص البث المباشر التفاعلية والأمان ضد التسريب؟' : 'How do live interactive streaming sessions work and what anti-piracy protection is included?',
            'a' => $isAr 
                ? 'يتصل الطالب بالبث المباشر عبر سيرفرات مشفرة مدمجة مع علامة مائية أمنية ديناميكية تحمل اسم الطالب ورقم الهوية وحاسوب الوصول، مما يحظر تسجيل الشاشة أو إعادة النشر تلقائياً.' 
                : 'Students join encrypted live streams with dynamic security watermarking displaying their identity, IP address, and timestamp to protect intellectual property and prevent screen recording.'
        ],
        [
            'q' => $isAr ? 'هل يتم حفظ مسودات الواجبات والاختبارات وتصحيحها فورياً؟' : 'Are assignment drafts saved automatically and scored instantly?',
            'a' => $isAr 
                ? 'نعم، تتميز واجهة حل الواجبات بحفظ المسودات حتى عند انقطاع الإنترنت، مع تصحيح فوري للمرحلة وتقديم تغذية راجعة وشرح بالفيديو لكل سؤال.' 
                : 'Yes, the interactive assignment solver auto-saves drafts even offline, calculates step-by-step scores instantly, and provides video explanations for incorrect answers.'
        ],
        [
            'q' => $isAr ? 'كيف يمكن لأولياء الأمور متابعة الحضور والدرجات؟' : 'How can parents monitor real-time attendance and academic performance?',
            'a' => $isAr 
                ? 'من خلال بوابة ولي الأمر، يقوم ولي الأمر بربط حساب الطالب برقم الهاتف لمتابعة نسبة الحضور بالدقيقة، نتائج الواجبات، وتلقي إشعارات لحظية عبر الواتساب أو FCM عند الغياب.' 
                : 'Parents access the Parent Portal by verifying their child phone number to view live attendance logs, quiz scores, and receive instant push/WhatsApp alerts for absences.'
        ],
        [
            'q' => $isAr ? 'هل الشهادات الصادرة من المنصة معتمدة؟' : 'Are certificates earned on the platform officially accredited?',
            'a' => $isAr 
                ? 'جميع المسارات التعليمية وحصص إتمام المناهج تمنح الطلاب شهادات إتمام رقمية مشفرة وموثقة برقم تسلسلي معتمد لدى الجهات الأكاديمية.' 
                : 'All completed academic tracks issue verified digital certificates with unique serial numbers recognized by leading educational institutions.'
        ],
        [
            'q' => $isAr ? 'ما هي طرق الدفع واشتراكات الحصص المتاحة؟' : 'What payment options and subscription package choices are available?',
            'a' => $isAr 
                ? 'توفر المنصة اشتراكات شهرية، باقات بالحصص، أو شراء كورس كامل مع دعم وسائل الدفع الإلكتروني (فوري، كروت ائتمان، المحافظ الإلكترونية مثل فودافون كاش).' 
                : 'We offer flexible monthly subscriptions, pay-per-session packages, and full course unlocks via Fawry, Credit Cards, and Mobile Wallets (Vodafone Cash).'
        ],
    ];
@endphp

{{-- FAQ Section (Section: Deep Dark Background) --}}
<section id="faq-section" class="py-16 md:py-24 bg-slate-950 text-white border-y border-slate-800/80 transition-colors duration-200" itemscope itemtype="https://schema.org/FAQPage">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
        <div class="text-center space-y-4 max-w-3xl mx-auto">
            <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-mono font-bold bg-teal-950/80 text-teal-300 border border-teal-500/40">
                <span>💬</span> {{ $isAr ? 'مركز الأسئلة الشائعة والمعلومات' : 'Frequently Asked Questions' }}
            </span>
            <h2 class="font-heading font-extrabold text-3xl sm:text-4xl text-white tracking-tight">
                {{ $isAr ? 'كل ما تحتاج معرفته عن منصة إيليت التعليمية' : 'Everything You Need to Know About Elite Academy' }}
            </h2>
            <p class="text-slate-300 text-sm sm:text-base leading-relaxed">
                {{ $isAr 
                    ? 'إجابات شاملة وموثقة حول البث المباشر، تصحيح الواجبات، أدوات أولياء الأمور، والشهادات المعتمدة.' 
                    : 'Clear, authoritative answers regarding our live stream tech, auto-graded assignments, parent monitoring, and accredited tracks.' 
                }}
            </p>
        </div>

        <div class="space-y-4">
            @foreach($homeFaqs as $faq)
                @include('components.faq-item', [
                    'question' => $faq['q'],
                    'answer' => $faq['a']
                ])
            @endforeach
        </div>

        <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-teal-950 rounded-3xl p-8 text-white flex flex-wrap items-center justify-between gap-6 shadow-xl border border-slate-700/80">
            <div class="space-y-1 max-w-xl">
                <h3 class="font-heading font-bold text-lg text-white">
                    {{ $isAr ? 'لديك استفسار آخر لم نجب عليه؟' : 'Have a specific question not listed here?' }}
                </h3>
                <p class="text-xs text-slate-300">
                    {{ $isAr ? 'فريق الدعم الأكاديمي والتقني متاح على مدار الساعة للإجابة على جميع تساؤلاتك.' : 'Our academic support specialists are available 24/7 to guide you.' }}
                </p>
            </div>
            <a href="{{ route('contact') }}" class="btn-lift px-6 py-3 bg-teal-500 hover:bg-teal-400 text-slate-950 font-bold rounded-2xl text-xs shadow-lg transition-all">
                💬 {{ $isAr ? 'تواصل مع الدعم المباشر' : 'Contact Support Team' }}
            </a>
        </div>
    </div>
</section>
