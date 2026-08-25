<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\View\View;

class AboutController extends Controller
{
    public function show(): View
    {
        $aboutSettings = [
            'hero_badge' => SiteSetting::get('about_hero_badge', 'ACCREDITED EXCELLENCE • EST. 2020'),
            'hero_title' => SiteSetting::get('about_hero_title', 'Transforming Academic Education For Future Leaders'),
            'hero_subtitle' => SiteSetting::get('about_hero_subtitle', 'Elite Academy combines accredited national curricula with interactive video sessions, real-time mentor Q&A, and parent progress tracking.'),
            'mission_title' => SiteSetting::get('about_mission_title', 'Our Core Educational Mission'),
            'mission_text' => SiteSetting::get('about_mission_text', 'To deliver accessible, high-quality, and interactive learning experiences that empower secondary students to excel in national and international examinations.'),
            'vision_title' => SiteSetting::get('about_vision_title', 'Our Vision For Tomorrow'),
            'vision_text' => SiteSetting::get('about_vision_text', 'Empowering 100,000+ students across Egypt and the MENA region with personalized academic paths, expert faculty mentorship, and innovative digital learning tools.'),
            'stat_students' => SiteSetting::get('about_stat_students', '25,000+'),
            'stat_courses' => SiteSetting::get('about_stat_courses', '120+'),
            'stat_teachers' => SiteSetting::get('about_stat_teachers', '45+'),
            'stat_pass_rate' => SiteSetting::get('about_stat_pass_rate', '98.5%'),
        ];

        return view('pages.about', [
            'pageTitle' => __('About Us — Elite Academy'),
            'activeNav' => 'about',
            'aboutSettings' => $aboutSettings,
        ]);
    }
}
