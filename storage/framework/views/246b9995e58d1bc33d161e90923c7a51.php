<?php $__env->startSection('content'); ?>
<section class="py-12 md:py-16 bg-[#FAFAF9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8 md:space-y-12">

        
        <?php echo $__env->make('components.section-header', [
            'badge' => 'ACADEMIC INSIGHTS & BLOG',
            'title' => 'Latest Articles & <span class="text-teal-600 underline decoration-orange-500 underline-offset-8">Educational News</span>',
            'subtitle' => 'Expert advice, study tips, and academic insights from Elite Academy faculty.',
            'centered' => true,
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        
        <div class="flex flex-wrap items-center justify-center gap-2 pt-2">
            <a href="<?php echo e(route('blog')); ?>"
               class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                   'px-4 py-2 rounded-full text-xs font-bold transition-all border',
                   'bg-teal-600 text-white shadow-md border-teal-600' => empty($selectedCategory),
                   'bg-white text-slate-700 hover:bg-slate-100 border-slate-200' => ! empty($selectedCategory),
               ]); ?>">
                All Articles
            </a>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <?php $isActive = strtolower($selectedCategory ?? '') === strtolower($cat); ?>
                <a href="<?php echo e(route('blog', ['category' => $cat])); ?>"
                   class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                       'px-4 py-2 rounded-full text-xs font-bold transition-all border',
                       'bg-teal-600 text-white shadow-md border-teal-600' => $isActive,
                       'bg-white text-slate-700 hover:bg-slate-100 border-slate-200' => ! $isActive,
                   ]); ?>">
                    <?php echo e($cat); ?>

                </a>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>

        
        <div class="space-y-8 md:space-y-12">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($articles) && count($articles) > 0): ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $articles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php
                        $isModel = $a instanceof \App\Models\Article;
                        $slug = $isModel ? $a->slug : 'blog-details';
                        $cardData = [
                            'image' => $isModel ? $a->featured_image_url : ($a['image'] ?? 'images/hero_student.png'),
                            'category' => $isModel ? $a->category : ($a['category'] ?? 'Study Tips'),
                            'categoryColor' => 'bg-teal-600',
                            'title' => $isModel ? $a->title : ($a['title'] ?? 'Article Title'),
                            'excerpt' => $isModel ? ($a->excerpt ?: 'Read our comprehensive academic guidance article.') : ($a['excerpt'] ?? 'Article excerpt'),
                            'author' => $isModel ? ($a->authorUser?->name ?: 'Dr. Ahmed Hassan') : ($a['author'] ?? 'Dr. Ahmed Hassan'),
                            'date' => $isModel ? ($a->published_at ? $a->published_at->format('M d, Y') : now()->format('M d, Y')) : ($a['date'] ?? 'Oct 12, 2026'),
                            'readTime' => $isModel ? ($a->read_time_minutes . ' min read') : ($a['readTime'] ?? '6 min read'),
                            'route' => route('blog-details', ['slug' => $slug]),
                        ];
                    ?>
                    <?php echo $__env->make('components.article-card', $cardData, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $loop->last): ?>
                        <hr class="border-t border-slate-200/80">
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <?php else: ?>
                <div class="text-center py-12 bg-white rounded-3xl border border-slate-200 p-8 shadow-sm">
                    <div class="text-4xl mb-3">📰</div>
                    <h3 class="font-bold text-lg text-slate-800">No Articles Found for Selected Category</h3>
                    <p class="text-xs text-slate-500 mt-1 mb-4">Try selecting "All Articles" or check back soon for new publications.</p>
                    <a href="<?php echo e(route('blog')); ?>" class="btn-lift inline-block px-5 py-2.5 bg-teal-600 text-white rounded-xl text-xs font-bold">
                        View All Articles
                    </a>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(method_exists($articles, 'hasPages') && $articles->hasPages()): ?>
            <div class="pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-slate-200/80">
                <div class="text-xs font-mono text-slate-500 font-bold">
                    Showing <?php echo e($articles->firstItem()); ?> to <?php echo e($articles->lastItem()); ?> of <?php echo e($articles->total()); ?> Articles
                </div>

                <div class="flex items-center gap-1.5">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($articles->onFirstPage()): ?>
                        <span class="px-3.5 py-2 text-xs font-bold text-slate-300 bg-slate-100 rounded-xl cursor-not-allowed">
                            &larr; Prev
                        </span>
                    <?php else: ?>
                        <a href="<?php echo e($articles->previousPageUrl()); ?>" class="btn-lift px-3.5 py-2 text-xs font-bold text-slate-700 bg-white hover:bg-slate-100 border border-slate-200 rounded-xl shadow-2xs transition-all">
                            &larr; Prev
                        </a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = range(1, $articles->lastPage()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($page == $articles->currentPage()): ?>
                            <span class="w-9 h-9 flex items-center justify-center text-xs font-bold text-white bg-teal-600 rounded-xl shadow-md shadow-teal-600/20">
                                <?php echo e($page); ?>

                            </span>
                        <?php else: ?>
                            <a href="<?php echo e($articles->url($page)); ?>" class="btn-lift w-9 h-9 flex items-center justify-center text-xs font-bold text-slate-700 bg-white hover:bg-slate-100 border border-slate-200 rounded-xl shadow-2xs transition-all">
                                <?php echo e($page); ?>

                            </a>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($articles->hasMorePages()): ?>
                        <a href="<?php echo e($articles->nextPageUrl()); ?>" class="btn-lift px-3.5 py-2 text-xs font-bold text-slate-700 bg-white hover:bg-slate-100 border border-slate-200 rounded-xl shadow-2xs transition-all">
                            Next &rarr;
                        </a>
                    <?php else: ?>
                        <span class="px-3.5 py-2 text-xs font-bold text-slate-300 bg-slate-100 rounded-xl cursor-not-allowed">
                            Next &rarr;
                        </span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\elite-academy\resources\views\pages\blog.blade.php ENDPATH**/ ?>