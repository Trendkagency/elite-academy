<?php use \App\Models\SiteSetting; ?>

<section class="py-16 md:py-24 lg:py-32 bg-slate-950 text-white relative overflow-hidden border-y border-slate-800/80 transition-colors duration-200">
    <div class="absolute top-1/2 -left-24 w-[36rem] h-[36rem] bg-teal-500/15 rounded-full blur-2xl pointer-events-none -z-10"></div>
    <div class="absolute bottom-10 right-0 w-[32rem] h-[32rem] bg-orange-500/10 rounded-full blur-2xl pointer-events-none -z-10"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        
        <div class="block md:hidden space-y-6 anim-about delay-1">
            <div class="space-y-2">
                <span class="inline-block text-xs font-mono uppercase tracking-widest text-teal-300 font-extrabold bg-teal-950/80 px-3.5 py-1.5 rounded-full border border-teal-500/40">
                    <?php echo e(SiteSetting::getLocalized('about_badge', 'REDEFINING EDUCATION')); ?>

                </span>
                <h2 class="font-heading text-2xl sm:text-3xl font-black text-white tracking-tight leading-tight">
                    <?php echo e(SiteSetting::getLocalized('about_title', 'Where Passion Meets Academic Mastery')); ?>

                </h2>
                <p class="text-slate-300 text-sm font-medium leading-relaxed line-clamp-2">
                    <?php echo e(SiteSetting::getLocalized('about_content', 'Elite Academy bridges secondary education and real-world innovation through interactive live streams, structured MCQs, and expert teacher mentorship.')); ?>

                </p>
            </div>

            <div class="flex flex-row items-center gap-4">
                <div class="w-[40%] shrink-0">
                    <div class="relative rounded-2xl overflow-hidden aspect-[4/5] border border-slate-800 shadow-md">
                        <img src="<?php echo e(media_url(\App\Models\SiteSetting::get('about_image'), 'images/academy_campus.webp')); ?>" alt="Elite Academy Campus" width="300" height="375" class="w-full h-full object-cover" loading="lazy" decoding="async">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/70 via-transparent to-transparent"></div>
                        <span class="absolute bottom-2 left-2 text-[9px] font-mono font-bold text-white bg-slate-900/90 px-2 py-0.5 rounded-md backdrop-blur-xs"><?php echo e(__('Campus')); ?></span>
                    </div>
                </div>

                <div class="w-[60%] space-y-3.5">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = [
                        ['icon' => '🎓', 'title' => __('Expert Mentors'), 'desc' => __('PhD faculty guidance.')],
                        ['icon' => '💻', 'title' => __('Practical Learning'), 'desc' => __('Hands-on lab projects.')],
                        ['icon' => '🌍', 'title' => __('Global Certificates'), 'desc' => __('Accredited diplomas.')],
                        ['icon' => '🚀', 'title' => __('Career Support'), 'desc' => __('Job readiness tracks.')],
                    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <div class="flex items-start gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-slate-900 text-teal-300 flex items-center justify-center text-base font-bold shrink-0 border border-slate-800">
                                <?php echo e($feat['icon']); ?>

                            </div>
                            <div class="min-w-0">
                                <h3 class="font-heading font-extrabold text-xs text-white leading-tight"><?php echo e($feat['title']); ?></h3>
                                <p class="text-[10px] text-slate-400 font-medium leading-tight truncate"><?php echo e($feat['desc']); ?></p>
                            </div>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            </div>

            <div>
                <a href="<?php echo e(route('about')); ?>" class="btn-lift w-full inline-flex items-center justify-center gap-2 text-sm font-extrabold text-slate-950 bg-teal-400 hover:bg-teal-300 min-h-[52px] rounded-2xl shadow-lg touch-press" aria-label="Learn more about Elite Academy mission and programs">
                    <span><?php echo e(__('About Academy & Programs')); ?></span>
                    <span>&rarr;</span>
                </a>
            </div>
        </div>

        
        <div class="hidden md:grid md:grid-cols-12 md:gap-10 lg:gap-16 md:items-center">
            <div class="md:col-span-6 flex flex-col space-y-6 lg:space-y-8">
                <div class="space-y-3 sm:space-y-4">
                    <span class="anim-about delay-1 sr-h inline-block text-xs font-mono uppercase tracking-widest text-teal-300 font-extrabold bg-teal-950/80 px-3.5 py-1.5 rounded-full border border-teal-500/40">
                        <?php echo e(SiteSetting::getLocalized('about_badge', 'REDEFINING EDUCATION')); ?>

                    </span>
                    <h2 class="anim-about delay-2 sr-h font-heading text-3xl sm:text-4xl lg:text-5xl xl:text-6xl font-black text-white tracking-tight leading-[1.15]">
                        <?php echo e(SiteSetting::getLocalized('about_title', 'Where Passion Meets Academic Mastery')); ?>

                    </h2>
                    <p class="anim-about delay-3 sr-img text-slate-300 text-sm sm:text-base font-medium leading-relaxed">
                        <?php echo e(SiteSetting::getLocalized('about_content', 'Elite Academy bridges secondary education and real-world innovation through interactive live streams, structured MCQs, and expert teacher mentorship.')); ?>

                    </p>
                </div>

                <div class="anim-about delay-4 sr grid grid-cols-2 gap-4 border-t border-slate-800 pt-6">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = [
                        ['icon' => '🎓', 'title' => __('Expert Mentors')],
                        ['icon' => '💻', 'title' => __('Practical Learning')],
                        ['icon' => '🌍', 'title' => __('Global Certificates')],
                        ['icon' => '🚀', 'title' => __('Career Support')],
                    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <div class="flex items-center gap-3 group">
                            <div class="w-10 h-10 rounded-xl bg-slate-900 text-teal-300 group-hover:text-teal-200 group-hover:scale-110 flex items-center justify-center font-extrabold text-xl transition-all duration-300 shadow-xs flex-shrink-0 border border-slate-800">
                                <?php echo e($item['icon']); ?>

                            </div>
                            <h3 class="font-heading font-extrabold text-sm sm:text-base text-white group-hover:text-teal-300 transition-colors leading-tight">
                                <?php echo e($item['title']); ?>

                            </h3>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>

                <div class="anim-about delay-5 pt-1">
                    <a href="<?php echo e(route('about')); ?>" class="btn-lift group inline-flex items-center justify-center gap-2.5 text-sm sm:text-base font-extrabold text-slate-950 bg-teal-400 hover:bg-teal-300 active:bg-teal-500 px-8 py-4 rounded-2xl shadow-lg shadow-teal-500/25 touch-press" aria-label="Discover Elite Academy curriculum and campus">
                        <span><?php echo e(__('Discover Our Academy')); ?></span>
                        <span class="group-hover:translate-x-1.5 transition-transform duration-300">&rarr;</span>
                    </a>
                </div>
            </div>

            <div class="md:col-span-6 relative anim-about delay-2">
                <div class="relative max-w-lg lg:max-w-none mx-auto">
                    <div class="relative rounded-[36px] p-3 bg-slate-900 border border-slate-800 shadow-2xl shadow-slate-950/50 group overflow-hidden card-lift">
                        <div class="relative rounded-[26px] overflow-hidden">
                            <img src="<?php echo e(media_url(\App\Models\SiteSetting::get('about_image'), 'images/academy_campus.webp')); ?>" alt="Elite Academy Campus Photography" width="600" height="440" class="w-full h-80 sm:h-[400px] lg:h-[440px] object-cover group-hover:scale-[1.03] transition-transform duration-700 ease-out" loading="lazy" decoding="async">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-950/20 to-transparent pointer-events-none"></div>
                            <span class="absolute top-4 left-4 text-[10px] font-mono font-extrabold uppercase tracking-wider text-white bg-slate-950/80 px-3 py-1 rounded-full backdrop-blur-xs border border-white/20">
                                🏫 Campus Life
                            </span>
                        </div>
                    </div>

                    <div class="absolute -bottom-6 -right-4 lg:-bottom-8 lg:-right-6 w-40 sm:w-48 h-28 sm:h-32 rounded-2xl overflow-hidden border-2 border-orange-500 shadow-xl z-20 card-lift group">
                        <img src="<?php echo e(asset('images/course_ai.webp')); ?>" alt="AI Neural Research" width="192" height="128" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy" decoding="async">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/60 to-transparent pointer-events-none"></div>
                        <span class="absolute bottom-2 left-2.5 text-[9px] font-mono font-bold text-white bg-orange-600/90 px-2 py-0.5 rounded-full backdrop-blur-xs shadow-xs">
                            🧠 AI Research
                        </span>
                    </div>

                    <div class="hidden lg:block absolute top-1/3 -right-6 w-20 h-20 rounded-2xl overflow-hidden border-2 border-white/80 shadow-xl z-30 animate-float group">
                        <img src="<?php echo e(asset('images/instructor_portrait_128.webp')); ?>" alt="Faculty Mentor Session" width="80" height="80" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" loading="lazy" decoding="async">
                    </div>

                    <div class="absolute bottom-6 left-6 z-30 bg-slate-900/95 backdrop-blur-md p-4 rounded-2xl border border-slate-800 shadow-2xl shadow-slate-950/60 space-y-2 max-w-xs">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-teal-500/20 text-teal-300 flex items-center justify-center font-extrabold text-sm icon-rotate flex-shrink-0 shadow-xs border border-teal-500/30">
                                ⭐
                            </div>
                            <div>
                                <p class="text-[9px] font-mono uppercase tracking-wider text-slate-400 font-bold">Trusted by</p>
                                <p class="text-xs font-extrabold text-white">25,000+ Active Students</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-800 text-[11px]">
                            <div>
                                <p class="text-amber-400 font-extrabold">★★★★★ 4.9</p>
                                <p class="text-[9px] text-slate-400 font-medium">Verified Rating</p>
                            </div>
                            <div>
                                <p class="text-teal-300 font-extrabold">120+ Courses</p>
                                <p class="text-[9px] text-slate-400 font-medium">Accredited Tracks</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
<?php /**PATH C:\laragon\www\elite-academy\resources\views/pages/home/about-preview.blade.php ENDPATH**/ ?>