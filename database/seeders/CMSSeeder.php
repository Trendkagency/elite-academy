<?php

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\FaqCategory;
use App\Models\HeroSlide;
use App\Models\SiteSetting;
use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class CMSSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Hero Slides
        HeroSlide::updateOrCreate(
            ['sort_order' => 1],
            [
                'title' => 'Empowering Future Leaders with Practical Academic Excellence',
                'subtitle' => 'Join thousands of students learning Programming, Artificial Intelligence, Science, and Business from Egypt’s top educators.',
                'image' => 'images/hero_student.png',
                'track_label' => '🚀 EGYPT’S #1 ACADEMIC PLATFORM',
                'cta_primary_url' => '/subjects',
                'cta_secondary_url' => '/register',
                'is_active' => true,
            ]
        );

        HeroSlide::updateOrCreate(
            ['sort_order' => 2],
            [
                'title' => 'Learn Artificial Intelligence. Shape Tomorrow.',
                'subtitle' => 'Explore Machine Learning, Deep Neural Networks, and modern computer vision.',
                'image' => 'images/course_ai.png',
                'track_label' => '🧠 Artificial Intelligence Track',
                'cta_primary_url' => '/courses',
                'cta_secondary_url' => '/subjects',
                'is_active' => true,
            ]
        );

        HeroSlide::updateOrCreate(
            ['sort_order' => 3],
            [
                'title' => 'Build. Create. Innovate.',
                'subtitle' => 'Design real robots and autonomous engineering hardware inside state-of-the-art labs.',
                'image' => 'images/instructor_male.png',
                'track_label' => '🤖 Robotics Track',
                'cta_primary_url' => '/subjects',
                'cta_secondary_url' => '/events',
                'is_active' => true,
            ]
        );

        HeroSlide::updateOrCreate(
            ['sort_order' => 4],
            [
                'title' => 'Curiosity Creates Excellence.',
                'subtitle' => 'Interactive science and mathematics education designed to build problem-solving mindsets.',
                'image' => 'images/academy_campus.png',
                'track_label' => '🔬 Science & Math Track',
                'cta_primary_url' => '/subjects',
                'cta_secondary_url' => '/register',
                'is_active' => true,
            ]
        );

        // 2. Site Settings
        $settings = [
            // Contact Page Settings
            'contact_hero_badge' => 'STUDENT & PARENT SUPPORT',
            'contact_hero_title' => 'We Are Always Here To Help',
            'contact_hero_subtitle' => 'Have questions regarding curriculum enrollment, parent progress dashboards, or scheduling a campus visit? Reach out to our dedicated support advisors.',
            'contact_hero_image' => 'images/academy_campus.png',
            'contact_card_title' => 'Support Desk 24/7',
            'contact_card_subtitle' => 'Direct Academic Assistance',
            'contact_card_icon' => '🎧',
            'contact_phone' => '+20 100 123 4567',
            'contact_whatsapp' => '+20 100 123 4568',
            'owner_whatsapp' => '+20 100 000 0000',
            'contact_email' => 'support@elite-academy.edu.eg',
            'contact_address' => 'New Cairo Hub, Egypt',
            'contact_form_title' => 'Send Us a Message',
            'contact_form_subtitle' => 'Our student advisors will respond within 24 hours.',
            'contact_map_iframe_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d55251.33660578643!2d31.470000000000002!3d30.030000000000005!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x145822ee00000001%3A0x1000000000000000!2sNew%20Cairo%2C%20Cairo%20Governorate%2C%20Egypt!5e0!3m2!1sen!2seg!4v1700000000000!5m2!1sen!2seg',

            // About Page Settings
            'about_hero_badge' => 'ACCREDITED EXCELLENCE • EST. 2020',
            'about_hero_title' => 'Transforming Academic Education For Future Leaders',
            'about_hero_subtitle' => 'Elite Academy combines accredited national curricula with interactive video sessions, real-time mentor Q&A, and parent progress tracking.',
            'about_mission_title' => 'Our Core Educational Mission',
            'about_mission_text' => 'To deliver accessible, high-quality, and interactive learning experiences that empower secondary students to excel in national and international examinations.',
            'about_vision_title' => 'Our Vision For Tomorrow',
            'about_vision_text' => 'Empowering 100,000+ students across Egypt and the MENA region with personalized academic paths, expert faculty mentorship, and innovative digital learning tools.',
            'about_stat_students' => '25,000+',
            'about_stat_courses' => '120+',
            'about_stat_teachers' => '45+',
            'about_stat_pass_rate' => '98.5%',

            // Landing Page Settings
            'landing_hero_badge' => '🚀 EGYPT’S #1 ACADEMIC PLATFORM',
            'landing_hero_title' => 'Empowering Future Leaders with Practical Academic Excellence',
            'landing_hero_subtitle' => 'Join thousands of students learning Programming, Artificial Intelligence, Science, and Business from Egypt’s top educators.',
            'landing_cta_primary_text' => 'Explore All Subjects',
        ];

        foreach ($settings as $key => $value) {
            SiteSetting::set($key, $value, str_starts_with($key, 'contact_') ? 'contact' : (str_starts_with($key, 'about_') ? 'about' : 'landing'));
        }

        // 3. FAQ Categories & FAQs
        $catGeneral = FaqCategory::updateOrCreate(['name' => 'General & Admissions'], ['sort_order' => 1]);
        $catQuizzes = FaqCategory::updateOrCreate(['name' => 'Assignments & Quizzes'], ['sort_order' => 2]);
        $catParents = FaqCategory::updateOrCreate(['name' => 'Parent Portal & Progress'], ['sort_order' => 3]);

        Faq::updateOrCreate(
            ['question' => 'ما هي أكاديمية إيليت التعليمية؟'],
            [
                'faq_category_id' => $catGeneral->id,
                'answer' => 'أكاديمية إيليت هي المنصة التعليمية الرقمية الرائدة في مصر لحصص البث المباشر المعتمدة، متابعة أولياء الأمور، والحل التفاعلي للواجبات بصفة لحظية.',
                'sort_order' => 1,
                'is_active' => true,
            ]
        );

        Faq::updateOrCreate(
            ['question' => 'كيف تعمل حصص البث المباشر والتفاعلي؟'],
            [
                'faq_category_id' => $catGeneral->id,
                'answer' => 'يتصل الطلاب بالبث التفاعلي عبر زوم أو جيتسي مع علامة مائية أمنية ديناميكية لحماية المحتوى وتسجيل حضور تلقائي بالدقيقة.',
                'sort_order' => 2,
                'is_active' => true,
            ]
        );

        Faq::updateOrCreate(
            ['question' => 'هل يتم تصحيح الواجبات والاختبارات تلقائياً؟'],
            [
                'faq_category_id' => $catQuizzes->id,
                'answer' => 'نعم، تشمل المنصة واجهة تفاعلية لحل الواجبات مع حفظ المسودات تلقائياً وإمكانية التصحيح الفوري وإرسال التغذية الراجعة.',
                'sort_order' => 3,
                'is_active' => true,
            ]
        );

        Faq::updateOrCreate(
            ['question' => 'كيف يمكن لأولياء الأمور متابعة مستوى الطالب؟'],
            [
                'faq_category_id' => $catParents->id,
                'answer' => 'يمكن لولي الأمر ربط حساب الطالب برقم الهاتف عبر بوابة ولي الأمر لمتابعة نسبة الحضور، درجات الاختبارات، وتنبيهات الغياب اللحظية.',
                'sort_order' => 4,
                'is_active' => true,
            ]
        );

        // 4. Testimonials
        Testimonial::updateOrCreate(
            ['name' => 'أحمد الفاروق'],
            [
                'avatar' => 'images/instructor_male.png',
                'content' => 'أكاديمية إيليت ساعدتني جداً في استيعاب الفيزياء الكهربية ومتابعة المدرس أولاً بأول.',
                'course_name' => 'كورس الفيزياء الكهربية والمغناطيسية',
                'rating' => 5,
                'reviewer_type' => 'student',
                'is_verified' => true,
                'is_featured' => true,
                'sort_order' => 1,
            ]
        );

        Testimonial::updateOrCreate(
            ['name' => 'د. خالد عبد المجيد'],
            [
                'avatar' => 'images/hero_student.png',
                'content' => 'منصة ممتازة تمكنت من خلال بوابة ولي الأمر متابعة حضور ابنتي مريم ودرجات الواجبات بانتظام.',
                'course_name' => 'بوابة متابعة ولي الأمر',
                'rating' => 5,
                'reviewer_type' => 'parent',
                'is_verified' => true,
                'is_featured' => true,
                'sort_order' => 2,
            ]
        );

        Testimonial::updateOrCreate(
            ['name' => 'مريم السعيد'],
            [
                'avatar' => 'images/instructor_female.png',
                'content' => 'الحصص التفاعلية واختبارات البرمجة التفاعلية ممتازة جداً ومفيدة لبناء المستقببل.',
                'course_name' => 'كورس البرمجة والذكاء الاصطناعي',
                'rating' => 5,
                'reviewer_type' => 'student',
                'is_verified' => true,
                'is_featured' => true,
                'sort_order' => 3,
            ]
        );
    }
}
