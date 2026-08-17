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
            ->when($slug, fn ($query) => $query->where('slug', $slug))
            ->with(['category', 'courses.teacher.user', 'courses.gradeLevel'])
            ->first();

        return view('pages.subject-details', [
            'pageTitle' => $subject ? "{$subject->name} — Elite Academy" : 'Subject Details — Elite Academy',
            'activeNav' => 'subjects',
            'subject' => $subject,
        ]);
    }
}
