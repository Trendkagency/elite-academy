<?php $__env->startSection('content'); ?>

<section class="relative py-12 md:py-16 bg-gradient-to-r from-slate-900 via-slate-950 to-teal-950 text-white border-b border-slate-800/80 overflow-hidden shadow-2xl">
    <div class="absolute -right-20 -top-20 w-96 h-96 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute left-10 -bottom-20 w-80 h-80 bg-orange-500/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6 relative z-10">
        <?php echo $__env->make('components.breadcrumb', [
            'items' => [
                ['label' => __('navbar.home'), 'route' => 'home'],
                ['label' => __('app.student_portal'), 'route' => 'student-portal'],
                ['label' => app()->getLocale() === 'ar' ? 'الملف الشخصي' : 'Student Profile'],
            ]
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-6">
                <div class="relative group">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($profile->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($profile->avatar)): ?>
                        <img src="<?php echo e(asset('storage/' . $profile->avatar)); ?>" alt="<?php echo e($user->name); ?>" class="w-20 h-20 sm:w-24 sm:h-24 rounded-3xl object-cover border-4 border-teal-500/40 shadow-xl shadow-teal-500/20">
                    <?php else: ?>
                        <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-3xl bg-gradient-to-tr from-teal-500 to-emerald-400 text-slate-950 font-heading font-black text-3xl sm:text-4xl flex items-center justify-center shadow-xl shadow-teal-500/20 border-4 border-teal-300/40">
                            <?php echo e(mb_substr($user->name ?? 'S', 0, 1)); ?>

                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <button onclick="document.getElementById('avatarInput').click()" class="absolute -bottom-2 -right-2 w-8 h-8 rounded-full bg-teal-500 hover:bg-teal-400 text-slate-950 flex items-center justify-center text-xs font-bold shadow-md cursor-pointer transition-transform hover:scale-110" title="<?php echo e(app()->getLocale() === 'ar' ? 'تغيير الصورة الشخصية' : 'Change Avatar'); ?>">
                        📷
                    </button>
                </div>

                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="inline-block text-[11px] font-mono uppercase tracking-widest text-teal-400 font-extrabold bg-teal-950/80 px-3 py-0.5 rounded-full border border-teal-700/60">
                            <?php echo e(app()->getLocale() === 'ar' ? 'حساب طالب معتمد' : 'Verified Student Account'); ?>

                        </span>
                        <span class="text-xs font-mono text-emerald-400 font-bold bg-emerald-950/60 px-2.5 py-0.5 rounded-full border border-emerald-800/60">
                            ● Active Status
                        </span>
                    </div>
                    <h1 class="font-heading text-2xl sm:text-4xl font-black text-white tracking-tight">
                        <?php echo e($user->name); ?>

                    </h1>
                    <p class="text-slate-300 text-xs sm:text-sm font-mono flex flex-wrap items-center gap-3 pt-0.5">
                        <span>✉️ <?php echo e($user->email); ?></span>
                        <span>•</span>
                        <span>📱 <?php echo e($user->phone ?: (app()->getLocale() === 'ar' ? 'غير مسجل' : 'Not Provided')); ?></span>
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3 self-start md:self-auto">
                <a href="<?php echo e(route('student-portal')); ?>" class="btn-lift px-5 py-3 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold rounded-2xl border border-slate-700 shadow-md flex items-center gap-2 transition-all">
                    <span>&larr;</span> <?php echo e(app()->getLocale() === 'ar' ? 'العودة للوحة التحكم' : 'Back to Dashboard'); ?>

                </a>
            </div>
        </div>
    </div>
</section>


<section class="py-12 md:py-16 bg-[#FAFAF9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10 md:space-y-12">

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
            <div class="animate-fade-in-up p-5 bg-emerald-50 border border-emerald-200 rounded-3xl flex items-center justify-between text-emerald-950 shadow-sm">
                <div class="flex items-center gap-3">
                    <span class="text-xl">✅</span>
                    <span class="font-bold text-xs sm:text-sm font-mono"><?php echo e(session('success')); ?></span>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
            <div class="animate-fade-in-up p-5 bg-rose-50 border border-rose-200 rounded-3xl space-y-2 text-rose-950 shadow-sm">
                <div class="flex items-center gap-2 font-bold text-xs sm:text-sm">
                    <span>⚠️</span>
                    <span><?php echo e(app()->getLocale() === 'ar' ? 'يرجى تصحيح الأخطاء التالية:' : 'Please correct the following errors:'); ?></span>
                </div>
                <ul class="list-disc list-inside text-xs font-mono text-rose-800 space-y-1">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <li><?php echo e($error); ?></li>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </ul>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10">

            
            <div class="lg:col-span-8 space-y-8 lg:space-y-10">

                
                <div class="glass-card rounded-3xl p-6 sm:p-8 md:p-9 border border-slate-200/80 shadow-sm hover:shadow-lg transition-all space-y-6 animate-fade-in-up stagger-1">
                    <div class="border-b border-slate-100 pb-4">
                        <h2 class="font-heading font-black text-xl sm:text-2xl text-slate-900 flex items-center gap-2">
                            <span>👤</span> <?php echo e(app()->getLocale() === 'ar' ? 'البيانات الشخصية والأكاديمية' : 'Personal & Academic Details'); ?>

                        </h2>
                        <p class="text-xs font-mono text-slate-500 mt-1">
                            <?php echo e(app()->getLocale() === 'ar' ? 'قم بتحديث اسمك، رقم الهاتف، المرحلة الدراسية، واسم المدرسة.' : 'Update your name, phone number, grade level, and school information.'); ?>

                        </p>
                    </div>

                    <form action="<?php echo e(route('student.profile.update')); ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
                        <?php echo csrf_field(); ?>
                        <input type="file" id="avatarInput" name="avatar" accept="image/*" class="hidden" onchange="previewAvatar(this)">

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            
                            <div class="space-y-2">
                                <label class="block text-xs font-mono font-bold text-slate-700 uppercase tracking-wider">
                                    <?php echo e(app()->getLocale() === 'ar' ? 'الاسم بالكامل' : 'Full Name'); ?> <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="name" value="<?php echo e(old('name', $user->name)); ?>" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-mono text-slate-900 focus:outline-none focus:border-teal-500 focus:bg-white transition-all">
                            </div>

                            
                            <div class="space-y-2">
                                <label class="block text-xs font-mono font-bold text-slate-700 uppercase tracking-wider">
                                    <?php echo e(app()->getLocale() === 'ar' ? 'رقم الهاتف / الواتساب' : 'Phone Number'); ?>

                                </label>
                                <input type="text" name="phone" value="<?php echo e(old('phone', $user->phone)); ?>" placeholder="+20 100 000 0000" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-mono text-slate-900 focus:outline-none focus:border-teal-500 focus:bg-white transition-all">
                            </div>

                            
                            <div class="space-y-2">
                                <label class="block text-xs font-mono font-bold text-slate-700 uppercase tracking-wider">
                                    <?php echo e(app()->getLocale() === 'ar' ? 'البريد الإلكتروني (غير قابل للتعديل)' : 'Email Address (Readonly)'); ?>

                                </label>
                                <input type="email" value="<?php echo e($user->email); ?>" disabled class="w-full px-4 py-3 bg-slate-100 border border-slate-200 rounded-2xl text-xs font-mono text-slate-500 cursor-not-allowed">
                            </div>

                            
                            <div class="space-y-2">
                                <label class="block text-xs font-mono font-bold text-slate-700 uppercase tracking-wider">
                                    <?php echo e(app()->getLocale() === 'ar' ? 'الصف / المرحلة الدراسية' : 'Grade Level'); ?>

                                </label>
                                <select name="grade_level_id" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-mono text-slate-900 focus:outline-none focus:border-teal-500 focus:bg-white transition-all">
                                    <option value=""><?php echo e(app()->getLocale() === 'ar' ? '-- اختر المرحلة الدراسية --' : '-- Select Grade Level --'); ?></option>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $gradeLevels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                        <option value="<?php echo e($gl->id); ?>" <?php echo e(old('grade_level_id', $profile->grade_level_id) == $gl->id ? 'selected' : ''); ?>>
                                            <?php echo e($gl->name); ?>

                                        </option>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </select>
                            </div>

                            
                            <div class="space-y-2">
                                <label class="block text-xs font-mono font-bold text-slate-700 uppercase tracking-wider">
                                    <?php echo e(app()->getLocale() === 'ar' ? 'اسم المدرسة / الأكاديمية' : 'School Name'); ?>

                                </label>
                                <input type="text" name="school_name" value="<?php echo e(old('school_name', $profile->school_name)); ?>" placeholder="e.g. STEM Cairo High School" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-mono text-slate-900 focus:outline-none focus:border-teal-500 focus:bg-white transition-all">
                            </div>

                            
                            <div class="space-y-2">
                                <label class="block text-xs font-mono font-bold text-slate-700 uppercase tracking-wider">
                                    <?php echo e(app()->getLocale() === 'ar' ? 'تاريخ الميلاد' : 'Date of Birth'); ?>

                                </label>
                                <input type="date" name="date_of_birth" value="<?php echo e(old('date_of_birth', $profile->date_of_birth ? $profile->date_of_birth->format('Y-m-d') : '')); ?>" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-mono text-slate-900 focus:outline-none focus:border-teal-500 focus:bg-white transition-all">
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                            <button type="submit" class="btn-lift px-6 py-3 bg-teal-600 hover:bg-teal-700 text-white text-xs font-extrabold rounded-2xl shadow-md shadow-teal-600/30 flex items-center gap-2 cursor-pointer">
                                <span>💾</span> <?php echo e(app()->getLocale() === 'ar' ? 'حفظ التغييرات' : 'Save Profile Details'); ?>

                            </button>
                        </div>
                    </form>
                </div>

                
                <div class="glass-card rounded-3xl p-6 sm:p-8 md:p-9 border border-slate-200/80 shadow-sm hover:shadow-lg transition-all space-y-6 animate-fade-in-up stagger-2">
                    <div class="border-b border-slate-100 pb-4">
                        <h2 class="font-heading font-black text-xl sm:text-2xl text-slate-900 flex items-center gap-2">
                            <span>🔒</span> <?php echo e(app()->getLocale() === 'ar' ? 'أمان الحساب وكلمة المرور' : 'Account Security & Password'); ?>

                        </h2>
                        <p class="text-xs font-mono text-slate-500 mt-1">
                            <?php echo e(app()->getLocale() === 'ar' ? 'قم بتغيير كلمة المرور الخاصة بك بحساب الطالب بانتظام لحماية بياناتك.' : 'Update your password regularly to maintain account security.'); ?>

                        </p>
                    </div>

                    <form action="<?php echo e(route('student.profile.password')); ?>" method="POST" class="space-y-6">
                        <?php echo csrf_field(); ?>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                            
                            <div class="space-y-2">
                                <label class="block text-xs font-mono font-bold text-slate-700 uppercase tracking-wider">
                                    <?php echo e(app()->getLocale() === 'ar' ? 'كلمة المرور الحالية' : 'Current Password'); ?> <span class="text-rose-500">*</span>
                                </label>
                                <input type="password" name="current_password" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-mono text-slate-900 focus:outline-none focus:border-teal-500 focus:bg-white transition-all">
                            </div>

                            
                            <div class="space-y-2">
                                <label class="block text-xs font-mono font-bold text-slate-700 uppercase tracking-wider">
                                    <?php echo e(app()->getLocale() === 'ar' ? 'كلمة المرور الجديدة' : 'New Password'); ?> <span class="text-rose-500">*</span>
                                </label>
                                <input type="password" name="password" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-mono text-slate-900 focus:outline-none focus:border-teal-500 focus:bg-white transition-all">
                            </div>

                            
                            <div class="space-y-2">
                                <label class="block text-xs font-mono font-bold text-slate-700 uppercase tracking-wider">
                                    <?php echo e(app()->getLocale() === 'ar' ? 'تأكيد كلمة المرور' : 'Confirm Password'); ?> <span class="text-rose-500">*</span>
                                </label>
                                <input type="password" name="password_confirmation" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-mono text-slate-900 focus:outline-none focus:border-teal-500 focus:bg-white transition-all">
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                            <button type="submit" class="btn-lift px-6 py-3 bg-slate-900 hover:bg-slate-800 text-white text-xs font-extrabold rounded-2xl shadow-md cursor-pointer flex items-center gap-2">
                                <span>🔑</span> <?php echo e(app()->getLocale() === 'ar' ? 'تحديث كلمة المرور' : 'Update Password'); ?>

                            </button>
                        </div>
                    </form>
                </div>

            </div>

            
            <div class="lg:col-span-4 space-y-8 lg:space-y-10">

                
                <div class="glass-card rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm hover:shadow-lg transition-all space-y-6 animate-fade-in-up stagger-1">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <h3 class="font-heading font-black text-xl text-slate-900 flex items-center gap-2">
                            <span>💳</span> <?php echo e(app()->getLocale() === 'ar' ? 'باقة الحصص النشطة' : 'Active Package'); ?>

                        </h3>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activePackage): ?>
                            <span class="text-xs font-mono font-bold text-teal-800 bg-teal-50 px-3 py-1 rounded-full border border-teal-200 shadow-2xs">
                                ● Active
                            </span>
                        <?php else: ?>
                            <span class="text-xs font-mono font-bold text-rose-800 bg-rose-50 px-3 py-1 rounded-full border border-rose-200 shadow-2xs">
                                ✕ No Active Package
                            </span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activePackage): ?>
                        <div class="space-y-4">
                            <div class="p-5 bg-gradient-to-br from-teal-50/80 to-emerald-50/40 rounded-2xl border border-teal-200/80 space-y-3">
                                <h4 class="font-bold text-base text-slate-900">
                                    <?php echo e($activePackage->packageTemplate?->name ?: ($activePackage->course?->title ?: 'Standard Monthly Package')); ?>

                                </h4>

                                <div class="space-y-1.5">
                                    <div class="flex justify-between text-xs font-mono font-bold">
                                        <span class="text-slate-600"><?php echo e(app()->getLocale() === 'ar' ? 'الحصص المتبقية' : 'Sessions Remaining'); ?>:</span>
                                        <span class="text-teal-700 font-extrabold text-sm"><?php echo e($activePackage->remaining_sessions); ?> / <?php echo e($activePackage->total_sessions); ?></span>
                                    </div>
                                    <?php
                                        $percentRemaining = $activePackage->total_sessions > 0 ? round(($activePackage->remaining_sessions / $activePackage->total_sessions) * 100) : 0;
                                    ?>
                                    <div class="w-full bg-slate-200 rounded-full h-2.5 overflow-hidden">
                                        <div class="bg-teal-500 h-2.5 rounded-full transition-all duration-500" style="width: <?php echo e($percentRemaining); ?>%"></div>
                                    </div>
                                </div>

                                <div class="pt-2 border-t border-teal-200/60 flex items-center justify-between text-[11px] font-mono text-slate-600">
                                    <span>📅 <?php echo e(app()->getLocale() === 'ar' ? 'تاريخ التفعيل' : 'Activated'); ?>: <?php echo e($activePackage->activated_at ? $activePackage->activated_at->format('Y-m-d') : 'Active'); ?></span>
                                    <span>⏳ <?php echo e(app()->getLocale() === 'ar' ? 'تاريخ الانتهاء' : 'Expires'); ?>: <?php echo e($activePackage->expires_at ? $activePackage->expires_at->format('Y-m-d') : 'No Expiry'); ?></span>
                                </div>
                            </div>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($packageTransactions->count() > 0): ?>
                                <div class="space-y-2">
                                    <h5 class="font-bold text-xs font-mono text-slate-700 uppercase tracking-wider"><?php echo e(app()->getLocale() === 'ar' ? 'آخر المعاملات والخصومات' : 'Recent Transactions'); ?></h5>
                                    <div class="space-y-2">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $packageTransactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tx): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-xs font-mono flex items-center justify-between">
                                                <div class="space-y-0.5">
                                                    <span class="font-bold text-slate-900 block"><?php echo e(ucfirst($tx->type)); ?></span>
                                                    <span class="text-[10px] text-slate-400"><?php echo e($tx->created_at->diffForHumans()); ?></span>
                                                </div>
                                                <span class="font-bold <?php echo e($tx->session_change < 0 ? 'text-rose-600' : 'text-emerald-600'); ?>">
                                                    <?php echo e($tx->session_change > 0 ? "+{$tx->session_change}" : $tx->session_change); ?> credits
                                                </span>
                                            </div>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <a href="<?php echo e(route('courses')); ?>" class="btn-lift w-full py-3 bg-teal-600 hover:bg-teal-700 text-white rounded-2xl font-bold text-xs shadow-md shadow-teal-600/30 text-center block">
                                🔄 <?php echo e(app()->getLocale() === 'ar' ? 'تجديد أو ترقية الباقة' : 'Renew / Upgrade Package'); ?>

                            </a>
                        </div>
                    <?php else: ?>
                        <div class="p-6 bg-slate-50 rounded-2xl border border-slate-200 text-center space-y-3">
                            <div class="text-3xl">💳</div>
                            <p class="text-xs font-mono text-slate-600"><?php echo e(app()->getLocale() === 'ar' ? 'لا توجد باقة حصص نشطة مرتبطة بحسابك حالياً.' : 'No active session package linked to your account.'); ?></p>
                            <a href="<?php echo e(route('courses')); ?>" class="btn-lift px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl font-bold text-xs shadow-md shadow-rose-600/30 inline-block">
                                🛒 <?php echo e(app()->getLocale() === 'ar' ? 'تصفح الباقات والكورسات' : 'Browse Packages & Courses'); ?>

                            </a>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                
                <div class="glass-card rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm hover:shadow-lg transition-all space-y-5 animate-fade-in-up stagger-2">
                    <h3 class="font-heading font-black text-xl text-slate-900 flex items-center gap-2 border-b border-slate-100 pb-3">
                        <span>👨‍👩‍👦</span> <?php echo e(app()->getLocale() === 'ar' ? 'بيانات ولي الأمر المرتبط' : 'Linked Parent / Guardian'); ?>

                    </h3>

                    <div class="space-y-3">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $parents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $parent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <div class="p-4 bg-slate-50/90 rounded-2xl border border-slate-200 space-y-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-xs text-slate-900"><?php echo e($parent->name); ?></span>
                                    <span class="text-[10px] font-mono font-bold bg-teal-100 text-teal-900 px-2 py-0.5 rounded-full">Linked</span>
                                </div>
                                <p class="text-xs font-mono text-slate-500">📱 <?php echo e($parent->phone ?: $parent->email); ?></p>
                            </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 text-xs font-mono text-slate-500 text-center space-y-1">
                                <div>👨‍👩‍👦</div>
                                <div><?php echo e(app()->getLocale() === 'ar' ? 'لم يتم ربط حساب ولي أمر بهذا الحساب بعد.' : 'No parent account linked yet.'); ?></div>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

            </div>
        </div>

    </div>
</section>

<?php $__env->startPush('scripts'); ?>
<script>
function previewAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const avatarImgs = document.querySelectorAll('.group img, .group div');
            avatarImgs.forEach(el => {
                if (el.tagName === 'IMG') {
                    el.src = e.target.result;
                }
            });
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\elite-academy\resources\views\pages\student-profile.blade.php ENDPATH**/ ?>