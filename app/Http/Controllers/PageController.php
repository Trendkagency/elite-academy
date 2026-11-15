<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class PageController extends Controller
{
    /** @var array<string, array{view: string, title: string, description?: string, active?: string}> */
    private const PAGES = [
        'home' => [
            'view' => 'pages.home',
            'title' => 'Digital Learning Platform',
            'description' => 'A premier educational platform offering world-class courses in Programming, AI, Mathematics, Science, Languages, and Design.',
            'active' => 'home',
        ],
        'about' => [
            'view' => 'pages.about',
            'title' => 'About Us',
            'description' => 'Redefining school education in Egypt through practical learning, STEM innovation, and expert mentorship.',
            'active' => 'about',
        ],
        'subjects' => [
            'view' => 'pages.subjects',
            'title' => 'Subjects',
            'active' => 'subjects',
        ],
        'subject-details' => [
            'view' => 'pages.subject-details',
            'title' => 'Subject Details',
            'active' => 'subjects',
        ],
        'teachers' => [
            'view' => 'pages.teachers',
            'title' => 'Teachers',
            'active' => 'teachers',
        ],
        'teacher-profile' => [
            'view' => 'pages.teacher-profile',
            'title' => 'Teacher Profile',
            'active' => 'teachers',
        ],
        'events' => [
            'view' => 'pages.events',
            'title' => 'Events',
            'active' => 'events',
        ],
        'event-details' => [
            'view' => 'pages.event-details',
            'title' => 'Event Details',
            'active' => 'events',
        ],
        'blog' => [
            'view' => 'pages.blog',
            'title' => 'Blog',
            'active' => 'blog',
        ],
        'contact' => [
            'view' => 'pages.contact',
            'title' => 'Contact',
            'active' => 'contact',
        ],
        'faq' => [
            'view' => 'pages.faq',
            'title' => 'FAQ',
            'active' => 'faq',
        ],
        'student-portal' => [
            'view' => 'pages.student-portal',
            'title' => 'Student Portal',
            'active' => 'portal',
        ],
    ];

    public function show(Request $request): View
    {
        $page = $request->route()->defaults['page'] ?? 'home';

        if (! isset(self::PAGES[$page])) {
            abort(404);
        }

        $meta = self::PAGES[$page];

        $pageTitle = match ($page) {
            'home' => brand_name() . ' — ' . brand('tagline', 'Digital Learning Platform'),
            'about' => __('About Us') . ' — ' . brand_name(),
            'subjects' => __('Subjects') . ' — ' . brand_name(),
            'subject-details' => __('Subject Details') . ' — ' . brand_name(),
            'teachers' => __('Teachers') . ' — ' . brand_name(),
            'teacher-profile' => __('Teacher Profile') . ' — ' . brand_name(),
            'events' => __('Events') . ' — ' . brand_name(),
            'event-details' => __('Event Details') . ' — ' . brand_name(),
            'blog' => __('Blog') . ' — ' . brand_name(),
            'contact' => __('Contact') . ' — ' . brand_name(),
            'faq' => __('FAQ') . ' — ' . brand_name(),
            'student-portal' => __('Student Portal') . ' — ' . brand_name(),
            default => brand_interpolate($meta['title']),
        };
        $pageDescription = isset($meta['description']) ? brand_interpolate($meta['description']) : null;

        return view($meta['view'], [
            'pageTitle' => $pageTitle,
            'pageDescription' => $pageDescription,
            'activeNav' => $meta['active'] ?? null,
            'minimalLayout' => $meta['minimal'] ?? false,
        ]);
    }
}
