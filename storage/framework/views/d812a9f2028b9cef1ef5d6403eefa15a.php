

<?php $__env->startSection('content'); ?>
<?php
    $cTitle = $course ? $course->title : 'Full-Stack Systems & Deep Learning Architecture';
    $cDesc = $course ? ($course->description ?: 'Master end-to-end Python microservices, distributed database indexing, computer vision algorithms, and neural network tuning.') : 'Master end-to-end Python microservices, distributed database indexing, computer vision algorithms, and neural network tuning.';
    $cTeacher = $course?->teacher?->user?->name ?: 'Dr. Elena Rostova';
    $cSubject = $course?->subject?->name ?: 'Programming & AI';
    $cId = $course ? $course->id : 1;
?>

<?php
    $courseJsonLd = [
        "@context" => "https://schema.org",
        "@graph" => [
            [
                "@type" => "Course",
                "@id" => url()->current() . "#course",
                "name" => $cTitle,
                "description" => $cDesc,
                "provider" => [
                    "@type" => "EducationalOrganization",
                    "name" => "Elite Academy LMS",
                    "sameAs" => url('/')
                ],
                "instructor" => [
                    "@type" => "Person",
                    "name" => $cTeacher,
                    "jobTitle" => "Senior Accredited Educator",
                    "worksFor" => [
                        "@type" => "EducationalOrganization",
                        "name" => "Elite Academy LMS"
                    ]
                ],
                "educationalLevel" => "Secondary K-12 & Thanawya Amma Accredited",
                "inLanguage" => app()->getLocale(),
                "aggregateRating" => [
                    "@type" => "AggregateRating",
                    "ratingValue" => "4.9",
                    "reviewCount" => "342",
                    "bestRating" => "5"
                ],
                "offers" => [
                    "@type" => "Offer",
                    "category" => "Educational Track",
                    "priceCurrency" => "EGP",
                    "price" => (string) ($course?->price ?? '0'),
                    "availability" => "https://schema.org/InStock"
                ]
            ],
            [
                "@type" => "BreadcrumbList",
                "@id" => url()->current() . "#breadcrumb",
                "itemListElement" => [
                    [
                        "@type" => "ListItem",
                        "position" => 1,
                        "name" => app()->getLocale() === 'ar' ? 'الرئيسية' : 'Home',
                        "item" => url('/')
                    ],
                    [
                        "@type" => "ListItem",
                        "position" => 2,
                        "name" => app()->getLocale() === 'ar' ? 'المواد الدراسية' : 'Subjects',
                        "item" => route('subjects')
                    ],
                    [
                        "@type" => "ListItem",
                        "position" => 3,
                        "name" => $cSubject,
                        "item" => url()->current()
                    ],
                    [
                        "@type" => "ListItem",
                        "position" => 4,
                        "name" => $cTitle,
                        "item" => url()->current()
                    ]
                ]
            ],
            [
                "@type" => "FAQPage",
                "@id" => url()->current() . "#faq",
                "mainEntity" => [
                    [
                        "@type" => "Question",
                        "name" => app()->getLocale() === 'ar' ? 'هل تشمل هذه الدورة حصصاً تفاعلية مباشرة وتسجيلات؟' : 'Does this course include live interactive sessions and recordings?',
                        "acceptedAnswer" => [
                            "@type" => "Answer",
                            "text" => app()->getLocale() === 'ar' 
                                ? 'نعم، تشمل الدورة حصص بث مباشر أسبوعية تفاعلية مع إمكانية مشاهدة التسجيلات المشفرة في أي وقت.' 
                                : 'Yes, the course includes weekly live interactive streams along with full access to encrypted high-definition session recordings.'
                        ]
                    ],
                    [
                        "@type" => "Question",
                        "name" => app()->getLocale() === 'ar' ? 'هل يتم تقديم شهادة إتمام معتمدة؟' : 'Is an accredited certificate issued upon completion?',
                        "acceptedAnswer" => [
                            "@type" => "Answer",
                            "text" => app()->getLocale() === 'ar' 
                                ? 'نعم، يحصل الطالب على شهادة معتمدة من أكاديمية إيليت بعد اجتياز الاختبارات والواجبات بنجاح.' 
                                : 'Yes, students earn a verified certificate of completion from Elite Academy upon successfully submitting all assignments and passing final assessments.'
                        ]
                    ]
                ]
            ]
        ]
    ];
