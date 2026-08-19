
<section class="py-24 lg:py-32 bg-[#FAFAF9] relative overflow-hidden border-b border-slate-200/80">
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[45rem] h-[25rem] bg-teal-400/10 rounded-full blur-3xl pointer-events-none -z-10 animate-pulse-glow"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12 relative z-10">

        
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-4 border-b border-slate-200/60">
            <div class="space-y-3 max-w-xl">
                <span class="anim-testimonials delay-1 sr-h inline-block text-xs font-mono uppercase tracking-widest text-teal-600 font-extrabold bg-teal-50 px-4 py-1.5 rounded-full border border-teal-200/80 shadow-xs">
                    STUDENT REVIEWS
                </span>
                <h2 class="anim-testimonials delay-2 sr-h font-heading text-3xl sm:text-4xl lg:text-5xl font-black text-slate-900 tracking-tight leading-tight">
                    What Our Students & <span class="text-teal-600 underline decoration-orange-500 underline-offset-8">Parents Say.</span>
                </h2>
            </div>

            <div class="flex items-center gap-4">
                <span class="text-xs font-mono text-slate-500 font-medium hidden sm:inline">&larr; Swipe Reviews &rarr;</span>
                <div class="flex items-center gap-2">
                    <button class="w-10 h-10 rounded-full bg-white border border-slate-200/90 shadow-md flex items-center justify-center text-slate-700 hover:text-teal-600 hover:border-teal-300 transition-all duration-300 active:scale-95 cursor-pointer">&larr;</button>
                    <button class="w-10 h-10 rounded-full bg-white border border-slate-200/90 shadow-md flex items-center justify-center text-slate-700 hover:text-teal-600 hover:border-teal-300 transition-all duration-300 active:scale-95 cursor-pointer">&rarr;</button>
                </div>
            </div>
        </div>

        
        <?php
            $dbTestimonials = \Illuminate\Support\Facades\Schema::hasTable('testimonials')
                ? \App\Models\Testimonial::where('is_featured', true)->orderBy('sort_order')->get()
                : collect();
            $testimonials = $dbTestimonials->count() > 0 ? $dbTestimonials->map(fn($t) => [
                'quote' => '"' . $t->content . '"',
                'photo' => $t->avatar ?: 'images/instructor_portrait.png',
                'name' => $t->name,
                'course' => $t->course_name ?: 'Elite Academic Track',
                'badge' => $t->is_verified ? '✔ Verified ' . ucfirst($t->reviewer_type) : ucfirst($t->reviewer_type),
                'quoteColor' => 'group-hover:text-teal-600',
                'nameColor' => 'group-hover:text-teal-600',
                'badgeBg' => 'bg-teal-50 text-teal-700 border-teal-200/80',
            ]) : [
                [
                    'quote' => '"Elite Academy completely transformed my son\'s approach to coding and math. Having direct access to PhD mentors made all the difference."',
                    'photo' => 'images/hero_student.png',
                    'name' => 'Mariam Al-Mansoor',
                    'course' => 'Full-Stack Programming',
                    'badge' => '✔ Verified Student',
                    'quoteColor' => 'group-hover:text-teal-600',
                    'nameColor' => 'group-hover:text-teal-600',
                    'badgeBg' => 'bg-teal-50 text-teal-700 border-teal-200/80',
                ],
                [
                    'quote' => '"The robotics and AI labs gave me real hands-on experience building computer vision models. I secured a software engineering role right after graduation!"',
                    'photo' => 'images/instructor_portrait.png',
                    'name' => 'Kareem El-Sayed',
                    'course' => 'AI & Machine Learning',
                    'badge' => '✔ Verified Student',
                    'quoteColor' => 'group-hover:text-purple-600',
                    'nameColor' => 'group-hover:text-purple-600',
                    'badgeBg' => 'bg-purple-50 text-purple-700 border-purple-200/80',
                ],
            ];
        ?>

        <div class="carousel-container no-scrollbar flex items-center gap-8 overflow-x-auto py-6 snap-x snap-mandatory scroll-smooth">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $testimonials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <div class="w-full max-w-[420px] sm:w-[420px] shrink-0 h-[340px] bg-white rounded-3xl p-8 border border-slate-200/90 shadow-xl hover:shadow-2xl card-lift flex flex-col justify-between group transition-all duration-500 snap-center">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-1 text-amber-400 text-sm">★★★★★</div>
                        <span class="text-4xl font-serif font-black text-slate-300 <?php echo e($t['quoteColor']); ?> transition-colors duration-300 select-none leading-none">"</span>
                    </div>

                    <p class="font-heading font-medium text-slate-700 text-base leading-relaxed italic line-clamp-3 my-3 flex-1">
                        <?php echo e($t['quote']); ?>

                    </p>

                    <div class="pt-5 border-t border-slate-100 flex items-center gap-4">
                        <img src="<?php echo e(media_url($t['photo'], 'images/instructor_portrait.png')); ?>" alt="<?php echo e($t['name']); ?>" class="w-14 h-14 sm:w-14 sm:h-14 rounded-full object-cover shadow-md group-hover:scale-105 transition-transform duration-500 border-2 border-white flex-shrink-0">
                        <div class="space-y-1 min-w-0 flex-1">
                            <h3 class="font-heading font-extrabold text-base text-slate-900 truncate <?php echo e($t['nameColor']); ?> transition-colors"><?php echo e($t['name']); ?></h3>
                            <p class="text-xs font-mono text-slate-500 font-semibold truncate"><?php echo e($t['course']); ?></p>
                            <span class="inline-block <?php echo e($t['badgeBg']); ?> text-[10px] font-mono font-extrabold px-2.5 py-0.5 rounded-full border"><?php echo e($t['badge']); ?></span>
                        </div>
                    </div>
                </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>

        
        <div class="flex items-center justify-center gap-2 pt-4">
            <span class="w-8 h-2.5 rounded-full bg-teal-600 transition-all duration-300"></span>
            <span class="w-2.5 h-2.5 rounded-full bg-slate-300 hover:bg-slate-400 transition-colors duration-300 cursor-pointer"></span>
            <span class="w-2.5 h-2.5 rounded-full bg-slate-300 hover:bg-slate-400 transition-colors duration-300 cursor-pointer"></span>
            <span class="w-2.5 h-2.5 rounded-full bg-slate-300 hover:bg-slate-400 transition-colors duration-300 cursor-pointer"></span>
        </div>

    </div>
</section>
<?php /**PATH C:\laragon\www\elite-academy\resources\views/pages/home/testimonials.blade.php ENDPATH**/ ?>