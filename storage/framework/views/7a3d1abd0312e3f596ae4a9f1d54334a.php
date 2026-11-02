<?php
    $credentials = config('demo.credentials', []);
    $accounts = $credentials['accounts'] ?? [];
    $password = $credentials['password'] ?? 'Demo@12345';
    $directLogin = $credentials['direct_login'] ?? [];
    $directEnabled = (bool) ($directLogin['enabled'] ?? true);
    $style = $directLogin['style'] ?? 'cards';
    $showTable = (bool) ($credentials['show_table'] ?? true);
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
?>

<div class="demo-credentials-wrapper space-y-4">
    <style>
        /* Force font icons inside demo wrapper to retain icon fonts */
        .demo-credentials-wrapper .fa,
        .demo-credentials-wrapper .fas,
        .demo-credentials-wrapper .far,
        .demo-credentials-wrapper .fa-solid,
        .demo-credentials-wrapper i[class*="fa-"] {
            font-family: "Font Awesome 6 Free" !important;
            font-style: normal !important;
            font-weight: 900 !important;
        }
    </style>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($directEnabled && !empty($accounts)): ?>
        <div class="space-y-2.5">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
                    </span>
                    <?php echo e($isRtl ? 'تسجيل الدخول السريع (نقرة واحدة)' : 'One-Click Instant Access'); ?>

                </span>
                <span class="text-[10px] font-semibold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/60 px-2 py-0.5 rounded-full border border-blue-200/60 dark:border-blue-800/60">
                    <?php echo e($isRtl ? 'بدون كتابة كلمة مرور' : 'No typing required'); ?>

                </span>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($style === 'cards'): ?>
                <div class="grid grid-cols-2 gap-2.5 sm:gap-3">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $acc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php
                            $role = $acc['role'] ?? 'student';
                            $label = is_array($acc['label'] ?? null)
                                ? ($acc['label'][$locale] ?? $acc['label']['en'] ?? ucfirst($role))
                                : ($acc['label'] ?? ucfirst($role));
                            $email = $acc['email'] ?? '';
                            
                            $roleMeta = match($role) {
                                'admin' => [
                                    'svg' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>',
                                    'color' => 'from-rose-500/10 to-rose-600/5 text-rose-600 dark:text-rose-400 border-rose-200/70 dark:border-rose-900/60',
                                    'badge' => 'bg-rose-50 text-rose-700 dark:bg-rose-950/80 dark:text-rose-300',
                                    'btn' => 'bg-rose-600 hover:bg-rose-700 text-white',
                                ],
                                'teacher' => [
                                    'svg' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h18v13H3zM8 21h8m-4-5v5m-4-10h8m-8 3h5"/></svg>',
                                    'color' => 'from-teal-500/10 to-teal-600/5 text-teal-600 dark:text-teal-400 border-teal-200/70 dark:border-teal-900/60',
                                    'badge' => 'bg-teal-50 text-teal-700 dark:bg-teal-950/80 dark:text-teal-300',
                                    'btn' => 'bg-teal-600 hover:bg-teal-700 text-white',
                                ],
                                'student' => [
                                    'svg' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 2L1 7l11 5 9-4.09V17h2V7L12 2zM5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/></svg>',
                                    'color' => 'from-blue-500/10 to-blue-600/5 text-blue-600 dark:text-blue-400 border-blue-200/70 dark:border-blue-900/60',
                                    'badge' => 'bg-blue-50 text-blue-700 dark:bg-blue-950/80 dark:text-blue-300',
                                    'btn' => 'bg-blue-600 hover:bg-blue-700 text-white',
                                ],
                                'parent' => [
                                    'svg' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2m8-10a4 4 0 100-8 4 4 0 000 8zm14 10v-2a4 4 0 00-3-3.87m-4-12a4 4 0 010 7.75"/></svg>',
                                    'color' => 'from-amber-500/10 to-amber-600/5 text-amber-600 dark:text-amber-400 border-amber-200/70 dark:border-amber-900/60',
                                    'badge' => 'bg-amber-50 text-amber-700 dark:bg-amber-950/80 dark:text-amber-300',
                                    'btn' => 'bg-amber-600 hover:bg-amber-700 text-white',
                                ],
                                default => [
                                    'svg' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>',
                                    'color' => 'from-slate-500/10 to-slate-600/5 text-slate-600 dark:text-slate-400 border-slate-200 dark:border-slate-800',
                                    'badge' => 'bg-slate-50 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
                                    'btn' => 'bg-slate-700 hover:bg-slate-800 text-white',
                                ],
                            };
                        ?>

                        <form action="<?php echo e(route('demo.login', ['role' => $role])); ?>" method="POST" class="w-full">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="group w-full text-start p-3 rounded-2xl bg-gradient-to-br <?php echo e($roleMeta['color']); ?> border transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md cursor-pointer flex flex-col justify-between h-full space-y-2">
                                <div class="flex items-center justify-between w-full">
                                    <span class="w-8 h-8 rounded-xl flex items-center justify-center bg-white/90 dark:bg-slate-900/90 shadow-xs">
                                        <?php echo $roleMeta['svg']; ?>

                                    </span>
                                    <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-md <?php echo e($roleMeta['badge']); ?>">
                                        <?php echo e($label); ?>

                                    </span>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-slate-900 dark:text-white truncate"><?php echo e($email); ?></p>
                                    <p class="text-[10px] text-slate-500 dark:text-slate-400 flex items-center gap-1 mt-0.5">
                                        <span><?php echo e($isRtl ? 'دخول فوري' : 'Direct Sign-In'); ?></span>
                                        <svg class="w-2.5 h-2.5 rtl:rotate-180 group-hover:translate-x-0.5 rtl:group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </p>
                                </div>
                            </button>
                        </form>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            <?php elseif($style === 'buttons'): ?>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $acc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php
                            $role = $acc['role'] ?? 'student';
                            $label = is_array($acc['label'] ?? null)
                                ? ($acc['label'][$locale] ?? $acc['label']['en'] ?? ucfirst($role))
                                : ($acc['label'] ?? ucfirst($role));
                        ?>
                        <form action="<?php echo e(route('demo.login', ['role' => $role])); ?>" method="POST" class="w-full">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="w-full py-2 px-3 text-xs font-bold rounded-xl bg-blue-600 hover:bg-blue-700 text-white shadow-xs transition-colors flex items-center justify-center gap-1.5 cursor-pointer">
                                <span><?php echo e($label); ?></span>
                                <svg class="w-2.5 h-2.5 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                        </form>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            <?php else: ?>
                
                <div class="divide-y divide-slate-100 dark:divide-slate-800 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden bg-white/50 dark:bg-slate-900/50">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $acc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php
                            $role = $acc['role'] ?? 'student';
                            $label = is_array($acc['label'] ?? null)
                                ? ($acc['label'][$locale] ?? $acc['label']['en'] ?? ucfirst($role))
                                : ($acc['label'] ?? ucfirst($role));
                            $email = $acc['email'] ?? '';
                        ?>
                        <form action="<?php echo e(route('demo.login', ['role' => $role])); ?>" method="POST" class="w-full">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="w-full px-3 py-2.5 flex items-center justify-between hover:bg-blue-50/50 dark:hover:bg-blue-950/30 transition-colors text-start cursor-pointer">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200"><?php echo e($label); ?></span>
                                    <span class="text-[11px] text-slate-400"><?php echo e($email); ?></span>
                                </div>
                                <span class="text-xs font-semibold text-blue-600 dark:text-blue-400 flex items-center gap-1">
                                    <span><?php echo e($isRtl ? 'دخول' : 'Sign in'); ?></span>
                                    <svg class="w-2.5 h-2.5 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </span>
                            </button>
                        </form>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showTable && !empty($accounts)): ?>
        <div class="pt-2">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400">
                    <?php echo e($isRtl ? 'بيانات الحسابات التجريبية (للملء اليدوي):' : 'Demo Accounts (Manual Fill):'); ?>

                </span>
                <span class="text-[10px] font-mono text-slate-400 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-md">
                    <?php echo e($isRtl ? 'كلمة المرور موحدة: ' : 'Password: '); ?> <strong class="text-slate-700 dark:text-slate-200"><?php echo e($password); ?></strong>
                </span>
            </div>

            <div class="overflow-hidden rounded-xl border border-slate-200/80 dark:border-slate-800 bg-white/40 dark:bg-slate-900/40 text-[11px]">
                <table class="w-full text-start">
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $acc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <?php
                                $role = $acc['role'] ?? 'student';
                                $label = is_array($acc['label'] ?? null)
                                    ? ($acc['label'][$locale] ?? $acc['label']['en'] ?? ucfirst($role))
                                    : ($acc['label'] ?? ucfirst($role));
                                $email = $acc['email'] ?? '';
                            ?>
                            <tr class="border-b border-slate-100 dark:border-slate-800/80 last:border-b-0 hover:bg-slate-50/80 dark:hover:bg-slate-800/50">
                                <td class="px-3 py-1.5 font-bold text-slate-700 dark:text-slate-300 w-24">
                                    <?php echo e($label); ?>

                                </td>
                                <td class="px-3 py-1.5 font-mono text-slate-600 dark:text-slate-400 truncate">
                                    <?php echo e($email); ?>

                                </td>
                                <td class="px-3 py-1.5 text-end">
                                    <button 
                                        type="button" 
                                        class="copy-cred-btn text-[10px] font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 bg-blue-50 dark:bg-blue-950/60 hover:bg-blue-100 px-2 py-0.5 rounded transition-colors cursor-pointer inline-flex items-center gap-1"
                                        data-email="<?php echo e($email); ?>"
                                        data-pass="<?php echo e($password); ?>"
                                    >
                                        <svg class="w-3 h-3 copy-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
                                        </svg>
                                        <span class="copy-text"><?php echo e($isRtl ? 'تعبئة' : 'Fill'); ?></span>
                                    </button>
                                </td>
                            </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const copyBtns = document.querySelectorAll('.copy-cred-btn');
    copyBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            const email = this.getAttribute('data-email');
            const pass = this.getAttribute('data-pass');

            const emailInput = document.getElementById('login-email');
            const passInput = document.getElementById('login-password');

            if (emailInput && passInput) {
                emailInput.value = email;
                passInput.value = pass;
                emailInput.dispatchEvent(new Event('input', { bubbles: true }));
                passInput.dispatchEvent(new Event('input', { bubbles: true }));
            }

            // Copy to clipboard
            if (navigator.clipboard) {
                navigator.clipboard.writeText(`${email}\n${pass}`);
            }

            const copyText = this.querySelector('.copy-text');
            const orig = copyText ? copyText.textContent : '';
            if (copyText) {
                copyText.textContent = '✓';
                setTimeout(() => { copyText.textContent = orig; }, 1500);
            }
        });
    });
});
</script>
<?php /**PATH C:\laragon\www\elite-academy\resources\views/components/demo/credentials.blade.php ENDPATH**/ ?>