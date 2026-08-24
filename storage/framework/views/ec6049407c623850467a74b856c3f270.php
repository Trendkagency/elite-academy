<?php use \App\Models\SiteSetting; ?>

<?php
    $dbHeroSlides = \Illuminate\Support\Facades\Schema::hasTable('hero_slides')
        ? \App\Models\HeroSlide::where('is_active', true)->orderBy('sort_order')->get()
        : collect();
    $totalSlideCount = $dbHeroSlides->count() > 0 ? $dbHeroSlides->count() : 4;
?>

<section 
    x-data="{ 
        activeSlide: 0, 
        totalSlides: <?php echo e($totalSlideCount); ?>,
        timer: null,
        startAutoplay() {
            this.stopAutoplay();
            this.timer = setInterval(() => {
                this.nextSlide();
            }, 6000);
        },
        stopAutoplay() {
            if (this.timer) clearInterval(this.timer);
        },
        nextSlide() {
            this.activeSlide = (this.activeSlide + 1) % this.totalSlides;
        },
        prevSlide() {
            this.activeSlide = (this.activeSlide - 1 + this.totalSlides) % this.totalSlides;
        },
        goToSlide(index) {
            this.activeSlide = index;
        }
    }"
    x-init="startAutoplay()"
    @mouseenter="stopAutoplay()"
    @mouseleave="startAutoplay()"
    class="w-full min-h-[75vh] lg:min-h-[92vh] relative overflow-hidden bg-slate-950 text-white flex flex-col justify-between hero-section select-none"
