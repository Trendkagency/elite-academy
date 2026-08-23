<?php use \App\Models\SiteSetting; ?>


<div class="absolute top-28 left-[10%] w-4 h-4 border-2 border-teal-500/30 rounded-full animate-drift pointer-events-none -z-10"></div>
<div class="absolute top-96 right-[12%] w-6 h-6 border-2 border-orange-500/20 rounded-md rotate-12 animate-float pointer-events-none -z-10"></div>

<?php
    $dbHeroSlides = \Illuminate\Support\Facades\Schema::hasTable('hero_slides')
        ? \App\Models\HeroSlide::where('is_active', true)->orderBy('sort_order')->get()
        : collect();
?>

<section class="w-full min-h-[75vh] lg:min-h-[95vh] relative overflow-hidden bg-slate-950 text-white flex flex-col justify-between hero-section">

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($dbHeroSlides->count() > 0): ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $dbHeroSlides; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <input type="radio" name="hero-slide" id="slide-<?php echo e($idx + 1); ?>" class="peer/s<?php echo e($idx + 1); ?> hidden" <?php echo e($idx === 0 ? 'checked' : ''); ?>>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    <?php else: ?>
        <input type="radio" name="hero-slide" id="slide-1" class="peer/s1 hidden" checked>
        <input type="radio" name="hero-slide" id="slide-2" class="peer/s2 hidden">
        <input type="radio" name="hero-slide" id="slide-3" class="peer/s3 hidden">
        <input type="radio" name="hero-slide" id="slide-4" class="peer/s4 hidden">
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <div class="absolute -top-24 -left-24 w-[40rem] h-[40rem] bg-teal-500/20 rounded-full blur-3xl pointer-events-none z-0 animate-pulse-glow"></div>
    <div class="absolute bottom-0 right-0 w-[45rem] h-[45rem] bg-orange-500/15 rounded-full blur-3xl pointer-events-none z-0 animate-float"></div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($dbHeroSlides->count() > 0): ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $dbHeroSlides; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <div class="absolute inset-0 z-10 hidden peer-checked/s<?php echo e($idx + 1); ?>:flex flex-col justify-between transition-all duration-700">
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
                </div>
            </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    <?php else: ?>
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
                        <span><?php echo e(SiteSetting::getLocalized('landing_cta_primary_text', 'Explore All Subjects →')); ?></span>
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

    
    <div class="absolute inset-0 z-10 hidden peer-checked/s2:flex flex-col justify-between transition-all duration-700">
        <img src="<?php echo e(asset('images/course_ai.png')); ?>" alt="AI Neural Networks Lab" class="absolute inset-0 w-full h-full object-cover animate-ken-burns">
        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/50 to-slate-950/30"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full my-auto py-10 lg:py-20 relative z-20 flex flex-col lg:grid lg:grid-cols-12 gap-6 items-center text-center lg:text-left">
            <div class="lg:col-span-8 space-y-4 sm:space-y-6 max-w-xl mx-auto lg:mx-0 flex flex-col items-center lg:items-start">
                <div class="inline-flex items-center gap-2.5 px-4 py-2 rounded-full bg-purple-500/20 border border-purple-400/30 text-purple-300 text-xs font-bold tracking-wide backdrop-blur-md animate-badge-pulse shadow-md">
                    <span class="w-2.5 h-2.5 rounded-full bg-purple-400 animate-pulse"></span>
                    <span>🧠 Artificial Intelligence Track</span>
                </div>

                <h1 class="font-heading font-extrabold text-[28px] sm:text-[34px] md:text-5xl lg:text-7xl text-white tracking-tight leading-snug lg:leading-[1.1] drop-shadow-md text-center lg:text-left line-clamp-2">
                    Learn Artificial Intelligence. <span class="text-purple-300 underline decoration-teal-400 underline-offset-8">Shape Tomorrow.</span>
                </h1>

                <p class="text-slate-200 text-[16px] sm:text-[18px] lg:text-xl font-medium leading-relaxed max-w-xl drop-shadow-sm text-center lg:text-left">
                    Explore Machine Learning, Deep Neural Networks, and modern computer vision.
                </p>

                <div class="flex flex-col sm:flex-row items-center gap-3.5 pt-2 w-full max-w-md lg:max-w-none">
                    <a href="<?php echo e(route('subject-details')); ?>" class="btn-mobile-lg btn-lift group w-full sm:w-auto inline-flex items-center justify-center gap-3 px-8 text-white bg-gradient-to-r from-purple-600 to-purple-500 hover:from-purple-500 shadow-lg shadow-purple-600/25">
                        <span>Explore AI</span>
                        <span class="group-hover:translate-x-1.5 transition-transform duration-300">&rarr;</span>
                    </a>
                    <a href="<?php echo e(route('courses')); ?>" class="btn-mobile-lg btn-lift w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 text-white bg-white/12 hover:bg-white/20 border border-white/30 backdrop-blur-md shadow-md shadow-white/10">
                        <span>View Curriculum</span>
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

    
    <div class="absolute inset-0 z-10 hidden peer-checked/s3:flex flex-col justify-between transition-all duration-700">
        <img src="<?php echo e(asset('images/instructor_male.png')); ?>" alt="Robotics Engineering Lab" class="absolute inset-0 w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/50 to-slate-950/30"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full my-auto py-10 lg:py-20 relative z-20 flex flex-col lg:grid lg:grid-cols-12 gap-6 items-center text-center lg:text-left">
            <div class="lg:col-span-8 space-y-4 sm:space-y-6 max-w-xl mx-auto lg:mx-0 flex flex-col items-center lg:items-start">
                <div class="inline-flex items-center gap-2.5 px-4 py-2 rounded-full bg-orange-500/20 border border-orange-400/30 text-orange-300 text-xs font-bold tracking-wide backdrop-blur-md animate-badge-pulse shadow-md">
                    <span class="w-2.5 h-2.5 rounded-full bg-orange-400 animate-pulse"></span>
                    <span>🤖 Robotics Track</span>
                </div>

                <h1 class="font-heading font-extrabold text-[28px] sm:text-[34px] md:text-5xl lg:text-7xl text-white tracking-tight leading-snug lg:leading-[1.1] drop-shadow-md text-center lg:text-left line-clamp-2">
                    Build. Create. <span class="text-orange-300 underline decoration-purple-400 underline-offset-8">Innovate.</span>
                </h1>

                <p class="text-slate-200 text-[16px] sm:text-[18px] lg:text-xl font-medium leading-relaxed max-w-xl drop-shadow-sm text-center lg:text-left">
                    Design real robots and autonomous engineering hardware inside state-of-the-art labs.
                </p>

                <div class="flex flex-col sm:flex-row items-center gap-3.5 pt-2 w-full max-w-md lg:max-w-none">
                    <a href="<?php echo e(route('subjects')); ?>" class="btn-mobile-lg btn-lift group w-full sm:w-auto inline-flex items-center justify-center gap-3 px-8 text-white bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-400 shadow-lg shadow-orange-500/25">
                        <span>Explore Robotics</span>
                        <span class="group-hover:translate-x-1.5 transition-transform duration-300">&rarr;</span>
                    </a>
                    <a href="<?php echo e(route('event-details')); ?>" class="btn-mobile-lg btn-lift w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 text-white bg-white/12 hover:bg-white/20 border border-white/30 backdrop-blur-md shadow-md shadow-white/10">
                        <span>Join Workshop</span>
                    </a>
                </div>
            </div>

            <div class="lg:col-span-4 w-full max-w-md mx-auto grid grid-cols-2 lg:flex lg:flex-col gap-3 pt-4 lg:pt-0">
                <div class="glass-card bg-white/15 backdrop-blur-lg p-3.5 sm:p-4 rounded-3xl border border-white/25 shadow-xl flex items-center gap-3">
                    <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-orange-500/30 text-orange-300 flex items-center justify-center font-extrabold text-base border border-orange-400/40 shrink-0">50+</div>
                    <div class="text-left">
                        <p class="text-[9px] sm:text-xs font-mono uppercase text-slate-300 font-bold">Autonomous</p>
                        <p class="text-xs sm:text-sm font-extrabold text-white leading-tight">Robotics Projects</p>
                    </div>
                </div>
                <div class="glass-card bg-white/15 backdrop-blur-lg p-3.5 sm:p-4 rounded-3xl border border-white/25 shadow-xl flex items-center gap-3">
                    <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-teal-500/30 text-teal-300 flex items-center justify-center font-extrabold text-base border border-teal-400/40 shrink-0">Live</div>
                    <div class="text-left">
                        <p class="text-[9px] sm:text-xs font-mono uppercase text-slate-300 font-bold">Hands-On</p>
                        <p class="text-xs sm:text-sm font-extrabold text-white leading-tight">Workshops</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="absolute inset-0 z-10 hidden peer-checked/s4:flex flex-col justify-between transition-all duration-700">
        <img src="<?php echo e(asset('images/academy_campus.png')); ?>" alt="Science Laboratory" class="absolute inset-0 w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/50 to-slate-950/30"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full my-auto py-10 lg:py-20 relative z-20 flex flex-col lg:grid lg:grid-cols-12 gap-6 items-center text-center lg:text-left">
            <div class="lg:col-span-8 space-y-4 sm:space-y-6 max-w-xl mx-auto lg:mx-0 flex flex-col items-center lg:items-start">
                <div class="inline-flex items-center gap-2.5 px-4 py-2 rounded-full bg-teal-500/20 border border-teal-400/30 text-teal-300 text-xs font-bold tracking-wide backdrop-blur-md animate-badge-pulse shadow-md">
                    <span class="w-2.5 h-2.5 rounded-full bg-teal-400 animate-pulse"></span>
                    <span>🔬 Science & Math Track</span>
                </div>

                <h1 class="font-heading font-extrabold text-[28px] sm:text-[34px] md:text-5xl lg:text-7xl text-white tracking-tight leading-snug lg:leading-[1.1] drop-shadow-md text-center lg:text-left line-clamp-2">
                    Curiosity Creates <span class="text-teal-300 underline decoration-orange-500 underline-offset-8">Excellence.</span>
                </h1>

                <p class="text-slate-200 text-[16px] sm:text-[18px] lg:text-xl font-medium leading-relaxed max-w-xl drop-shadow-sm text-center lg:text-left">
                    Interactive science and mathematics education designed to build problem-solving mindsets.
                </p>

                <div class="flex flex-col sm:flex-row items-center gap-3.5 pt-2 w-full max-w-md lg:max-w-none">
                    <a href="<?php echo e(route('subjects')); ?>" class="btn-mobile-lg btn-lift group w-full sm:w-auto inline-flex items-center justify-center gap-3 px-8 text-slate-950 bg-gradient-to-r from-teal-400 to-teal-500 hover:from-teal-300 shadow-lg shadow-teal-500/25">
                        <span>Explore Science</span>
                        <span class="group-hover:translate-x-1.5 transition-transform duration-300">&rarr;</span>
                    </a>
                    <a href="<?php echo e(route('register')); ?>" class="btn-mobile-lg btn-lift w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 text-white bg-white/12 hover:bg-white/20 border border-white/30 backdrop-blur-md shadow-md shadow-white/10">
                        <span>Book Trial</span>
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
            <span class="text-teal-400 text-xl font-extrabold tracking-wider">
                <span class="hidden peer-checked/s1:inline">01</span>
                <span class="hidden peer-checked/s2:inline">02</span>
                <span class="hidden peer-checked/s3:inline">03</span>
                <span class="hidden peer-checked/s4:inline">04</span>
            </span>
            <div class="w-32 sm:w-48 h-1.5 bg-white/20 rounded-full relative overflow-hidden">
                <div class="absolute top-0 bottom-0 left-0 bg-teal-400 rounded-full transition-all duration-500 peer-checked/s1:w-1/4 peer-checked/s2:w-2/4 peer-checked/s3:w-3/4 peer-checked/s4:w-full"></div>
            </div>
            <span class="text-slate-400 text-sm">04</span>
        </div>

        <div class="flex items-center gap-4">
            <div class="hidden sm:flex items-center gap-2.5">
                <label for="slide-1" class="w-3 h-3 rounded-full bg-white/30 peer-checked/s1:bg-teal-400 peer-checked/s1:w-7 cursor-pointer transition-all duration-300 hover:bg-white/70" title="Slide 01"></label>
                <label for="slide-2" class="w-3 h-3 rounded-full bg-white/30 peer-checked/s2:bg-purple-400 peer-checked/s2:w-7 cursor-pointer transition-all duration-300 hover:bg-white/70" title="Slide 02"></label>
                <label for="slide-3" class="w-3 h-3 rounded-full bg-white/30 peer-checked/s3:bg-orange-400 peer-checked/s3:w-7 cursor-pointer transition-all duration-300 hover:bg-white/70" title="Slide 03"></label>
                <label for="slide-4" class="w-3 h-3 rounded-full bg-white/30 peer-checked/s4:bg-teal-400 peer-checked/s4:w-7 cursor-pointer transition-all duration-300 hover:bg-white/70" title="Slide 04"></label>
            </div>
            <div class="flex items-center gap-2.5">
                <label for="slide-1" class="btn-lift w-10 h-10 rounded-full bg-white/12 border border-white/25 flex items-center justify-center text-white cursor-pointer hover:bg-teal-500/40 shadow-sm backdrop-blur-md">&larr;</label>
                <label for="slide-2" class="btn-lift w-10 h-10 rounded-full bg-white/12 border border-white/25 flex items-center justify-center text-white cursor-pointer hover:bg-teal-500/40 shadow-sm backdrop-blur-md">&rarr;</label>
            </div>
        </div>
    </div>

</section>
<?php /**PATH C:\laragon\www\elite-academy\resources\views/pages/home/hero-slider.blade.php ENDPATH**/ ?>