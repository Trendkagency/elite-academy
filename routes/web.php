<?php

use App\Http\Controllers\PageController;
use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

Route::get('/lang/{locale}', function (string $locale) {
    if (in_array($locale, ['en', 'ar'], true)) {
        session(['locale' => $locale]);
        App::setLocale($locale);
    }

    return redirect()->back();
})->name('lang.switch');

Route::middleware(SetLocale::class)->group(function () {
    Route::get('/', [PageController::class, 'show'])->defaults('page', 'home')->name('home');
    Route::get('/about', [PageController::class, 'show'])->defaults('page', 'about')->name('about');
    Route::get('/subjects', [PageController::class, 'show'])->defaults('page', 'subjects')->name('subjects');
    Route::get('/subject-details/{slug?}', [PageController::class, 'show'])->defaults('page', 'subject-details')->name('subject-details');
    Route::get('/teachers', [PageController::class, 'show'])->defaults('page', 'teachers')->name('teachers');
    Route::get('/teacher-profile/{slug?}', [PageController::class, 'show'])->defaults('page', 'teacher-profile')->name('teacher-profile');
    Route::get('/courses', [PageController::class, 'show'])->defaults('page', 'courses')->name('courses');
    Route::get('/course-details/{slug?}', [PageController::class, 'show'])->defaults('page', 'course-details')->name('course-details');
    Route::get('/events', [PageController::class, 'show'])->defaults('page', 'events')->name('events');
    Route::get('/event-details/{slug?}', [PageController::class, 'show'])->defaults('page', 'event-details')->name('event-details');
    Route::get('/blog', [PageController::class, 'show'])->defaults('page', 'blog')->name('blog');
    Route::get('/contact', [PageController::class, 'show'])->defaults('page', 'contact')->name('contact');
    Route::get('/faq', [PageController::class, 'show'])->defaults('page', 'faq')->name('faq');
    Route::get('/login', [PageController::class, 'show'])->defaults('page', 'login')->name('login');
    Route::get('/register', [PageController::class, 'show'])->defaults('page', 'register')->name('register');
    Route::get('/student-portal', [PageController::class, 'show'])->defaults('page', 'student-portal')->name('student-portal');
});
