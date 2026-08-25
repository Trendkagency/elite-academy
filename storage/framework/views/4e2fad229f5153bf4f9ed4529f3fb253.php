<?php
    $locale = app()->getLocale();
    $isAr = $locale === 'ar';

    $faqCategories = [
        [
            'title' => $isAr ? 'القبول والتسجيل والاشتراكات' : 'Admissions, Enrollment & Packages',
            'items' => [
                [
                    'q' => $isAr ? 'كيف يتم الاشتراك في الكورسات أو باقات الحصص؟' : 'How do I enroll in a course or purchase session packages?',
                    'a' => $isAr 
                        ? 'تصفح قائمة المواد ثم اختر الكورس أو الباقة المطلوبة. اضغط على "اشتراك" واختر طريقة الدفع المناسبة (فوري، كروت ائتمان، محفظة إلكترونية). تتاح حصة تجريبية مجانية لجميع الكورسات قبل الدفع.' 
                        : 'Browse our subjects catalog and select your target course. Click "Enroll Now" and select your preferred payment method (Fawry, Credit Card, Mobile Wallet). A free demo lesson is available for every course.'
                ],
                [
                    'q' => $isAr ? 'هل تتاح حصص تجريبية مجانية قبل الشراء؟' : 'Are free demo lessons available before purchasing?',
                    'a' => $isAr 
                        ? 'نعم، توفر المنصة حصة تجريبية مجانية كاملة لكل كورس حتى يتمكن الطالب وولي الأمر من تجربة الشرح التفاعلي وجودة البث.' 
                        : 'Yes! Every course includes a complete free preview demo lesson so students and parents can evaluate the teaching style and stream quality.'
                ],
                [
                    'q' => $isAr ? 'ما هي سياسة الاسترداد وإلغاء الاشتراك؟' : 'What is the refund and cancellation policy?',
                    'a' => $isAr 
                        ? 'يمكن طلب استرداد المبلغ خلال 7 أيام من الشراء بشرط عدم مشاهدة أكثر من حصة واحدة أو إجراء اختبار نضج.' 
                        : 'Full refunds can be requested within 7 days of purchase provided no more than 1 live session or assignment has been consumed.'
                ]
            ]
        ],
        [
            'title' => $isAr ? 'تقنية البث المباشر والحماية الأكاديمية' : 'Live Session Streaming & Anti-Piracy Tech',
            'items' => [
                [
                    'q' => $isAr ? 'كيف تعمل غرفة الاجتماعات والبث المباشر داخل المنصة؟' : 'How does the in-system live meeting room operate?',
                    'a' => $isAr 
                        ? 'يتم الدخول للبث بنقرة واحدة داخل المنصة عبر زوم أو جيتسي دون الحاجة لتنزيل تطبيقات خارجية، مع علامة مائية أمنية ديناميكية لمنع تصوير الشاشة.' 
                        : 'Students join live classes directly inside their dashboard via embedded Zoom/Jitsi stream frames, featuring dynamic security watermarks to prevent piracy.'
                ],
                [
                    'q' => $isAr ? 'ماذا يحدث إذا فاتني موعد البث المباشر؟' : 'What happens if I miss a live stream session?',
                    'a' => $isAr 
                        ? 'تتم أرشفة جميع الحصص المباشرة فور انتهائها وتتوفر بتنسيق HD عالي الجودة داخل حساب الطالب لمشاهدتها في أي وقت طوال فترة الكورس.' 
                        : 'All live streams are automatically recorded in Full HD and uploaded to the student portal for unlimited re-watching throughout the semester.'
                ]
            ]
        ],
        [
            'title' => $isAr ? 'نظام الواجبات والتصحيح التلقائي' : 'Interactive Assignment Solver & Auto-Grading',
            'items' => [
                [
                    'q' => $isAr ? 'كيف يتم حل الواجبات وتصحيحها؟' : 'How are assignments submitted and evaluated?',
                    'a' => $isAr 
                        ? 'يستخدم الطالب واجهة حل الواجبات التفاعلية خطوة بخطوة مع حفظ مسودات الإجابات تلقائياً. يتم الاحتساب الفوري للدرجات وتوفير شروحات للإجابات الصحيحة.' 
                        : 'Students use our step-by-step solver interface with auto-saved drafts. Scores are calculated instantly with detailed solution walkthroughs.'
                ],
                [
                    'q' => $isAr ? 'ماذا يحدث في حال انقطاع اتصال الإنترنت أثناء الاختبار؟' : 'What happens if my internet disconnects during a quiz?',
                    'a' => $isAr 
                        ? 'تحفظ الواجبات إجاباتك محلياً في الذاكرة المؤقتة، وتتم مزامنتها تلقائياً مع الخادم فور إعادة الاتصال دون فقدان أي بيانات.' 
                        : 'Draft answers are cached locally in your browser memory and synchronized automatically when internet connectivity is restored.'
                ]
            ]
        ],
        [
            'title' => $isAr ? 'بوابة ولي الأمر والإشعارات' : 'Parent Portal & Real-time Progress Tracking',
            'items' => [
                [
                    'q' => $isAr ? 'كيف يمكن لولي الأمر متابعة الطالب بالدقيقة؟' : 'How do parents monitor real-time student performance?',
                    'a' => $isAr 
                        ? 'يقوم ولي الأمر بربط حساب الابن عبر بوابة ولي الأمر باستخدام رقم الهاتف لمشاهدة سجل الحضور بالدقيقة، درجات الواجبات، وتنبيهات الغياب.' 
                        : 'Parents link student profiles on the Parent Portal using phone verification to access live attendance logs, homework grades, and absence alerts.'
                ],
                [
                    'q' => $isAr ? 'كيف يتم تلقي إشعارات الحصص والواجبات؟' : 'How are notification alerts delivered?',
                    'a' => $isAr 
                        ? 'ترسل المنصة إشعارات برمجية خفيفة (FCM Push Notifications) وتنبيهات لحظية عند اقتراب مواعيد الحصص المباشرة أو موعد تسليم الواجبات.' 
                        : 'We send native browser FCM push alerts and real-time dashboard notifications 30 minutes before live streams and 24 hours prior to homework deadlines.'
                ]
            ]
        ],
        [
            'title' => $isAr ? 'الشهادات والاعتماد الأكاديمي' : 'Accreditation & Certificate Verification',
            'items' => [
                [
                    'q' => $isAr ? 'هل الشهادات الصادرة موثقة وقابلة للتحقق؟' : 'Are completion certificates verified and official?',
                    'a' => $isAr 
                        ? 'نعم، يحصل الخريجون على شهادة إتمام رقمية تحمل رمز QR ورمز تحقق فريد يمكن الاستعلام عنه رسمياً من موقع المنصة.' 
                        : 'Yes! Graduates receive encrypted digital certificates featuring QR codes and unique verification IDs for institutional validation.'
                ]
            ]
        ]
    ];

    $allFaqItems = [];
    foreach ($faqCategories as $cat) {
        foreach ($cat['items'] as $item) {
            $allFaqItems[] = [
                '@type' => 'Question',
                'name' => $item['q'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $item['a']
                ]
            ];
        }
    }

    $faqPageJsonLd = [
        "@context" => "https://schema.org",
        "@graph" => [
            [
                "@type" => "BreadcrumbList",
                "@id" => route('faq') . "#breadcrumb",
                "itemListElement" => [
                    [
                        "@type" => "ListItem",
                        "position" => 1,
                        "name" => $isAr ? 'الرئيسية' : 'Home',
                        "item" => url('/')
                    ],
                    [
                        "@type" => "ListItem",
                        "position" => 2,
                        "name" => $isAr ? 'الأسئلة الشائعة' : 'FAQ',
                        "item" => route('faq')
                    ]
                ]
            ],
            [
                "@type" => "FAQPage",
                "@id" => route('faq') . "#faq",
                "mainEntity" => $allFaqItems
            ]
        ]
    ];
