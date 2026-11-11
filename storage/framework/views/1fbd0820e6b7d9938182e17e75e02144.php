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

    <div x-data="{ viewMode: 'desktop' }" class="cms-preview-grid">
        
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <form wire:submit="save" style="display: flex; flex-direction: column; gap: 1.5rem;">
                <?php echo e($this->form); ?>


                <?php if (isset($component)) { $__componentOriginal6330f08526bbb3ce2a0da37da512a11f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.button.index','data' => ['type' => 'submit','style' => 'width: 100%;']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','style' => 'width: 100%;']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                    <?php echo e(__('Save About Page Content Settings')); ?>

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

        
        <div style="display: flex; flex-direction: column;">
            <div class="cms-preview-toolbar">
                <div>
                    <h3 class="cms-preview-title">
                        <?php echo e(__('Live About UI Preview')); ?>

                    </h3>
                    <p class="cms-preview-sub"><?php echo e(__('Real-time view of the about page layout')); ?></p>
                </div>

                
                <div class="cms-toggle-group">
                    <button type="button" @click.prevent="viewMode = 'desktop'" :class="viewMode === 'desktop' ? 'is-active' : ''" class="cms-toggle-btn">
                        <?php echo e(__('Desktop')); ?>

                    </button>
                    <button type="button" @click.prevent="viewMode = 'tablet'" :class="viewMode === 'tablet' ? 'is-active' : ''" class="cms-toggle-btn">
                        <?php echo e(__('Tablet')); ?>

                    </button>
                    <button type="button" @click.prevent="viewMode = 'mobile'" :class="viewMode === 'mobile' ? 'is-active' : ''" class="cms-toggle-btn">
                        <?php echo e(__('Mobile')); ?>

                    </button>
                </div>
            </div>

            
            <div class="cms-preview-stage">
                <div :class="{
                    'cms-frame-desktop': viewMode === 'desktop',
                    'cms-frame-tablet': viewMode === 'tablet',
                    'cms-frame-mobile': viewMode === 'mobile'
                }" class="cms-preview-frame">
                    <iframe id="about-preview-iframe" src="<?php echo e(url('/about?iframe=1')); ?>" title="About Page UI Preview" style="width: 100%; height: 100%; border: 0; display: block;"></iframe>
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
<?php /**PATH C:\laragon\www\elite-academy\resources\views/filament/pages/manage-about-page.blade.php ENDPATH**/ ?>