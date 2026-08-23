<?php $__env->startSection('content'); ?>
<section class="py-12 md:py-20 px-4 bg-[#FAFAF9]">
    <div class="max-w-md mx-auto bg-white rounded-3xl p-8 sm:p-10 border border-slate-200/90 shadow-xl space-y-6">
        <div class="text-center space-y-2">
            <div class="w-14 h-14 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center text-2xl font-bold mx-auto border border-teal-100 shadow-xs">🎓</div>
            <h1 class="font-heading font-black text-2xl sm:text-3xl text-slate-900 tracking-tight">
                <?php echo e(app()->getLocale() === 'ar' ? 'إنشاء حساب جديد' : 'Create an Account'); ?>

            </h1>
            <p class="text-xs text-slate-500 font-mono">
                <?php echo e(app()->getLocale() === 'ar' ? 'انضم إلى أكاديمية النخبة وابدأ رحلة التعلم التفاعلي.' : 'Join Elite Academy and start interactive learning.'); ?>

            </p>
        </div>

        
        <div id="authAlert" class="hidden p-3.5 rounded-2xl text-xs font-semibold"></div>

        
        <form id="registerForm" action="<?php echo e(route('ajax.register')); ?>" method="POST" class="space-y-4">
            <?php echo csrf_field(); ?>
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-700"><?php echo e(app()->getLocale() === 'ar' ? 'الاسم بالكامل' : 'Full Name'); ?></label>
                <input type="text" name="name" required placeholder="<?php echo e(app()->getLocale() === 'ar' ? 'مثل: أحمد محمود' : 'e.g. David Kovacs'); ?>" class="input-mobile">
            </div>

            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-700"><?php echo e(app()->getLocale() === 'ar' ? 'البريد الإلكتروني' : 'Email Address'); ?></label>
                <input type="email" name="email" required placeholder="name@example.com" class="input-mobile">
            </div>

            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-700 flex items-center justify-between">
                    <span><?php echo e(app()->getLocale() === 'ar' ? 'رقم الهاتف' : 'Phone Number'); ?></span>
                    <span class="text-[10px] text-rose-500 font-bold">* <?php echo e(app()->getLocale() === 'ar' ? 'مطلوب لربط ولي الأمر' : 'Required'); ?></span>
                </label>
                <input type="tel" name="phone" required placeholder="<?php echo e(app()->getLocale() === 'ar' ? '01000000000' : '+1234567890'); ?>" class="input-mobile">
            </div>

            <div class="space-y-4 pt-1">
                <label class="text-xs font-bold text-slate-700 flex items-center justify-between">
                    <span><?php echo e(app()->getLocale() === 'ar' ? 'نوع الحساب' : 'Account Type'); ?></span>
                    <span class="text-[10px] font-semibold text-teal-600 bg-teal-50 px-2.5 py-0.5 rounded-full border border-teal-100/80">
                        <?php echo e(app()->getLocale() === 'ar' ? 'حدد دورك' : 'Select Role'); ?>

                    </span>
                </label>

                <div class="grid grid-cols-3 gap-2.5 sm:gap-3 py-1">
                    <!-- Student Card (Default Checked) -->
                    <label class="relative group cursor-pointer select-none block">
                        <input type="radio" name="user_type" value="student" checked class="peer account-type-radio" style="position: absolute; opacity: 0; width: 0; height: 0; pointer-events: none;" onchange="toggleStudentGrade(this.value)">
                        <div class="h-full p-3.5 sm:p-4 rounded-2xl border-2 border-slate-200 bg-white hover:border-teal-400 transition-all duration-200 ease-out flex flex-col items-center justify-center text-center space-y-1.5 peer-checked:border-teal-600 peer-checked:bg-teal-50/50 peer-checked:shadow-md peer-checked:shadow-teal-500/10 peer-checked:ring-2 peer-checked:ring-teal-500/20 active:scale-95">
                            <div class="text-2xl sm:text-3xl transition-transform duration-200 group-hover:scale-110 peer-checked:scale-110">🎓</div>
                            <span class="text-xs font-bold text-slate-700 peer-checked:text-teal-900 transition-colors">
                                <?php echo e(app()->getLocale() === 'ar' ? 'طالب' : 'Student'); ?>

                            </span>
                        </div>
                    </label>

                    <!-- Parent Card -->
                    <label class="relative group cursor-pointer select-none block">
                        <input type="radio" name="user_type" value="parent" class="peer account-type-radio" style="position: absolute; opacity: 0; width: 0; height: 0; pointer-events: none;" onchange="toggleStudentGrade(this.value)">
                        <div class="h-full p-3.5 sm:p-4 rounded-2xl border-2 border-slate-200 bg-white hover:border-teal-400 transition-all duration-200 ease-out flex flex-col items-center justify-center text-center space-y-1.5 peer-checked:border-teal-600 peer-checked:bg-teal-50/50 peer-checked:shadow-md peer-checked:shadow-teal-500/10 peer-checked:ring-2 peer-checked:ring-teal-500/20 active:scale-95">
                            <div class="text-2xl sm:text-3xl transition-transform duration-200 group-hover:scale-110 peer-checked:scale-110">👨‍👩‍👧</div>
                            <span class="text-xs font-bold text-slate-700 peer-checked:text-teal-900 transition-colors">
                                <?php echo e(app()->getLocale() === 'ar' ? 'ولي أمر' : 'Parent'); ?>

                            </span>
                        </div>
                    </label>

                    <!-- Teacher Card -->
                    <label class="relative group cursor-pointer select-none block">
                        <input type="radio" name="user_type" value="teacher" class="peer account-type-radio" style="position: absolute; opacity: 0; width: 0; height: 0; pointer-events: none;" onchange="toggleStudentGrade(this.value)">
                        <div class="h-full p-3.5 sm:p-4 rounded-2xl border-2 border-slate-200 bg-white hover:border-teal-400 transition-all duration-200 ease-out flex flex-col items-center justify-center text-center space-y-1.5 peer-checked:border-teal-600 peer-checked:bg-teal-50/50 peer-checked:shadow-md peer-checked:shadow-teal-500/10 peer-checked:ring-2 peer-checked:ring-teal-500/20 active:scale-95">
                            <div class="text-2xl sm:text-3xl transition-transform duration-200 group-hover:scale-110 peer-checked:scale-110">👨‍🏫</div>
                            <span class="text-xs font-bold text-slate-700 peer-checked:text-teal-900 transition-colors">
                                <?php echo e(app()->getLocale() === 'ar' ? 'معلم' : 'Teacher'); ?>

                            </span>
                        </div>
                    </label>
                </div>
            </div>

            
            <div id="studentFieldsGroup" class="space-y-4 pt-2">
                
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-700 flex items-center justify-between">
                        <span><?php echo e(app()->getLocale() === 'ar' ? 'الصف الدراسي' : 'Grade Level'); ?></span>
                        <span class="text-[10px] text-teal-600 font-bold">* <?php echo e(app()->getLocale() === 'ar' ? 'مطلوب للطالب' : 'Required for Student'); ?></span>
                    </label>
                    <select name="grade_level_id" class="w-full bg-white border border-slate-200 rounded-2xl px-4 py-3 text-xs font-bold text-slate-800 focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 shadow-xs">
                        <option value=""><?php echo e(app()->getLocale() === 'ar' ? 'اختر الصف الدراسي...' : 'Select Grade Level...'); ?></option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $gradeLevels ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <option value="<?php echo e($g->id); ?>"><?php echo e($g->name); ?></option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </select>
                </div>

                
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-700"><?php echo e(app()->getLocale() === 'ar' ? 'اسم المدرسة (اختياري)' : 'School Name (Optional)'); ?></label>
                    <input type="text" name="school_name" placeholder="<?php echo e(app()->getLocale() === 'ar' ? 'مثل: مدرسة المتفوقين STEM' : 'e.g. Cairo International School'); ?>" class="input-mobile">
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-700"><?php echo e(app()->getLocale() === 'ar' ? 'كلمة المرور' : 'Password'); ?></label>
                <input type="password" name="password" required placeholder="••••••••" class="input-mobile">
            </div>

            <button type="submit" id="submitBtn" class="w-full btn-mobile-lg btn-lift text-white bg-teal-600 hover:bg-teal-700 shadow-md shadow-teal-600/20 touch-press mt-2 flex items-center justify-center gap-2 font-bold py-3.5 rounded-2xl">
                <span><?php echo e(app()->getLocale() === 'ar' ? 'إنشاء الحساب والانضمام' : 'Create Account & Start'); ?></span>
                <span class="arrow-icon">&rarr;</span>
            </button>
        </form>

        
        <div class="pt-6 border-t border-slate-100 text-center">
            <p class="text-xs text-slate-500 font-mono">
                <?php echo e(app()->getLocale() === 'ar' ? 'لديك حساب بالفعل؟' : 'Already have an account?'); ?>

                <a href="<?php echo e(route('login')); ?>" class="font-bold text-teal-600 hover:text-teal-700 hover:underline ml-1">
                    <?php echo e(app()->getLocale() === 'ar' ? 'تسجيل الدخول ←' : 'Log In to Portal &rarr;'); ?>

                </a>
            </p>
        </div>
    </div>