?>
<script type="application/ld+json">
<?php echo json_encode($courseJsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>

</script>

<section class="py-12 bg-slate-900 text-white border-b border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
        <?php echo $__env->make('components.breadcrumb', [
            'items' => [
                ['label' => __('navbar.home'), 'route' => 'home'],
                ['label' => __('navbar.subjects'), 'route' => 'subjects'],
                ['label' => $cSubject],
                ['label' => $cTitle],
            ]
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <div class="space-y-3 max-w-3xl">
            <div class="flex items-center gap-3">
                <span class="bg-teal-600 text-white text-xs font-bold px-3 py-1 rounded-full"><?php echo e($cSubject); ?></span>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isEnrolled ?? false): ?>
                    <span class="bg-teal-500 text-white text-xs font-bold px-3 py-1 rounded-full">✓ Enrolled Course</span>
                <?php else: ?>
                    <span class="bg-orange-500 text-white text-xs font-bold px-3 py-1 rounded-full">▶ Free Demo Available</span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <h1 class="font-heading text-3xl sm:text-5xl font-extrabold text-white tracking-tight">
                <?php echo e($cTitle); ?>

            </h1>

            <p class="text-slate-300 text-base leading-relaxed">
                <?php echo e($cDesc); ?>

            </p>

            <div class="flex flex-wrap items-center gap-6 pt-4 text-xs font-medium text-slate-300">
                <span>⏱️ Duration: 16 Weeks</span>
                <span>👥 Teacher: <?php echo e($cTeacher); ?></span>
                <span>⭐ Rating: 4.9/5</span>
                <span>🏆 Accredited Certification</span>
            </div>
        </div>
    </div>
</section>


<section class="py-12 md:py-16 bg-[#FAFAF9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
            <div class="lg:col-span-8 space-y-12">
                
                <?php
                    $isArabicTitle = preg_match('/\p{Arabic}/u', $cTitle);
                    $demoTitle = ($isArabicTitle || app()->getLocale() === 'ar')
                        ? 'الحصة الأولى التجريبية: ' . $cTitle 
                        : 'Watch Sample Lesson 1.1: ' . $cTitle;
                    $demoSubtitle = ($isArabicTitle || app()->getLocale() === 'ar')
                        ? 'شاهد الحصة المجانية الأولى واستكشف أسلوب الشرح التفاعلي والتطبيقات العملية قبل الاشتراك.'
                        : 'Get a glimpse of our hands-on teaching style before committing. This sample demo covers core concepts and interactive exercises.';
                    $videoData = $course ? $course->getVideoEmbedData() : ['type' => 'mp4', 'embed_url' => asset('videos/physics_demo.mp4')];
                    $posterImage = $course && $course->image ? media_url($course->image, 'images/course_ai.png') : asset('images/course_ai.png');
                ?>
                <div id="demo" class="bg-gradient-to-br from-teal-900 via-slate-900 to-teal-950 text-white rounded-3xl p-8 border border-teal-500/40 shadow-xl space-y-6">
                    <div class="flex items-center justify-between border-b border-teal-500/30 pb-4">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-orange-500 animate-pulse"></span>
                            <span class="font-mono text-xs font-bold uppercase tracking-widest text-orange-400"><?php echo e(__('Interactive Preview')); ?></span>
                        </div>
                        <span class="text-xs font-mono bg-teal-800/80 text-teal-200 px-3 py-1 rounded-full border border-teal-500/30"><?php echo e(__('Free Demo Lesson')); ?></span>
                    </div>

                    <div class="space-y-3">
                        <h2 class="font-heading font-extrabold text-2xl text-white">
                            <?php echo e($demoTitle); ?>

                        </h2>
                        <p class="text-slate-300 text-xs leading-relaxed">
                            <?php echo e($demoSubtitle); ?>

                        </p>
                    </div>

                    <?php if (isset($component)) { $__componentOriginal5301b23ce04afdf1845ed0b5d2aa7a9f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5301b23ce04afdf1845ed0b5d2aa7a9f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.secure-video-player','data' => ['course' => $course,'videoData' => $videoData,'posterImage' => $posterImage,'title' => $cTitle]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('secure-video-player'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['course' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($course),'videoData' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($videoData),'posterImage' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($posterImage),'title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($cTitle)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5301b23ce04afdf1845ed0b5d2aa7a9f)): ?>
<?php $attributes = $__attributesOriginal5301b23ce04afdf1845ed0b5d2aa7a9f; ?>
<?php unset($__attributesOriginal5301b23ce04afdf1845ed0b5d2aa7a9f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5301b23ce04afdf1845ed0b5d2aa7a9f)): ?>
<?php $component = $__componentOriginal5301b23ce04afdf1845ed0b5d2aa7a9f; ?>
<?php unset($__componentOriginal5301b23ce04afdf1845ed0b5d2aa7a9f); ?>
<?php endif; ?>
                </div>

                
                <?php echo $__env->make('components.curriculum-timeline', [
                    'sessions' => $course?->sessions,
                    'title' => 'Course Curriculum & Module Lifetime Roadmap',
                    'subtitle' => 'Structured timeline of lectures, live coding labs, and homework assignments'
                ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>

            
            <div class="lg:col-span-4 space-y-6">
                <?php
                    $nextLiveSession = $course?->liveSessions?->where('scheduled_at', '>=', now())->sortBy('scheduled_at')->first();
                    $targetDate = $nextLiveSession ? $nextLiveSession->scheduled_at->toIso8601String() : now()->addDays(3)->setTime(18, 0)->toIso8601String();
                    $sessionTitle = $nextLiveSession ? $nextLiveSession->title : null;
                ?>

                
                <?php echo $__env->make('components.course-countdown-timer', [
                    'targetDate' => $targetDate,
                    'sessionTitle' => $sessionTitle,
                    'title' => app()->getLocale() === 'ar' ? 'عداد البث المباشر القادم' : 'Live Cohort Start Timer',
                    'subtitle' => app()->getLocale() === 'ar' ? 'الوقت المتبقي لإنطلاق حصة البث المباشر التفاعلية' : 'Countdown to upcoming interactive live stream'
                ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-2xs space-y-6">
                    <div class="space-y-2 border-b border-slate-100 pb-4">
                        <span class="text-xs font-mono font-extrabold text-slate-400 uppercase">Tuition Fee</span>
                        <p class="font-mono text-3xl font-extrabold text-slate-900">$290 <span class="text-xs text-slate-400 font-normal">/ term</span></p>
                    </div>

                    <div id="enrollAlert" class="hidden p-3 rounded-xl text-xs font-semibold"></div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isEnrolled ?? false): ?>
                            <a href="<?php echo e(route('student-portal')); ?>" class="btn-lift w-full inline-block text-center py-3.5 px-6 font-semibold text-white bg-teal-600 hover:bg-teal-700 rounded-xl shadow-md transition-all">
                                <?php echo e(app()->getLocale() === 'ar' ? 'مشترك في هذا الكورس ✓ — الذهاب لبوابة الطالب ←' : 'Enrolled Course ✓ — Go to Student Portal &rarr;'); ?>

                            </a>
                        <?php else: ?>
                            <button id="btnEnroll" class="w-full text-center py-3.5 px-6 font-semibold text-white bg-orange-500 hover:bg-orange-600 rounded-xl shadow-md transition-all cursor-pointer">
                                <?php echo e(app()->getLocale() === 'ar' ? 'التسجيل في الكورس الآن 🚀' : 'Enroll in Course Now 🚀'); ?>

                            </button>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php else: ?>
                        <a href="<?php echo e(route('login')); ?>" class="w-full inline-block text-center py-3.5 px-6 font-semibold text-white bg-teal-600 hover:bg-teal-700 rounded-xl shadow-md transition-all">
                            <?php echo e(app()->getLocale() === 'ar' ? 'سجل الدخول للتسجيل في الكورس' : 'Log In to Enroll'); ?>

                        </a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <div class="pt-4 border-t border-slate-100 space-y-3">
                        <p class="text-xs font-bold text-slate-900 uppercase tracking-wider">Course Teacher</p>
                        <div class="flex items-center gap-3">
                            <img src="<?php echo e(asset('images/instructor_portrait.png')); ?>" alt="<?php echo e($cTeacher); ?>" class="w-10 h-10 rounded-xl object-cover border border-teal-500">
                            <div>
                                <a href="<?php echo e(route('teachers')); ?>" class="text-xs font-bold text-slate-900 hover:text-teal-600"><?php echo e($cTeacher); ?></a>
                                <p class="text-[11px] text-slate-500">Senior Academic Lead</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! ($isEnrolled ?? false)): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const btn = document.getElementById('btnEnroll');
    const alertBox = document.getElementById('enrollAlert');
    if (!btn) return;

    btn.addEventListener('click', async function () {
        btn.disabled = true;
        btn.textContent = "<?php echo e(app()->getLocale() === 'ar' ? 'جاري التسجيل...' : 'Enrolling...'); ?>";

        try {
            const res = await fetch("<?php echo e(route('ajax.course.enroll', $cId)); ?>", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                    'Accept': 'application/json'
                }
            });
            const data = await res.json();

            if (window.Toast) {
                if (data.success) {
                    window.Toast.success(data.message || 'Enrolled in course successfully!');
                } else {
                    window.Toast.error(data.message || 'Enrollment failed.');
                }
            }

            alertBox.className = `p-3 rounded-xl text-xs font-semibold ${data.success ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-red-50 text-red-700 border border-red-200'}`;
            alertBox.textContent = data.message;
            alertBox.classList.remove('hidden');

            if (data.success) {
                const portalUrl = "<?php echo e(route('student-portal')); ?>";
                const linkText = "<?php echo e(app()->getLocale() === 'ar' ? 'تم التسجيل بنجاح ✓ — الذهاب لبوابة الطالب ←' : 'Enrolled Successfully ✓ — Go to Student Portal &rarr;'); ?>";
                btn.outerHTML = `<a href="${portalUrl}" class="btn-lift w-full inline-block text-center py-3.5 px-6 font-semibold text-white bg-teal-600 hover:bg-teal-700 rounded-xl shadow-md transition-all">${linkText}</a>`;
            } else {
                btn.disabled = false;
                btn.textContent = "<?php echo e(app()->getLocale() === 'ar' ? 'التسجيل في الكورس الآن 🚀' : 'Enroll in Course Now 🚀'); ?>";
            }
        } catch (e) {
            btn.disabled = false;
            btn.textContent = "<?php echo e(app()->getLocale() === 'ar' ? 'التسجيل في الكورس الآن 🚀' : 'Enroll in Course Now 🚀'); ?>";
            alertBox.className = 'p-3 rounded-xl text-xs font-semibold bg-red-50 text-red-700 border border-red-200';
            alertBox.textContent = 'Network error. Please try again.';
            alertBox.classList.remove('hidden');
        }
    });
});
</script>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\elite-academy\resources\views/pages/course-details.blade.php ENDPATH**/ ?>