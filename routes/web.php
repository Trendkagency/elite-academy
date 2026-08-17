<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Course\CourseController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Session\SessionController;
use App\Http\Controllers\Submission\SubmissionController;
use App\Http\Controllers\Teacher\TeacherController;
use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

// Language Switcher
Route::get('/lang/{locale}', function (string $locale) {
    if (in_array($locale, ['en', 'ar'], true)) {
        session(['locale' => $locale]);
        App::setLocale($locale);
    }

    return redirect()->back();
})->name('lang.switch');

Route::middleware(SetLocale::class)->group(function () {

    // 1. Static & Public Pages
    Route::get('/', [PageController::class, 'show'])->defaults('page', 'home')->name('home');
    Route::get('/about', [\App\Http\Controllers\Cms\AboutController::class, 'show'])->name('about');
    Route::get('/subjects', [\App\Http\Controllers\Subject\SubjectController::class, 'index'])->name('subjects');
    Route::get('/subject-details/{slug?}', [\App\Http\Controllers\Subject\SubjectController::class, 'show'])->name('subject-details');
    Route::get('/teachers', [TeacherController::class, 'index'])->name('teachers');
    Route::get('/teacher-profile/{slug?}', [TeacherController::class, 'show'])->name('teacher-profile');
    Route::redirect('/instructors', '/teachers');
    Route::get('/instructor-profile/{slug}', function (string $slug) {
        return redirect()->route('teacher-profile', ['slug' => $slug]);
    });

    Route::get('/events', [PageController::class, 'show'])->defaults('page', 'events')->name('events');
    Route::get('/event-details/{slug?}', [PageController::class, 'show'])->defaults('page', 'event-details')->name('event-details');
    // 5. Blog Domain Routes
    Route::get('/blog', [\App\Http\Controllers\Blog\BlogController::class, 'index'])->name('blog');
    Route::get('/blog-details/{slug?}', [\App\Http\Controllers\Blog\BlogController::class, 'show'])->name('blog-details');
    // 6. Contact Domain Routes
    Route::get('/contact', [\App\Http\Controllers\Cms\ContactController::class, 'show'])->name('contact');
    Route::post('/ajax/contact/submit', [\App\Http\Controllers\Cms\ContactController::class, 'submitAjax'])->name('ajax.contact.submit');
    Route::get('/faq', [PageController::class, 'show'])->defaults('page', 'faq')->name('faq');
    Route::get('/student-portal', [PageController::class, 'show'])->defaults('page', 'student-portal')->name('student-portal');

    // 2. Authentication Domain Routes
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/ajax/login', [LoginController::class, 'login'])->name('ajax.login');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/ajax/register', [RegisterController::class, 'register'])->name('ajax.register');

    // 3. Course Domain Routes
    Route::get('/courses', [CourseController::class, 'index'])->name('courses');
    Route::get('/course-details/{slug?}', [CourseController::class, 'show'])->name('course-details');

    // 4. Protected Student & Teacher Actions
    Route::middleware('auth')->group(function () {
        // Course Enrollment
        Route::post('/ajax/courses/{id}/enroll', [CourseController::class, 'enroll'])->name('ajax.course.enroll');

        // Sessions & Stream Access
        Route::get('/ajax/sessions/{id}/access', [SessionController::class, 'show'])->name('ajax.session.access');
        Route::post('/ajax/exceptions/submit', [SessionController::class, 'submitException'])->name('ajax.exception.submit');

        // Submissions & Grading
        Route::post('/ajax/assignments/submit', [SubmissionController::class, 'submit'])->name('ajax.assignment.submit');
        Route::post('/ajax/submissions/{id}/grade', [SubmissionController::class, 'grade'])->name('ajax.submission.grade');

        // Parent Portal Domain Routes
        Route::get('/parent-portal', [\App\Http\Controllers\Parent\ParentPortalController::class, 'index'])->name('parent-portal');
        Route::get('/ajax/parent/student/{studentId}/progress', [\App\Http\Controllers\Parent\ParentPortalController::class, 'studentProgress'])->name('ajax.parent.student.progress');
    });
});
