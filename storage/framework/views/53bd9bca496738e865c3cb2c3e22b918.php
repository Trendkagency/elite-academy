<?php $__env->startSection('content'); ?>
<?php
    $heroBadge = $contactSettings['hero_badge'] ?? 'STUDENT & PARENT SUPPORT';
    $heroTitle = $contactSettings['hero_title'] ?? 'We Are Always Here To Help';
    $heroSubtitle = $contactSettings['hero_subtitle'] ?? 'Have questions regarding curriculum enrollment, parent progress dashboards, or scheduling a campus visit? Reach out to our dedicated support advisors.';
    $phone = $contactSettings['phone'] ?? '+20 100 123 4567';
    $whatsapp = $contactSettings['whatsapp'] ?? '+20 100 123 4568';
    $email = $contactSettings['email'] ?? 'support@elite-academy.edu.eg';
    $address = $contactSettings['address'] ?? 'New Cairo Hub, Egypt';
    $mapUrl = $contactSettings['map_url'] ?? 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d55251.33660578643!2d31.470000000000002!3d30.030000000000005!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x145822ee00000001%3A0x1000000000000000!2sNew%20Cairo%2C%20Cairo%20Governorate%2C%20Egypt!5e0!3m2!1sen!2seg!4v1700000000000!5m2!1sen!2seg';
?>

<section class="relative py-16 md:py-24 bg-white border-b border-slate-200/80 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <?php echo $__env->make('components.breadcrumb', [
            'items' => [
                ['label' => __('navbar.home'), 'route' => 'home'],
                ['label' => __('navbar.contact')],
            ]
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            <div class="lg:col-span-6 space-y-6">
                <span class="inline-block text-xs font-mono uppercase tracking-widest text-teal-600 font-extrabold bg-teal-50 px-3.5 py-1.5 rounded-full border border-teal-200/80">
                    <?php echo e($heroBadge); ?>

                </span>

                <h1 class="font-heading text-4xl sm:text-5xl font-black text-slate-900 tracking-tight leading-tight">
                    <?php echo $heroTitle; ?>

                </h1>

                <p class="text-slate-600 text-base font-medium leading-relaxed">
                    <?php echo e($heroSubtitle); ?>

                </p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <div class="bg-[#FAFAF9] p-4 rounded-2xl border border-slate-200/80 space-y-1">
                        <span class="text-xs font-mono font-bold text-teal-600 uppercase">Phone Support</span>
                        <p class="font-extrabold text-slate-900 text-sm"><?php echo e($phone); ?></p>
                    </div>

                    <div class="bg-[#FAFAF9] p-4 rounded-2xl border border-slate-200/80 space-y-1">
                        <span class="text-xs font-mono font-bold text-teal-600 uppercase">WhatsApp Help</span>
                        <p class="font-extrabold text-slate-900 text-sm"><?php echo e($whatsapp); ?></p>
                    </div>

                    <div class="bg-[#FAFAF9] p-4 rounded-2xl border border-slate-200/80 space-y-1">
                        <span class="text-xs font-mono font-bold text-teal-600 uppercase">Support Email</span>
                        <p class="font-extrabold text-slate-900 text-sm truncate"><?php echo e($email); ?></p>
                    </div>

                    <div class="bg-[#FAFAF9] p-4 rounded-2xl border border-slate-200/80 space-y-1">
                        <span class="text-xs font-mono font-bold text-teal-600 uppercase">Campus Location</span>
                        <p class="font-extrabold text-slate-900 text-sm truncate"><?php echo e($address); ?></p>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-6 relative">
                <div class="rounded-3xl overflow-hidden shadow-2xl border-4 border-white h-[420px] bg-slate-950">
                    <img src="<?php echo e(asset('images/academy_campus.png')); ?>" alt="Campus Support Desk" class="w-full h-full object-cover">
                </div>
                <div class="absolute -bottom-6 -left-6 bg-teal-600 text-white p-5 rounded-2xl shadow-2xl flex items-center gap-3">
                    <span class="text-3xl">🎧</span>
                    <div>
                        <p class="font-heading font-black text-lg">24/7 Support Desk</p>
                        <p class="text-xs font-mono text-teal-100">Direct Academic Assistance</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<section class="py-20 md:py-28 bg-[#FAFAF9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-2 gap-12">
        <div class="bg-white rounded-3xl overflow-hidden border border-slate-200/90 shadow-xl h-96 relative">
            <iframe title="Campus Location Map" src="<?php echo e($mapUrl); ?>" class="w-full h-full border-0" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>

        <div class="bg-white rounded-3xl p-8 sm:p-10 border border-slate-200/90 shadow-xl space-y-6">
            <div class="space-y-2">
                <h2 class="font-heading text-2xl sm:text-3xl font-black text-slate-900">Send Us a Message</h2>
                <p class="text-xs font-mono text-slate-500">Our student advisors will respond within 24 hours.</p>
            </div>

            <div id="contactAlert" class="hidden p-4 rounded-2xl text-xs font-semibold"></div>

            <form id="contactForm" action="<?php echo e(route('ajax.contact.submit')); ?>" method="POST" class="space-y-4">
                <?php echo csrf_field(); ?>
                <div>
                    <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">Full Name</label>
                    <input type="text" name="full_name" placeholder="e.g. Ahmed Mohamed" required class="input-mobile">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">Email Address</label>
                        <input type="email" name="email" placeholder="name@example.com" required class="input-mobile">
                    </div>
                    <div>
                        <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">Phone Number</label>
                        <input type="tel" name="phone" placeholder="+20 100..." class="input-mobile">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">Subject of Inquiry</label>
                    <input type="text" name="subject" placeholder="e.g. Grade 10 Mathematics Enrollment Inquiry" class="input-mobile">
                </div>

                <div>
                    <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">Message</label>
                    <textarea name="message" rows="4" placeholder="How can we help you?" required class="w-full bg-white border border-slate-200 rounded-xl p-4 text-[16px] font-medium focus:outline-none focus:border-teal-600 focus:ring-2 focus:ring-teal-500/20"></textarea>
                </div>

                <button type="submit" class="btn-mobile-lg btn-lift bg-teal-600 hover:bg-teal-700 text-white font-extrabold shadow-lg shadow-teal-600/20 cursor-pointer touch-press">
                    Submit Inquiry &rarr;
                </button>
            </form>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const contactForm = document.getElementById('contactForm');
    const contactAlert = document.getElementById('contactAlert');
    if (!contactForm) return;

    contactForm.addEventListener('submit', async function (e) {
        e.preventDefault();
        contactAlert.classList.add('hidden');
        const formData = new FormData(contactForm);

        try {
            const res = await fetch(contactForm.action, {
                method: 'POST',
                body: formData,
                headers: { 'Accept': 'application/json' }
            });
            const data = await res.json();

            contactAlert.className = `p-4 rounded-2xl text-xs font-semibold ${data.success ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-red-50 text-red-700 border border-red-200'}`;
            contactAlert.textContent = data.message;
            contactAlert.classList.remove('hidden');

            if (data.success) {
                contactForm.reset();
            }
        } catch (err) {
            contactAlert.className = 'p-4 rounded-2xl text-xs font-semibold bg-red-50 text-red-700 border border-red-200';
            contactAlert.textContent = 'Network error. Please try again.';
            contactAlert.classList.remove('hidden');
        }
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\elite-academy\resources\views/pages/contact.blade.php ENDPATH**/ ?>