
<section class="py-16 md:py-24 bg-[#FAFAF9] relative overflow-hidden border-b border-slate-200/80">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        
        <div class="flex flex-col sm:flex-row items-start sm:items-end justify-between gap-4">
            <div class="space-y-3">
                <span class="anim-projects delay-1 inline-block text-xs font-mono uppercase tracking-widest text-teal-600 font-extrabold bg-teal-50 px-3.5 py-1.5 rounded-full border border-teal-200/80 animate-badge-pulse">
                    <?php echo e(__('FACULTY')); ?>

                </span>
                <h2 class="anim-projects delay-2 font-heading text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                    <?php echo e(\App\Models\SiteSetting::getLocalized('teachers_title', __('Meet Our Featured Mentors.'))); ?>

                </h2>
            </div>
            <span class="text-xs font-mono text-slate-500 font-medium">&larr; <?php echo e(__('Swipe Teachers')); ?> &rarr;</span>
        </div>

        
        <?php
            $dbTeachers = \Illuminate\Support\Facades\Schema::hasTable('teacher_profiles')
                ? \App\Models\TeacherProfile::with('user')->get()
                : collect();

            $mentors = $dbTeachers->count() > 0 ? $dbTeachers->map(fn($t) => [
                'name' => $t->user?->name ?: 'Teacher Profile',
                'title' => $t->specialization ?: 'Senior Academic Mentor',
                'dept' => 'Faculty',
                'badgeBg' => 'bg-teal-600',
                'textColor' => 'group-hover:text-teal-300',
                'meta' => ($t->years_experience ?: 5) . '+ Yrs Exp • Active Educator',
                'photo' => 'images/instructor_portrait.webp',
            ]) : [
                ['name' => 'Dr. Ahmed Hassan', 'title' => 'Senior AI & Systems Researcher', 'dept' => 'Programming', 'badgeBg' => 'bg-teal-600', 'textColor' => 'group-hover:text-teal-300', 'meta' => '15+ Yrs Exp • 1,400+ Students • PhD - MIT', 'photo' => 'images/instructor_portrait.webp'],
                ['name' => 'Sarah Mohamed', 'title' => 'Deep Learning Lead Architect', 'dept' => 'Artificial Intelligence', 'badgeBg' => 'bg-purple-600', 'textColor' => 'group-hover:text-purple-300', 'meta' => '12+ Yrs Exp • 1,100+ Students • MSc - Stanford', 'photo' => 'images/instructor_female.webp'],
                ['name' => 'Omar Khaled', 'title' => 'Robotics & Autonomous Systems Specialist', 'dept' => 'Robotics', 'badgeBg' => 'bg-orange-600', 'textColor' => 'group-hover:text-orange-300', 'meta' => '10+ Yrs Exp • 950+ Students • PhD - Cambridge', 'photo' => 'images/instructor_male.webp'],
            ];
        ?>

        <div class="carousel-container no-scrollbar">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $mentors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <div class="carousel-card-large-peek anim-projects delay-3 rounded-3xl overflow-hidden shadow-xl border border-slate-200/80 h-96 relative group card-lift flex-shrink-0 transition-all duration-300">
                    <img src="<?php echo e(media_url($m['photo'], 'images/instructor_portrait.webp')); ?>" alt="<?php echo e($m['name']); ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/40 to-transparent pointer-events-none"></div>

                    <span class="absolute top-5 left-5 text-xs font-mono font-extrabold text-white <?php echo e($m['badgeBg']); ?> px-3.5 py-1.5 rounded-full shadow-md">
                        <?php echo e($m['dept']); ?>

                    </span>

                    <div class="absolute bottom-6 left-6 right-6 text-white space-y-2">
                        <div>
                            <h3 class="font-heading font-extrabold text-2xl text-white <?php echo e($m['textColor']); ?> transition-colors">
                                <?php echo e($m['name']); ?>

                            </h3>
                            <p class="text-xs font-mono text-slate-300 font-semibold"><?php echo e($m['title']); ?></p>
                        </div>

                        <div class="flex items-center justify-between pt-2 border-t border-white/20 text-xs font-medium text-slate-200">
                            <span class="text-[11px] font-mono"><?php echo e($m['meta']); ?></span>
                            <a href="<?php echo e(route('teacher-profile')); ?>" class="text-xs font-extrabold text-teal-300 group-hover:text-teal-200 flex items-center gap-1">
                                <span>View Profile</span>
                                <span class="group-hover:translate-x-1.5 transition-transform duration-300">&rarr;</span>
                            </a>
                        </div>
                    </div>
                </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>

    </div>
</section>
<?php /**PATH C:\laragon\www\elite-academy\resources\views/pages/home/teachers-marquee.blade.php ENDPATH**/ ?>