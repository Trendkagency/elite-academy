<?php

namespace App\Http\Controllers\Subject;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubjectController extends Controller
{
    public function index(Request $request): View
    {
        $subjects = Subject::where('is_active', true)
            ->with(['category', 'courses'])
            ->orderBy('sort_order')
            ->get();

        $categories = Category::with('subjects')->get();

        return view('pages.subjects', [
            'pageTitle' => 'Academic Subjects — Elite Academy',
            'activeNav' => 'subjects',
            'subjects' => $subjects,
            'categories' => $categories,
        ]);
    }

    public function show(?string $slug = null): View
    {
        $subject = Subject::where('is_active', true)
            ->when($slug, function ($query) use ($slug) {
                $query->where(function ($q) use ($slug) {
                    $q->where('slug', strtolower($slug))
                      ->orWhere('name', 'like', "%{$slug}%");
                });
            })
            ->with(['category', 'courses' => function ($q) {
                $q->where('is_active', true)->with(['teacher.user', 'gradeLevel', 'sessions']);
            }])
            ->first();

        if (! $subject && $slug) {
            $subject = Subject::where('is_active', true)
                ->with(['category', 'courses' => function ($q) {
                    $q->where('is_active', true)->with(['teacher.user', 'gradeLevel', 'sessions']);
                }])
                ->first();
        }

        $videoLessonsCount = $subject ? $subject->getVideoLessonsCount() : 0;
        $activeCoursesCount = $subject ? $subject->getActiveCoursesCount() : 0;
        $activeStudentsCount = $subject ? $subject->getActiveStudentsCount() : 0;
        $ratingAvg = $subject ? $subject->getRatingAvg() : 4.9;

        return view('pages.subject-details', [
            'pageTitle' => $subject ? "{$subject->getLocalizedName()} — Elite Academy" : 'Subject Details — Elite Academy',
            'activeNav' => 'subjects',
            'subject' => $subject,
            'videoLessonsCount' => $videoLessonsCount,
            'activeCoursesCount' => $activeCoursesCount,
            'activeStudentsCount' => $activeStudentsCount,
            'ratingAvg' => $ratingAvg,
        ]);
    }
}
