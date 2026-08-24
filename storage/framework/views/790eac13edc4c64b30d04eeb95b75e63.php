<?php if (isset($component)) { $__componentOriginal166a02a7c5ef5a9331faf66fa665c256 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal166a02a7c5ef5a9331faf66fa665c256 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-panels::components.page.index','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-panels::page'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <div x-data="{ viewMode: 'desktop' }" class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-start">
        
        <div class="xl:col-span-5 space-y-6">
            <form wire:submit="save" class="space-y-6">
                <?php echo e($this->form); ?>


                <?php if (isset($component)) { $__componentOriginal6330f08526bbb3ce2a0da37da512a11f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.button.index','data' => ['type' => 'submit','class' => 'w-full']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','class' => 'w-full']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                    Save About Page Content Settings
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $attributes = $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $component = $__componentOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
            </form>
        </div>

        
        <div class="xl:col-span-7 space-y-4">
            <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-3">
                <div>
                    <h3 class="font-bold text-sm text-slate-900 dark:text-slate-100 flex items-center gap-2">
                        <span>Live About UI Preview</span>
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Real-time view of the about page layout</p>
                </div>

                
                <div class="flex items-center gap-1 bg-slate-100 dark:bg-slate-800 p-1 rounded-xl border border-slate-200 dark:border-slate-700">
                    <button type="button" @click.prevent="viewMode = 'desktop'" :class="viewMode === 'desktop' ? 'bg-white dark:bg-slate-700 text-teal-600 dark:text-teal-400 shadow-xs font-bold' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900'" class="px-3 py-1 text-xs rounded-lg transition-all cursor-pointer">
                        Desktop
                    </button>
                    <button type="button" @click.prevent="viewMode = 'tablet'" :class="viewMode === 'tablet' ? 'bg-white dark:bg-slate-700 text-teal-600 dark:text-teal-400 shadow-xs font-bold' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900'" class="px-3 py-1 text-xs rounded-lg transition-all cursor-pointer">
                        Tablet
                    </button>
                    <button type="button" @click.prevent="viewMode = 'mobile'" :class="viewMode === 'mobile' ? 'bg-white dark:bg-slate-700 text-teal-600 dark:text-teal-400 shadow-xs font-bold' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900'" class="px-3 py-1 text-xs rounded-lg transition-all cursor-pointer">
                        Mobile
                    </button>
                </div>
            </div>

            
            <div class="p-4 bg-slate-200/60 dark:bg-slate-800/60 rounded-2xl border border-slate-300 dark:border-slate-700 shadow-inner flex justify-center items-center overflow-hidden min-h-[750px]">
                <div :class="{
                    'w-full h-[720px] border-2 border-slate-300': viewMode === 'desktop',
                    'w-[768px] max-w-full h-[720px] mx-auto border-4 border-slate-600': viewMode === 'tablet',
                    'w-[375px] max-w-full h-[720px] mx-auto border-8 border-slate-900 rounded-[2rem]': viewMode === 'mobile'
                }" class="transition-all duration-300 bg-white rounded-2xl shadow-2xl overflow-hidden">
                    <iframe id="about-preview-iframe" src="<?php echo e(url('/about?iframe=1')); ?>" title="About Page UI Preview" class="w-full h-full border-0 block"></iframe>
                </div>
            </div>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal166a02a7c5ef5a9331faf66fa665c256)): ?>
<?php $attributes = $__attributesOriginal166a02a7c5ef5a9331faf66fa665c256; ?>
<?php unset($__attributesOriginal166a02a7c5ef5a9331faf66fa665c256); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal166a02a7c5ef5a9331faf66fa665c256)): ?>
<?php $component = $__componentOriginal166a02a7c5ef5a9331faf66fa665c256; ?>
<?php unset($__componentOriginal166a02a7c5ef5a9331faf66fa665c256); ?>
<?php endif; ?>
<?php /**PATH C:\laragon\www\elite-academy\resources\views\filament\pages\manage-about-page.blade.php ENDPATH**/ ?>