?>

<?php $__env->startSection('content'); ?>
<script type="application/ld+json">
<?php echo json_encode($faqPageJsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>

</script>

<section class="py-12 md:py-16 bg-slate-900 text-white border-b border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
        <?php echo $__env->make('components.breadcrumb', [
            'items' => [
                ['label' => __('navbar.home'), 'route' => 'home'],
                ['label' => $isAr ? 'الأسئلة الشائعة والمركز المعرفي' : 'Frequently Asked Questions'],
            ]
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <div class="text-center max-w-3xl mx-auto space-y-4 pt-4">
            <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-mono font-bold bg-teal-500/20 text-teal-300 border border-teal-500/40">
                <span>💡</span> <?php echo e($isAr ? 'المركز المعرفي والمساعدة الشاملة' : 'Knowledge Base & Support Hub'); ?>

            </span>
            <h1 class="font-heading font-extrabold text-3xl sm:text-5xl text-white tracking-tight">
                <?php echo e($isAr ? 'كيف يمكننا مساعدتك اليوم؟' : 'Frequently Asked Questions'); ?>

            </h1>
            <p class="text-slate-300 text-sm sm:text-base leading-relaxed">
                <?php echo e($isAr 
                    ? 'دليل استرشادي شامل يجيب على كافة التساؤلات الفنية والأكاديمية حول الحصص، التصحيح، والباقات.' 
                    : 'Everything you need to know about our curriculums, live sessions, auto-grading, parent monitoring, and accredited track certificates.'); ?>

            </p>
        </div>
    </div>
</section>


<section class="py-12 md:py-16 bg-[#FAFAF9]" itemscope itemtype="https://schema.org/FAQPage">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $faqCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <div class="space-y-4">
                <h2 class="font-heading font-bold text-xl sm:text-2xl text-slate-900 border-b border-slate-200/90 pb-3 flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-teal-500"></span>
                    <?php echo e($category['title']); ?>

                </h2>

                <div class="space-y-3">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $category['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $faq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php echo $__env->make('components.faq-item', [
                            'question' => $faq['q'],
                            'answer' => $faq['a']
                        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

        <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm flex flex-wrap items-center justify-between gap-6">
            <div class="space-y-1">
                <h3 class="font-heading font-bold text-lg text-slate-900">
                    <?php echo e($isAr ? 'لم تجد الإجابة التي تبحث عنها؟' : 'Still have unanswered questions?'); ?>

                </h3>
                <p class="text-xs text-slate-600">
                    <?php echo e($isAr ? 'يمكنك التواصل المباشر مع فريق الدعم الأكاديمي عبر الهاتف أو النموذج الإلكتروني.' : 'Reach out directly to our academic support staff for one-on-one assistance.'); ?>

                </p>
            </div>
            <a href="<?php echo e(route('contact')); ?>" class="btn-lift px-6 py-3 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-2xl text-xs shadow-md transition-all">
                ✉️ <?php echo e($isAr ? 'إرسال استفسار للدعم' : 'Submit Support Ticket'); ?>

            </a>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\elite-academy\resources\views/pages/faq.blade.php ENDPATH**/ ?>