>
    
    <div class="absolute top-28 left-[10%] w-4 h-4 border-2 border-teal-500/30 rounded-full animate-drift pointer-events-none -z-10"></div>
    <div class="absolute top-96 right-[12%] w-6 h-6 border-2 border-orange-500/20 rounded-md rotate-12 animate-float pointer-events-none -z-10"></div>

    
    <div class="absolute -top-24 -left-24 w-[40rem] h-[40rem] bg-teal-500/20 rounded-full blur-3xl pointer-events-none z-0 animate-pulse-glow"></div>
    <div class="absolute bottom-0 right-0 w-[45rem] h-[45rem] bg-orange-500/15 rounded-full blur-3xl pointer-events-none z-0 animate-float"></div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($dbHeroSlides->count() > 0): ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $dbHeroSlides; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <div 
                x-show="activeSlide === <?php echo e($idx); ?>" 
                x-transition:enter="transition ease-out duration-700" 
                x-transition:enter-start="opacity-0 scale-98" 
                x-transition:enter-end="opacity-100 scale-100" 
                x-transition:leave="transition ease-in duration-400" 
                x-transition:leave-start="opacity-100" 
                x-transition:leave-end="opacity-0"
                class="absolute inset-0 z-10 flex flex-col justify-between"
            >
                <img src="<?php echo e(media_url($s->image, 'images/hero_student.png')); ?>" alt="<?php echo e($s->title); ?>" class="absolute inset-0 w-full h-full object-cover animate-ken-burns">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/50 to-slate-950/30"></div>

                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full my-auto py-10 lg:py-20 relative z-20 flex flex-col lg:grid lg:grid-cols-12 gap-6 items-center text-center lg:text-left">
                    <div class="lg:col-span-8 space-y-4 sm:space-y-6 max-w-xl mx-auto lg:mx-0 flex flex-col items-center lg:items-start">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($s->track_label): ?>
                            <div class="inline-flex items-center gap-2.5 px-4 py-2 rounded-full bg-teal-500/20 border border-teal-400/30 text-teal-300 text-xs font-bold tracking-wide backdrop-blur-md animate-badge-pulse shadow-md">
                                <span class="w-2.5 h-2.5 rounded-full bg-orange-500 animate-pulse"></span>
                                <span><?php echo e($s->getLocalizedTrackLabel()); ?></span>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <h1 class="font-heading font-extrabold text-[28px] sm:text-[34px] md:text-5xl lg:text-7xl text-white tracking-tight leading-snug lg:leading-[1.1] drop-shadow-md text-center lg:text-left line-clamp-2">
                            <?php echo e($s->getLocalizedTitle()); ?>

                        </h1>

                        <p class="text-slate-200 text-[16px] sm:text-[18px] lg:text-xl font-medium leading-relaxed max-w-xl drop-shadow-sm text-center lg:text-left">
                            <?php echo e($s->getLocalizedSubtitle()); ?>

                        </p>

                        <div class="flex flex-col sm:flex-row items-center gap-3.5 pt-2 w-full max-w-md lg:max-w-none">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($s->cta_primary_url): ?>
                                <a href="<?php echo e($s->cta_primary_url); ?>" class="btn-mobile-lg btn-lift group w-full sm:w-auto inline-flex items-center justify-center gap-3 px-8 text-slate-950 bg-gradient-to-r from-teal-400 to-teal-500 hover:from-teal-300 hover:to-teal-400 shadow-lg shadow-teal-500/25">
                                    <span><?php echo e(__('Explore Now')); ?></span>
                                    <span class="group-hover:translate-x-1.5 transition-transform duration-300">&rarr;</span>
                                </a>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($s->cta_secondary_url): ?>
                                <a href="<?php echo e($s->cta_secondary_url); ?>" class="btn-mobile-lg btn-lift w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 text-white bg-white/12 hover:bg-white/20 border border-white/30 backdrop-blur-md shadow-md shadow-white/10">
                                    <span><?php echo e(__('Learn More')); ?></span>
                                </a>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>

                    <div class="lg:col-span-4 w-full max-w-md mx-auto grid grid-cols-2 lg:flex lg:flex-col gap-3 pt-4 lg:pt-0">
                        <div class="bg-white/95 backdrop-blur-md border border-white/70 rounded-3xl shadow-xl p-3.5 sm:p-4 flex items-center gap-3">
                            <div class="w-11 h-11 sm:w-14 sm:h-14 rounded-2xl bg-teal-100 text-teal-700 flex items-center justify-center font-black text-xl sm:text-2xl shadow-xs shrink-0">120+</div>
                            <div class="text-left">
                                <p class="text-[9px] sm:text-[10px] font-mono uppercase tracking-widest font-semibold text-slate-400"><?php echo e(__('home.stat_accredited')); ?></p>
                                <p class="text-xs sm:text-base font-bold text-slate-900 leading-tight"><?php echo e(__('home.stat_courses')); ?></p>
                            </div>
                        </div>
                        <div class="bg-white/95 backdrop-blur-md border border-white/70 rounded-3xl shadow-xl p-3.5 sm:p-4 flex items-center gap-3">
                            <div class="w-11 h-11 sm:w-14 sm:h-14 rounded-2xl bg-orange-100 text-orange-600 flex items-center justify-center font-black text-xl sm:text-2xl shadow-xs shrink-0">25K+</div>
                            <div class="text-left">
                                <p class="text-[9px] sm:text-[10px] font-mono uppercase tracking-widest font-semibold text-slate-400"><?php echo e(__('home.stat_global')); ?></p>
                                <p class="text-xs sm:text-base font-bold text-slate-900 leading-tight"><?php echo e(__('home.stat_students')); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    <?php else: ?>
        
        <div 
            x-show="activeSlide === 0" 
            x-transition:enter="transition ease-out duration-700" 
            x-transition:enter-start="opacity-0 scale-98" 
            x-transition:enter-end="opacity-100 scale-100" 
            x-transition:leave="transition ease-in duration-400" 
            x-transition:leave-start="opacity-100" 
            x-transition:leave-end="opacity-0"
            class="absolute inset-0 z-10 flex flex-col justify-between"
        >
            <img src="<?php echo e(asset('images/hero_student.png')); ?>" alt="Programming & Tech Lab" class="absolute inset-0 w-full h-full object-cover animate-ken-burns">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/50 to-slate-950/30"></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full my-auto py-10 lg:py-20 relative z-20 flex flex-col lg:grid lg:grid-cols-12 gap-6 items-center text-center lg:text-left">
                <div class="lg:col-span-8 space-y-4 sm:space-y-6 max-w-xl mx-auto lg:mx-0 flex flex-col items-center lg:items-start">
                    <div class="inline-flex items-center gap-2.5 px-4 py-2 rounded-full bg-teal-500/20 border border-teal-400/30 text-teal-300 text-xs font-bold tracking-wide backdrop-blur-md animate-badge-pulse shadow-md">
                        <span class="w-2.5 h-2.5 rounded-full bg-orange-500 animate-pulse"></span>
                        <span><?php echo e(SiteSetting::getLocalized('landing_hero_badge', '🚀 EGYPT’S #1 ACADEMIC PLATFORM')); ?></span>
                    </div>

                    <h1 class="font-heading font-extrabold text-[28px] sm:text-[34px] md:text-5xl lg:text-7xl text-white tracking-tight leading-snug lg:leading-[1.1] drop-shadow-md text-center lg:text-left line-clamp-2">
                        <?php echo e(SiteSetting::getLocalized('landing_hero_title', 'Empowering Future Leaders with Practical Academic Excellence')); ?>

                    </h1>

                    <p class="text-slate-200 text-[16px] sm:text-[18px] lg:text-xl font-medium leading-relaxed max-w-xl drop-shadow-sm text-center lg:text-left">
                        <?php echo e(SiteSetting::getLocalized('landing_hero_subtitle', 'Join thousands of students learning Programming, Artificial Intelligence, Science, and Business from Egypt’s top educators.')); ?>

                    </p>

                    <div class="flex flex-col sm:flex-row items-center gap-3.5 pt-2 w-full max-w-md lg:max-w-none">
                        <a href="<?php echo e(SiteSetting::get('landing_cta_primary_link', '/subjects')); ?>" class="btn-mobile-lg btn-lift group w-full sm:w-auto inline-flex items-center justify-center gap-3 px-8 text-slate-950 bg-gradient-to-r from-teal-400 to-teal-500 hover:from-teal-300 hover:to-teal-400 shadow-lg shadow-teal-500/25">
                            <span><?php echo e(SiteSetting::getLocalized('landing_cta_primary_text', 'Explore All Subjects')); ?></span>
                            <span class="group-hover:translate-x-1.5 transition-transform duration-300">&rarr;</span>
                        </a>
                        <a href="<?php echo e(route('register')); ?>" class="btn-mobile-lg btn-lift w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 text-white bg-white/12 hover:bg-white/20 border border-white/30 backdrop-blur-md shadow-md shadow-white/10">
                            <span><?php echo e(__('Book Free Trial')); ?></span>
                        </a>
                    </div>
                </div>

                <div class="lg:col-span-4 w-full max-w-md mx-auto grid grid-cols-2 lg:flex lg:flex-col gap-3 pt-4 lg:pt-0">
                    <div class="bg-white/95 backdrop-blur-md border border-white/70 rounded-3xl shadow-xl p-3.5 sm:p-4 flex items-center gap-3">
                        <div class="w-11 h-11 sm:w-14 sm:h-14 rounded-2xl bg-teal-100 text-teal-700 flex items-center justify-center font-black text-xl sm:text-2xl shadow-xs shrink-0">120+</div>
                        <div class="text-left">
                            <p class="text-[9px] sm:text-[10px] font-mono uppercase tracking-widest font-semibold text-slate-400"><?php echo e(__('home.stat_accredited')); ?></p>
                            <p class="text-xs sm:text-base font-bold text-slate-900 leading-tight"><?php echo e(__('home.stat_courses')); ?></p>
                        </div>
                    </div>
                    <div class="bg-white/95 backdrop-blur-md border border-white/70 rounded-3xl shadow-xl p-3.5 sm:p-4 flex items-center gap-3">
                        <div class="w-11 h-11 sm:w-14 sm:h-14 rounded-2xl bg-orange-100 text-orange-600 flex items-center justify-center font-black text-xl sm:text-2xl shadow-xs shrink-0">25K+</div>
                        <div class="text-left">
                            <p class="text-[9px] sm:text-[10px] font-mono uppercase tracking-widest font-semibold text-slate-400"><?php echo e(__('home.stat_global')); ?></p>
                            <p class="text-xs sm:text-base font-bold text-slate-900 leading-tight"><?php echo e(__('home.stat_students')); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <div 
            x-show="activeSlide === 1" 
            x-transition:enter="transition ease-out duration-700" 
            x-transition:enter-start="opacity-0 scale-98" 
            x-transition:enter-end="opacity-100 scale-100" 
            x-transition:leave="transition ease-in duration-400" 
            x-transition:leave-start="opacity-100" 
            x-transition:leave-end="opacity-0"
            class="absolute inset-0 z-10 flex flex-col justify-between"
        >
            <img src="<?php echo e(asset('images/course_ai.png')); ?>" alt="AI Neural Networks Lab" class="absolute inset-0 w-full h-full object-cover animate-ken-burns">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/50 to-slate-950/30"></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full my-auto py-10 lg:py-20 relative z-20 flex flex-col lg:grid lg:grid-cols-12 gap-6 items-center text-center lg:text-left">
                <div class="lg:col-span-8 space-y-4 sm:space-y-6 max-w-xl mx-auto lg:mx-0 flex flex-col items-center lg:items-start">
                    <div class="inline-flex items-center gap-2.5 px-4 py-2 rounded-full bg-purple-500/20 border border-purple-400/30 text-purple-300 text-xs font-bold tracking-wide backdrop-blur-md animate-badge-pulse shadow-md">
                        <span class="w-2.5 h-2.5 rounded-full bg-purple-400 animate-pulse"></span>
                        <span>🧠 <?php echo e(__('Artificial Intelligence Track')); ?></span>
                    </div>

                    <h1 class="font-heading font-extrabold text-[28px] sm:text-[34px] md:text-5xl lg:text-7xl text-white tracking-tight leading-snug lg:leading-[1.1] drop-shadow-md text-center lg:text-left line-clamp-2">
                        <?php echo __('Learn Artificial Intelligence. <span class="text-purple-300 underline decoration-teal-400 underline-offset-8">Shape Tomorrow.</span>'); ?>

                    </h1>

                    <p class="text-slate-200 text-[16px] sm:text-[18px] lg:text-xl font-medium leading-relaxed max-w-xl drop-shadow-sm text-center lg:text-left">
                        <?php echo e(__('Explore Machine Learning, Deep Neural Networks, and modern computer vision.')); ?>

                    </p>

                    <div class="flex flex-col sm:flex-row items-center gap-3.5 pt-2 w-full max-w-md lg:max-w-none">
                        <a href="<?php echo e(route('subject-details')); ?>" class="btn-mobile-lg btn-lift group w-full sm:w-auto inline-flex items-center justify-center gap-3 px-8 text-white bg-gradient-to-r from-purple-600 to-purple-500 hover:from-purple-500 shadow-lg shadow-purple-600/25">
                            <span><?php echo e(__('Explore AI')); ?></span>
                            <span class="group-hover:translate-x-1.5 transition-transform duration-300">&rarr;</span>
                        </a>
                        <a href="<?php echo e(route('courses')); ?>" class="btn-mobile-lg btn-lift w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 text-white bg-white/12 hover:bg-white/20 border border-white/30 backdrop-blur-md shadow-md shadow-white/10">
                            <span><?php echo e(__('View Curriculum')); ?></span>
                        </a>
                    </div>
                </div>

                <div class="lg:col-span-4 w-full max-w-md mx-auto grid grid-cols-2 lg:flex lg:flex-col gap-3 pt-4 lg:pt-0">
                    <div class="bg-white/95 backdrop-blur-md border border-white/70 rounded-3xl shadow-xl p-3.5 sm:p-4 flex items-center gap-3">
                        <div class="w-11 h-11 sm:w-14 sm:h-14 rounded-2xl bg-purple-100 text-purple-700 flex items-center justify-center font-black text-xl sm:text-2xl shadow-xs shrink-0">50+</div>
                        <div class="text-left">
                            <p class="text-[9px] sm:text-[10px] font-mono uppercase tracking-widest font-semibold text-slate-400"><?php echo e(__('Autonomous')); ?></p>
                            <p class="text-xs sm:text-base font-bold text-slate-900 leading-tight"><?php echo e(__('AI Models')); ?></p>
                        </div>
                    </div>
                    <div class="bg-white/95 backdrop-blur-md border border-white/70 rounded-3xl shadow-xl p-3.5 sm:p-4 flex items-center gap-3">
                        <div class="w-11 h-11 sm:w-14 sm:h-14 rounded-2xl bg-teal-100 text-teal-600 flex items-center justify-center font-black text-xl sm:text-2xl shadow-xs shrink-0">Live</div>
                        <div class="text-left">
                            <p class="text-[9px] sm:text-[10px] font-mono uppercase tracking-widest font-semibold text-slate-400"><?php echo e(__('Hands-On')); ?></p>
                            <p class="text-xs sm:text-base font-bold text-slate-900 leading-tight"><?php echo e(__('Mentorship')); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <div 
            x-show="activeSlide === 2" 
            x-transition:enter="transition ease-out duration-700" 
            x-transition:enter-start="opacity-0 scale-98" 
            x-transition:enter-end="opacity-100 scale-100" 
            x-transition:leave="transition ease-in duration-400" 
            x-transition:leave-start="opacity-100" 
            x-transition:leave-end="opacity-0"
            class="absolute inset-0 z-10 flex flex-col justify-between"
        >
            <img src="<?php echo e(asset('images/instructor_male.png')); ?>" alt="Robotics Engineering Lab" class="absolute inset-0 w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/50 to-slate-950/30"></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full my-auto py-10 lg:py-20 relative z-20 flex flex-col lg:grid lg:grid-cols-12 gap-6 items-center text-center lg:text-left">
                <div class="lg:col-span-8 space-y-4 sm:space-y-6 max-w-xl mx-auto lg:mx-0 flex flex-col items-center lg:items-start">
                    <div class="inline-flex items-center gap-2.5 px-4 py-2 rounded-full bg-orange-500/20 border border-orange-400/30 text-orange-300 text-xs font-bold tracking-wide backdrop-blur-md animate-badge-pulse shadow-md">
                        <span class="w-2.5 h-2.5 rounded-full bg-orange-400 animate-pulse"></span>
                        <span>🤖 <?php echo e(__('Robotics Track')); ?></span>
                    </div>

                    <h1 class="font-heading font-extrabold text-[28px] sm:text-[34px] md:text-5xl lg:text-7xl text-white tracking-tight leading-snug lg:leading-[1.1] drop-shadow-md text-center lg:text-left line-clamp-2">
                        <?php echo __('Build. Create. <span class="text-orange-300 underline decoration-purple-400 underline-offset-8">Innovate.</span>'); ?>

                    </h1>

                    <p class="text-slate-200 text-[16px] sm:text-[18px] lg:text-xl font-medium leading-relaxed max-w-xl drop-shadow-sm text-center lg:text-left">
                        <?php echo e(__('Design real robots and autonomous engineering hardware inside state-of-the-art labs.')); ?>

                    </p>

                    <div class="flex flex-col sm:flex-row items-center gap-3.5 pt-2 w-full max-w-md lg:max-w-none">
                        <a href="<?php echo e(route('subjects')); ?>" class="btn-mobile-lg btn-lift group w-full sm:w-auto inline-flex items-center justify-center gap-3 px-8 text-white bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-400 shadow-lg shadow-orange-500/25">
                            <span><?php echo e(__('Explore Robotics')); ?></span>
                            <span class="group-hover:translate-x-1.5 transition-transform duration-300">&rarr;</span>
                        </a>
                        <a href="<?php echo e(route('event-details')); ?>" class="btn-mobile-lg btn-lift w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 text-white bg-white/12 hover:bg-white/20 border border-white/30 backdrop-blur-md shadow-md shadow-white/10">
                            <span><?php echo e(__('Join Workshop')); ?></span>
                        </a>
                    </div>
                </div>

                <div class="lg:col-span-4 w-full max-w-md mx-auto grid grid-cols-2 lg:flex lg:flex-col gap-3 pt-4 lg:pt-0">
                    <div class="bg-white/95 backdrop-blur-md border border-white/70 rounded-3xl shadow-xl p-3.5 sm:p-4 flex items-center gap-3">
                        <div class="w-11 h-11 sm:w-14 sm:h-14 rounded-2xl bg-orange-100 text-orange-700 flex items-center justify-center font-black text-xl sm:text-2xl shadow-xs shrink-0">50+</div>
                        <div class="text-left">
                            <p class="text-[9px] sm:text-[10px] font-mono uppercase tracking-widest font-semibold text-slate-400"><?php echo e(__('Autonomous')); ?></p>
                            <p class="text-xs sm:text-base font-bold text-slate-900 leading-tight"><?php echo e(__('Robotics Projects')); ?></p>
                        </div>
                    </div>
                    <div class="bg-white/95 backdrop-blur-md border border-white/70 rounded-3xl shadow-xl p-3.5 sm:p-4 flex items-center gap-3">
                        <div class="w-11 h-11 sm:w-14 sm:h-14 rounded-2xl bg-teal-100 text-teal-600 flex items-center justify-center font-black text-xl sm:text-2xl shadow-xs shrink-0">Live</div>
                        <div class="text-left">
                            <p class="text-[9px] sm:text-[10px] font-mono uppercase tracking-widest font-semibold text-slate-400"><?php echo e(__('Hands-On')); ?></p>
                            <p class="text-xs sm:text-base font-bold text-slate-900 leading-tight"><?php echo e(__('Workshops')); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <div 
            x-show="activeSlide === 3" 
            x-transition:enter="transition ease-out duration-700" 
            x-transition:enter-start="opacity-0 scale-98" 
            x-transition:enter-end="opacity-100 scale-100" 
            x-transition:leave="transition ease-in duration-400" 
            x-transition:leave-start="opacity-100" 
            x-transition:leave-end="opacity-0"
            class="absolute inset-0 z-10 flex flex-col justify-between"
        >
            <img src="<?php echo e(asset('images/academy_campus.png')); ?>" alt="Science Laboratory" class="absolute inset-0 w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/50 to-slate-950/30"></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full my-auto py-10 lg:py-20 relative z-20 flex flex-col lg:grid lg:grid-cols-12 gap-6 items-center text-center lg:text-left">
                <div class="lg:col-span-8 space-y-4 sm:space-y-6 max-w-xl mx-auto lg:mx-0 flex flex-col items-center lg:items-start">
                    <div class="inline-flex items-center gap-2.5 px-4 py-2 rounded-full bg-teal-500/20 border border-teal-400/30 text-teal-300 text-xs font-bold tracking-wide backdrop-blur-md animate-badge-pulse shadow-md">
                        <span class="w-2.5 h-2.5 rounded-full bg-teal-400 animate-pulse"></span>
                        <span>🔬 <?php echo e(__('Science & Math Track')); ?></span>
                    </div>

                    <h1 class="font-heading font-extrabold text-[28px] sm:text-[34px] md:text-5xl lg:text-7xl text-white tracking-tight leading-snug lg:leading-[1.1] drop-shadow-md text-center lg:text-left line-clamp-2">
                        <?php echo __('Curiosity Creates <span class="text-teal-300 underline decoration-orange-500 underline-offset-8">Excellence.</span>'); ?>

                    </h1>

                    <p class="text-slate-200 text-[16px] sm:text-[18px] lg:text-xl font-medium leading-relaxed max-w-xl drop-shadow-sm text-center lg:text-left">
                        <?php echo e(__('Interactive science and mathematics education designed to build problem-solving mindsets.')); ?>

                    </p>

                    <div class="flex flex-col sm:flex-row items-center gap-3.5 pt-2 w-full max-w-md lg:max-w-none">
                        <a href="<?php echo e(route('subjects')); ?>" class="btn-mobile-lg btn-lift group w-full sm:w-auto inline-flex items-center justify-center gap-3 px-8 text-slate-950 bg-gradient-to-r from-teal-400 to-teal-500 hover:from-teal-300 shadow-lg shadow-teal-500/25">
                            <span><?php echo e(__('Explore Science')); ?></span>
                            <span class="group-hover:translate-x-1.5 transition-transform duration-300">&rarr;</span>
                        </a>
                        <a href="<?php echo e(route('register')); ?>" class="btn-mobile-lg btn-lift w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 text-white bg-white/12 hover:bg-white/20 border border-white/30 backdrop-blur-md shadow-md shadow-white/10">
                            <span><?php echo e(__('Book Trial')); ?></span>
                        </a>
                    </div>
                </div>

                <div class="lg:col-span-4 w-full max-w-md mx-auto grid grid-cols-2 lg:flex lg:flex-col gap-3 pt-4 lg:pt-0">
                    <div class="bg-white/95 backdrop-blur-md border border-white/70 rounded-3xl shadow-xl p-3.5 sm:p-4 flex items-center gap-3">
                        <div class="w-11 h-11 sm:w-14 sm:h-14 rounded-2xl bg-teal-100 text-teal-700 flex items-center justify-center font-black text-xl sm:text-2xl shadow-xs shrink-0">120+</div>
                        <div class="text-left">
                            <p class="text-[9px] sm:text-[10px] font-mono uppercase tracking-widest font-semibold text-slate-400"><?php echo e(__('home.stat_accredited')); ?></p>
                            <p class="text-xs sm:text-base font-bold text-slate-900 leading-tight"><?php echo e(__('home.stat_courses')); ?></p>
                        </div>
                    </div>
                    <div class="bg-white/95 backdrop-blur-md border border-white/70 rounded-3xl shadow-xl p-3.5 sm:p-4 flex items-center gap-3">
                        <div class="w-11 h-11 sm:w-14 sm:h-14 rounded-2xl bg-orange-100 text-orange-600 flex items-center justify-center font-black text-xl sm:text-2xl shadow-xs shrink-0">25K+</div>
                        <div class="text-left">
                            <p class="text-[9px] sm:text-[10px] font-mono uppercase tracking-widest font-semibold text-slate-400"><?php echo e(__('home.stat_global')); ?></p>
                            <p class="text-xs sm:text-base font-bold text-slate-900 leading-tight"><?php echo e(__('home.stat_students')); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <div class="relative z-30 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full pb-8 flex items-center justify-between border-t border-white/15 pt-6">
        <div class="font-mono text-sm font-bold text-slate-200 flex items-center gap-4">
            <span class="text-teal-400 text-xl font-extrabold tracking-wider" x-text="String(activeSlide + 1).padStart(2, '0')">01</span>
            <div class="w-32 sm:w-48 h-1.5 bg-white/20 rounded-full relative overflow-hidden">
                <div class="absolute top-0 bottom-0 left-0 bg-teal-400 rounded-full transition-all duration-500" :style="'width: ' + (((activeSlide + 1) / totalSlides) * 100) + '%'"></div>
            </div>
            <span class="text-slate-400 text-sm" x-text="String(totalSlides).padStart(2, '0')">04</span>
        </div>

        <div class="flex items-center gap-4">
            <div class="hidden sm:flex items-center gap-2.5">
                <template x-for="i in totalSlides" :key="i">
                    <button 
                        type="button" 
                        @click="goToSlide(i - 1)" 
                        :class="activeSlide === (i - 1) ? 'bg-teal-400 w-7' : 'bg-white/30 w-3 hover:bg-white/70'" 
                        class="h-3 rounded-full transition-all duration-300 cursor-pointer" 
                        :aria-label="'Go to slide ' + i"
                    ></button>
                </template>
            </div>
            <div class="flex items-center gap-2.5">
                <button type="button" @click="prevSlide()" class="btn-lift w-10 h-10 rounded-full bg-white/12 border border-white/25 flex items-center justify-center text-white cursor-pointer hover:bg-teal-500/40 shadow-sm backdrop-blur-md" aria-label="Previous Slide">&larr;</button>
                <button type="button" @click="nextSlide()" class="btn-lift w-10 h-10 rounded-full bg-white/12 border border-white/25 flex items-center justify-center text-white cursor-pointer hover:bg-teal-500/40 shadow-sm backdrop-blur-md" aria-label="Next Slide">&rarr;</button>
            </div>
        </div>
    </div>
</section>
<?php /**PATH C:\laragon\www\elite-academy\resources\views\pages\home\hero-slider.blade.php ENDPATH**/ ?>