</section>

<script>
function toggleStudentGrade(val) {
    const studentGroup = document.getElementById('studentFieldsGroup');
    if (studentGroup) {
        studentGroup.style.display = (val === 'student') ? 'block' : 'none';
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const alertBox = document.getElementById('authAlert');
    const form = document.getElementById('registerForm');
    const submitBtn = document.getElementById('submitBtn');
    
    function showNotification(msg, isError = true, title = null) {
        if (window.Toast) {
            if (isError) {
                window.Toast.error(msg, title || (document.documentElement.lang === 'ar' ? 'فشل إنشاء الحساب' : 'Registration Failed'));
            } else {
                window.Toast.success(msg, title || (document.documentElement.lang === 'ar' ? 'تم إنشاء الحساب' : 'Registration Successful'));
            }
        } else if (alertBox) {
            alertBox.className = `p-3.5 rounded-2xl text-xs font-semibold ${isError ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200'}`;
            alertBox.textContent = msg;
            alertBox.classList.remove('hidden');
        }
    }

    if (form) {
        form.addEventListener('submit', async function (e) {
            e.preventDefault();
            if (alertBox) alertBox.classList.add('hidden');

            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-75', 'cursor-not-allowed');

            try {
                const formData = new FormData(form);
                const res = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();

                if (!res.ok || !data.success) {
                    let errMsg = data.message || (document.documentElement.lang === 'ar' ? 'فشل إنشاء الحساب. يرجى التأكد من الحقول المدخلة.' : 'Registration failed. Please check input fields.');
                    if (data.errors) {
                        const errs = Object.values(data.errors).flat();
                        if (errs.length > 0) errMsg = errs;
                    }
                    showNotification(errMsg, true);
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
                    return;
                }

                showNotification(data.message || (document.documentElement.lang === 'ar' ? 'تم إنشاء الحساب بنجاح! جاري التوجيه...' : 'Account created successfully! Redirecting...'), false);
                
                setTimeout(() => {
                    window.location.href = data.redirect_url || '/student-portal';
                }, 900);
            } catch (err) {
                showNotification(document.documentElement.lang === 'ar' ? 'حدث خطأ في الاتصال بالشبكة. يرجى المحاولة لاحقاً.' : 'Network connection error. Please try again.', true);
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
            }
        });
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\elite-academy\resources\views/pages/register.blade.php ENDPATH**/ ?>