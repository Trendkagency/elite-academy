<?php

namespace Database\Seeders;

use App\Models\HeroSlide;
use App\Models\SiteSetting;
use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class CMSSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Hero Slides ───────────────────────────────────────────────────
        HeroSlide::firstOrCreate(
            ['title' => 'Build Your Future Through Technology.'],
            [
                'track_label' => '💻 Programming & Tech Track',
                'subtitle' => 'Master programming with industry experts through hands-on projects and interactive cohorts.',
                'image' => 'images/hero_student.png',
                'cta_primary_url' => '/subjects',
                'cta_secondary_url' => '/register',
                'sort_order' => 1,
                'is_active' => true,
            ]
        );

        HeroSlide::firstOrCreate(
            ['title' => 'Pioneer Next-Gen Artificial Intelligence.'],
            [
                'track_label' => '🤖 Artificial Intelligence & ML Track',
                'subtitle' => 'Dive deep into Neural Networks, Machine Learning models, and autonomous AI systems.',
                'image' => 'images/course_ai.png',
                'cta_primary_url' => '/subjects',
                'cta_secondary_url' => '/courses',
                'sort_order' => 2,
                'is_active' => true,
            ]
        );

        HeroSlide::firstOrCreate(
            ['title' => 'Empower Your Business & Enterprise Mindset.'],
            [
                'track_label' => '📊 Business & Leadership Track',
                'subtitle' => 'Learn financial modeling, startup acceleration, and modern business strategy from PhD mentors.',
                'image' => 'images/instructor_male.png',
                'cta_primary_url' => '/courses',
                'cta_secondary_url' => '/about',
                'sort_order' => 3,
                'is_active' => true,
            ]
        );

        // ── 2. Testimonials (Student & Parent Reviews) ────────────────────────
        Testimonial::firstOrCreate(
            ['name' => 'Youssef Mansour'],
            [
                'reviewer_type' => 'student',
                'course_name' => 'AI & Python Development',
                'rating' => 5,
                'content' => 'Elite Academy transformed how I prepare for my Thanawya Amma exams. Dr. Ahmed’s live coding sessions are clear, practical, and highly engaging!',
                'avatar' => 'images/instructor_portrait.png',
                'is_featured' => true,
                'is_verified' => true,
                'sort_order' => 1,
            ]
        );

        Testimonial::firstOrCreate(
            ['name' => 'Eng. Mohamed Abdel-Rahman'],
            [
                'reviewer_type' => 'parent',
                'course_name' => 'Parent of Secondary Student (Grade 12)',
                'rating' => 5,
                'content' => 'As a parent, I can track my daughter’s progress, quiz scores, and live session attendance in real-time. Exceptional transparency and quality!',
                'avatar' => 'images/instructor_male.png',
                'is_featured' => true,
                'is_verified' => true,
                'sort_order' => 2,
            ]
        );

        Testimonial::firstOrCreate(
            ['name' => 'Nour El-Din Sherif'],
            [
                'reviewer_type' => 'student',
                'course_name' => 'Physics & Quantum Mechanics',
                'rating' => 5,
                'content' => 'The MCQ assignments and instant AI feedback helped me score top grades in Physics. Highly recommended for all Egyptian secondary students!',
                'avatar' => 'images/instructor_female.png',
                'is_featured' => true,
                'is_verified' => true,
                'sort_order' => 3,
            ]
        );

        // ── 3. Global Site Settings ──────────────────────────────────────────
        $settings = [
            'landing_hero_badge' => '🚀 EGYPT’S #1 ACADEMIC PLATFORM',
            'landing_hero_title' => 'Empowering Future Leaders with Practical Academic Excellence',
            'landing_hero_subtitle' => 'Join thousands of students learning Programming, Artificial Intelligence, Science, and Business from Egypt’s top educators.',
            'landing_cta_primary_text' => 'Explore All Subjects →',
            'landing_cta_primary_link' => '/subjects',
            'announcement_enabled' => '1',
            'announcement_text' => '🎉 Fall Cohort 2026 Registration is Now Open! Enroll in Live Stream Sessions.',
            'announcement_link' => '/courses',
            'about_badge' => 'REDEFINING EDUCATION',
            'about_title' => 'Where Passion Meets Academic Mastery',
            'about_content' => 'Elite Academy bridges secondary education and real-world innovation through interactive live streams, structured MCQs, and expert teacher mentorship.',
            'cta_headline' => 'Ready to Excel in Your Academic Journey?',
            'cta_subtitle' => 'Join Elite Academy today and gain unlimited access to top teachers, interactive live streams, and accredited courses.',
            'contact_phone' => '+20 100 000 0000',
            'contact_email' => 'info@eliteacademy.edu.eg',
            'contact_address' => 'New Cairo, Egypt',
            'social_facebook' => 'https://facebook.com',
            'social_twitter' => 'https://twitter.com',
            'social_instagram' => 'https://instagram.com',
            'social_linkedin' => 'https://linkedin.com',
            'social_youtube' => 'https://youtube.com',
            'theme_primary_color' => '#0d9488',
        ];

        foreach ($settings as $k => $v) {
            SiteSetting::set($k, $v, 'landing');
        }
    }
}
