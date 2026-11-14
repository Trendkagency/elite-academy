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

    <?php
        $isAr = app()->getLocale() === 'ar';
        $currentAppName = brand('app_name', 'Elite Academy');
        $currentPrimary = brand_color('primary', '#0d9488');
        $currentSecondary = brand_color('secondary', '#6366f1');
        $currentCurrency = app_currency();
        $currentSymbol = currency_symbol();
        $currentSurfaceMode = \App\Models\SiteSetting::get('theme_surface_mode', 'dark-glass');
    ?>

    
    <div class="flex items-center justify-between gap-3 px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-teal-500/25 bg-white/70 dark:bg-slate-900/60 backdrop-blur-md shadow-xs flex-wrap">
        <div class="flex items-center gap-2.5 text-xs">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-teal-500/10 text-teal-700 dark:text-teal-300 font-bold border border-teal-500/20">
                <span class="w-2 h-2 rounded-full bg-teal-500 animate-pulse"></span>
                <?php echo e($isAr ? 'المزامنة الحية مفعلة' : 'Live Sync Active'); ?>

            </span>

            <span class="text-slate-500 dark:text-slate-400 font-medium hidden sm:inline">
                <?php echo e($isAr ? 'المنظومة النشطة:' : 'Active System:'); ?>

            </span>
            <strong class="text-slate-800 dark:text-white font-extrabold"><?php echo e($currentAppName); ?></strong>
        </div>

        <div class="flex items-center gap-2.5 sm:gap-3 text-xs flex-wrap">
            
            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-100 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700">
                <span class="w-3 h-3 rounded-full border border-white/20 shadow-xs" style="background-color: <?php echo e($currentPrimary); ?>;"></span>
                <code class="font-mono text-[11px] font-bold text-teal-600 dark:text-teal-400"><?php echo e($currentPrimary); ?></code>
            </div>

            
            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-100 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 font-bold text-amber-600 dark:text-amber-400">
                <i class="fa-solid fa-coins text-[10px]"></i>
                <span><?php echo e($currentCurrency); ?> (<?php echo e($currentSymbol); ?>)</span>
            </div>

            
            <div class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-slate-100 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 font-medium hidden md:inline-flex">
                <i class="fa-solid fa-wand-magic-sparkles text-teal-500 text-[10px]"></i>
                <span class="capitalize"><?php echo e(str_replace('-', ' ', (string) $currentSurfaceMode)); ?></span>
            </div>
        </div>
    </div>

    
    <form wire:submit="save" class="space-y-6">
        <?php echo e($this->form); ?>


        
        <div class="p-4 rounded-2xl border border-slate-200/80 dark:border-teal-500/25 bg-white/80 dark:bg-slate-900/70 backdrop-blur-md shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                <i class="fa-solid fa-keyboard text-teal-500"></i>
                <span><?php echo e($isAr ? 'اضغط' : 'Press'); ?></span>
                <kbd class="px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-mono text-[11px] text-teal-600 dark:text-teal-400 font-bold shadow-xs">Ctrl + S</kbd>
                <span><?php echo e($isAr ? 'للحفظ الفوري ومزامنة كافة الإعدادات' : 'to instantly save & sync full settings'); ?></span>
            </div>

            <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
                <a href="<?php echo e(route('home')); ?>" target="_blank"
                   class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold transition-all shadow-xs">
                    <i class="fa-solid fa-arrow-up-right-from-square text-[11px]"></i>
                    <?php echo e($isAr ? 'معاينة الموقع الرئيسي' : 'Preview Site ↗'); ?>

                </a>

                <?php if (isset($component)) { $__componentOriginal6330f08526bbb3ce2a0da37da512a11f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.button.index','data' => ['type' => 'submit','size' => 'lg','icon' => 'heroicon-o-check-circle','color' => 'primary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','size' => 'lg','icon' => 'heroicon-o-check-circle','color' => 'primary']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                    <?php echo e($isAr ? 'حفظ ومزامنة كافة الإعدادات' : 'Save & Sync Full System Settings'); ?>

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
            </div>
        </div>
    </form>
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
<?php /**PATH C:\laragon\www\elite-academy\resources\views/filament/pages/manage-general-settings.blade.php ENDPATH**/ ?>