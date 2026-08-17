<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PageController extends Controller
{
    /** @var array<string, array{view: string, title: string, description?: string, active?: string}> */
    private const PAGES = [
        'home' => [
            'view' => 'pages.home',
            'title' => 'Elite Academy — Digital Learning Platform',
            'description' => 'Elite Academy is a premier educational platform offering world-class courses in Programming, AI, Mathematics, Science, Languages, and Design.',
            'active' => 'home',
        ],
        'about' => [
            'view' => 'pages.about',
            'title' => 'About Us — Elite Academy',
            'description' => 'Redefining school education in Egypt through practical learning, STEM innovation, and expert mentorship.',
            'active' => 'about',
        ],
        'subjects' => [
            'view' => 'pages.subjects',
            'title' => 'Subjects — Elite Academy',
            'active' => 'subjects',
        ],
        'subject-details' => [
            'view' => 'pages.subject-details',
            'title' => 'Subject Details — Elite Academy',
            'active' => 'subjects',
        ],
        'teachers' => [
            'view' => 'pages.teachers',
            'title' => 'Instructors — Elite Academy',
            'active' => 'teachers',
        ],
        'teacher-profile' => [
            'view' => 'pages.teacher-profile',
            'title' => 'Instructor Profile — Elite Academy',
            'active' => 'teachers',
        ],
        'courses' => [
            'view' => 'pages.courses',
            'title' => 'Courses — Elite Academy',
            'active' => 'courses',
        ],
        'course-details' => [
            'view' => 'pages.course-details',
            'title' => 'Course Details — Elite Academy',
            'active' => 'courses',
        ],
        'events' => [
            'view' => 'pages.events',
            'title' => 'Events — Elite Academy',
            'active' => 'events',
        ],
        'event-details' => [
            'view' => 'pages.event-details',
            'title' => 'Event Details — Elite Academy',
            'active' => 'events',
        ],
        'blog' => [
            'view' => 'pages.blog',
            'title' => 'Blog — Elite Academy',
            'active' => 'blog',
        ],
        'contact' => [
            'view' => 'pages.contact',
            'title' => 'Contact — Elite Academy',
            'active' => 'contact',
        ],
        'faq' => [
            'view' => 'pages.faq',
            'title' => 'FAQ — Elite Academy',
            'active' => 'faq',
        ],
        'login' => [
            'view' => 'pages.login',
            'title' => 'Log In — Elite Academy',
            'active' => 'login',
        ],
        'register' => [
            'view' => 'pages.register',
            'title' => 'Register — Elite Academy',
            'active' => 'register',
        ],
        'student-portal' => [
            'view' => 'pages.student-portal',
            'title' => 'Student Portal — Elite Academy',
            'active' => 'portal',
        ],
    ];

    public function show(\Illuminate\Http\Request $request): View
    {
        $page = $request->route()->defaults['page'] ?? 'home';

        if (! isset(self::PAGES[$page])) {
            abort(404);
        }

        $meta = self::PAGES[$page];

        return view($meta['view'], [
            'pageTitle' => $meta['title'],
            'pageDescription' => $meta['description'] ?? null,
            'activeNav' => $meta['active'] ?? null,
            'minimalLayout' => $meta['minimal'] ?? false,
        ]);
    }
}
