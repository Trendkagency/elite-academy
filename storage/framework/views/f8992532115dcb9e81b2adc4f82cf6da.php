
<section class="py-20 lg:py-28 bg-[#FAFAF9] relative overflow-hidden border-b border-slate-200/80" aria-label="<?php echo e(__('Student & Parent Reviews')); ?>">
    
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[40rem] h-[22rem] bg-teal-400/10 rounded-full blur-3xl pointer-events-none -z-10"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10 relative z-10">

        
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-6 border-b border-slate-200/80">
            <div class="space-y-3 max-w-xl text-start">
                <span class="inline-flex items-center gap-2 text-xs font-mono uppercase tracking-widest text-teal-800 font-extrabold bg-teal-100/90 px-4 py-1.5 rounded-full border border-teal-300 shadow-2xs">
                    <span>💡</span>
                    <span><?php echo e(__('TESTIMONIALS & REVIEWS')); ?></span>
                </span>
                <h2 class="font-heading text-3xl sm:text-4xl lg:text-5xl font-black text-slate-900 tracking-tight leading-tight">
                    <?php echo e(\App\Models\SiteSetting::getLocalized('testimonials_title', __('What Our Students & Parents Say'))); ?>

                </h2>
            </div>

            
            <div class="flex items-center gap-4">
                <span class="text-xs font-mono text-slate-600 font-bold hidden sm:inline-block"><?php echo e(__('Swipe Reviews')); ?></span>
                <div class="flex items-center gap-2.5">
                    <button id="testimonial-prev-btn" aria-label="<?php echo e(__('Previous Review')); ?>" class="w-11 h-11 rounded-full bg-white border border-slate-300 shadow-md flex items-center justify-center text-slate-800 hover:bg-teal-600 hover:text-white hover:border-teal-600 transition-all duration-200 active:scale-95 cursor-pointer">
                        <svg class="w-5 h-5 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>
                    <button id="testimonial-next-btn" aria-label="<?php echo e(__('Next Review')); ?>" class="w-11 h-11 rounded-full bg-white border border-slate-300 shadow-md flex items-center justify-center text-slate-800 hover:bg-teal-600 hover:text-white hover:border-teal-600 transition-all duration-200 active:scale-95 cursor-pointer">
                        <svg class="w-5 h-5 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        
        <?php
            $dbTestimonials = \Illuminate\Support\Facades\Schema::hasTable('testimonials')
                ? \App\Models\Testimonial::where('is_featured', true)->orderBy('sort_order')->get()
                : collect();

            $isArabic = app()->getLocale() === 'ar';

            $testimonials = $dbTestimonials->count() > 0 ? $dbTestimonials->map(fn($t) => [
                'quote' => $t->getLocalizedContent(),
                'photo' => $t->avatar ?: 'images/instructor_portrait.png',
                'name' => $t->name,
                'course' => $t->getLocalizedCourseName() ?: __('Elite Academic Track'),
                'badge' => $t->is_verified ? ($isArabic ? '✔ طالب موثق' : '✔ Verified Student') : ($isArabic ? 'خريج متميز' : 'Top Graduate'),
            ]) : [
                [
                    'quote' => $isArabic
                        ? 'أكاديمية إيليت غيرت مفهوم ابني تماماً في البرمجة والرياضيات. التواصل المباشر مع نخبة المعلمين أحدث فارقاً كبيراً في تفوقه.'
                        : 'Elite Academy completely transformed my son\'s approach to coding and math. Having direct access to PhD mentors made all the difference.',
                    'photo' => 'images/hero_student.png',
                    'name' => $isArabic ? 'مريم المنصور' : 'Mariam Al-Mansoor',
                    'course' => $isArabic ? 'برمجة وتطوير البرمجيات' : 'Full-Stack Programming',
                    'badge' => $isArabic ? '✔ ولي أمر موثق' : '✔ Verified Parent',
                ],
                [
                    'quote' => $isArabic
                        ? 'مختبرات الذكاء الاصطناعي والروبوتات منحتني خبرة عملية حقيقية في بناء نماذج الرؤية الحاسوبية، وحصلت على فرصة عمل فور تخرجي!'
                        : 'The robotics and AI labs gave me real hands-on experience building computer vision models. I secured a software engineering role right after graduation!',
                    'photo' => 'images/instructor_portrait.png',
                    'name' => $isArabic ? 'كريم السيد' : 'Kareem El-Sayed',
                    'course' => $isArabic ? 'الذكاء الاصطناعي وتعلم الآلة' : 'AI & Machine Learning',
                    'badge' => $isArabic ? '✔ طالب موثق' : '✔ Verified Student',
                ],
                [
                    'quote' => $isArabic
                        ? 'مراجعات الفيزياء والرياضيات البحتة كانت تدريبًا مكثفًا بأسلوب الامتحانات النهائية مما ساعدني على تحقيق مجموع 99.2%!'
                        : 'The Thanawya Amma Physics and Mathematics preparation courses provided precise exam-style problem solving that helped me achieve 99.2%!',
                    'photo' => 'images/hero_student.png',
                    'name' => $isArabic ? 'أحمد حسن' : 'Ahmed Hassan',
                    'course' => $isArabic ? 'الفيزياء والرياضيات المتقدمة' : 'Advanced Physics & Pure Math',
                    'badge' => $isArabic ? '✔ خريج متميز' : '✔ Top Graduate',
                ],
            ];
        ?>

        <div id="testimonial-carousel" class="carousel-container no-scrollbar flex items-center gap-6 overflow-x-auto py-4 snap-x snap-mandatory scroll-smooth">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $testimonials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <div class="w-full max-w-[420px] sm:w-[420px] shrink-0 h-[330px] bg-white rounded-3xl p-7 border border-slate-200/90 shadow-lg hover:shadow-xl flex flex-col justify-between transition-all duration-300 snap-center group hover:border-teal-500/30">

                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-1 text-amber-400 text-sm" aria-label="5 out of 5 stars">
                            ★★★★★
                        </div>
                        <span class="w-9 h-9 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center font-serif font-black text-xl select-none group-hover:bg-teal-600 group-hover:text-white transition-colors duration-300">
                            “
                        </span>
                    </div>

                    
                    <p class="text-start font-sans font-semibold text-slate-800 text-base leading-relaxed line-clamp-4 my-2 flex-1 dir-auto">
                        <?php echo e($t['quote']); ?>

                    </p>

                    
                    <div class="pt-4 border-t border-slate-100 flex items-center gap-4 text-start">
                        <img src="<?php echo e(media_url($t['photo'], 'images/instructor_portrait.png')); ?>" alt="<?php echo e($t['name']); ?>" class="w-13 h-13 rounded-full object-cover shadow-sm border-2 border-slate-200 flex-shrink-0 group-hover:border-teal-400 transition-colors">
                        <div class="space-y-1 min-w-0 flex-1">
                            <h3 class="font-heading font-extrabold text-base text-slate-900 truncate group-hover:text-teal-700 transition-colors"><?php echo e($t['name']); ?></h3>
                            <p class="text-xs font-mono text-slate-600 font-bold truncate"><?php echo e($t['course']); ?></p>
                            <span class="inline-block bg-teal-50 text-teal-800 border border-teal-200 text-[11px] font-mono font-bold px-2.5 py-0.5 rounded-full">
                                <?php echo e($t['badge']); ?>

                            </span>
                        </div>
                    </div>
                </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>

    </div>
</section>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const carousel = document.getElementById('testimonial-carousel');
    const prevBtn = document.getElementById('testimonial-prev-btn');
    const nextBtn = document.getElementById('testimonial-next-btn');

    if (carousel && prevBtn && nextBtn) {
        const scrollAmount = 440;
        prevBtn.addEventListener('click', function () {
            const isRTL = document.documentElement.dir === 'rtl';
            carousel.scrollBy({ left: isRTL ? scrollAmount : -scrollAmount, behavior: 'smooth' });
        });
        nextBtn.addEventListener('click', function () {
            const isRTL = document.documentElement.dir === 'rtl';
            carousel.scrollBy({ left: isRTL ? -scrollAmount : scrollAmount, behavior: 'smooth' });
        });
    }
});
</script>


<?php /**PATH C:\laragon\www\elite-academy\resources\views\pages\home\testimonials.blade.php ENDPATH**/ ?>