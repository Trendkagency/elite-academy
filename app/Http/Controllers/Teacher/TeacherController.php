<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\TeacherProfile;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TeacherController extends Controller
{
    public function index(Request $request): View|\Illuminate\Http\RedirectResponse
    {
        if (auth()->check() && auth()->user()->isTeacher()) {
            return redirect()->route('teacher-portal');
        }

        $subjectFilter = $request->query('subject');
        $searchQuery = $request->query('q');

        $query = TeacherProfile::with(['user', 'subjects'])
            ->where('is_public', true);

        if ($subjectFilter) {
            $query->whereHas('subjects', function ($q) use ($subjectFilter) {
                $q->where('slug', $subjectFilter)
                  ->orWhere('name', 'LIKE', "%{$subjectFilter}%");
            });
        }

        if ($searchQuery) {
            $query->where(function ($q) use ($searchQuery) {
                $q->where('title', 'LIKE', "%{$searchQuery}%")
                  ->orWhere('specialization', 'LIKE', "%{$searchQuery}%")
                  ->orWhereHas('user', function ($uq) use ($searchQuery) {
                      $uq->where('name', 'LIKE', "%{$searchQuery}%");
                  });
            });
        }

        $teachers = $query->orderBy('is_featured', 'desc')
            ->orderBy('rating_avg', 'desc')
            ->get();

        $subjects = Subject::orderBy('sort_order')->get();

        return view('pages.teachers', [
            'pageTitle' => 'Teachers & Faculty Directory — Elite Academy',
            'activeNav' => 'teachers',
            'teachers' => $teachers,
            'subjects' => $subjects,
            'selectedSubject' => $subjectFilter,
            'searchQuery' => $searchQuery,
        ]);
    }

    public function show(?string $slug = null): View
    {
        $teacher = TeacherProfile::with(['user', 'courses.subject', 'subjects', 'gradeLevels'])
            ->when($slug, fn ($query) => $query->where('slug', $slug)->orWhere('id', $slug))
            ->first();

        if (! $teacher) {
            $teacher = TeacherProfile::with(['user', 'courses.subject', 'subjects', 'gradeLevels'])->first();
        }

        return view('pages.teacher-profile', [
            'pageTitle' => $teacher ? "{$teacher->user?->name} — Teacher Profile" : 'Teacher Profile — Elite Academy',
            'activeNav' => 'teachers',
            'teacher' => $teacher,
        ]);
    }
}
