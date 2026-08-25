<?php $__env->startSection('content'); ?>
<section class="py-12 md:py-16 bg-[#FAFAF9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 border-b border-slate-200/80 pb-6">
            <div class="space-y-2">
                <h1 class="font-heading text-3xl sm:text-4xl lg:text-5xl font-black text-slate-900 tracking-tight">
                    School <span class="text-teal-600 underline decoration-orange-500 underline-offset-8">Subjects</span>
                </h1>
                <p class="text-slate-600 text-base font-medium">
                    Browse every subject and discover available teachers and courses.
                </p>
            </div>
        </div>

        
        <div id="subjects-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-x-4 gap-y-5 md:gap-8 pt-2">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($subjects) && count($subjects) > 0): ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php
                        $isModel = $s instanceof \App\Models\Subject;
                        $slug = $isModel ? $s->slug : 'subject-details';
                        $cardData = [
                            'image' => $isModel ? ($s->image ?: 'images/course_ai.png') : ($s['img'] ?? 'images/course_ai.png'),
                            'grade' => $isModel ? ($s->category?->name ?: 'General Curriculum') : ($s['grade'] ?? 'General Curriculum'),
                            'badgeColor' => 'bg-teal-600',
                            'name' => $isModel ? $s->name : ($s['name'] ?? 'Subject Name'),
                            'description' => $isModel ? ($s->description ?: 'Comprehensive subject curriculum.') : ($s['desc'] ?? 'Description'),
                            'teachers' => $isModel ? ($s->courses->count() . ' Courses') : ($s['teachers'] ?? '10 Teachers'),
                            'lessons' => 'Full Syllabus',
                            'route' => route('subject-details', ['slug' => $slug]),
                        ];
                    ?>
                    <?php echo $__env->make('components.subject-card', $cardData, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <?php else: ?>
                <div class="col-span-4 text-center py-12 bg-white rounded-3xl border border-slate-200 p-8 shadow-sm">
                    <div class="text-4xl mb-3">📐</div>
                    <h3 class="font-bold text-lg text-slate-800">No Subjects Active Yet</h3>
                    <p class="text-xs text-slate-500 mt-1">Check back soon as new subjects are being added by administrators.</p>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\elite-academy\resources\views/pages/subjects.blade.php ENDPATH**/ ?>