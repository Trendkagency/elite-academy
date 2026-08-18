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
    Route::get('/blog', [\App\Http\Controllers\Blog\BlogController::class, 'index'])->name('blog');
    Route::get('/blog-details/{slug?}', [\App\Http\Controllers\Blog\BlogController::class, 'show'])->name('blog-details');
    Route::get('/contact', [\App\Http\Controllers\Cms\ContactController::class, 'show'])->name('contact');
    Route::post('/ajax/contact/submit', [\App\Http\Controllers\Cms\ContactController::class, 'submitAjax'])->name('ajax.contact.submit');
    Route::get('/faq', [PageController::class, 'show'])->defaults('page', 'faq')->name('faq');

    // 2. Authentication Domain Routes
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/ajax/login', [LoginController::class, 'login'])->name('ajax.login');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/ajax/register', [RegisterController::class, 'register'])->name('ajax.register');

    // 3. Course Catalog Domain Routes
    Route::get('/courses', [CourseController::class, 'index'])->name('courses');
    Route::get('/course-details/{slug?}', [CourseController::class, 'show'])->name('course-details');

    // 4. Protected Student, Parent & Teacher Portals
    Route::middleware('auth')->group(function () {
        // Student Portal Dashboard & Profile Management
        Route::get('/student-portal', [\App\Http\Controllers\Student\StudentPortalController::class, 'index'])->name('student-portal');
        Route::get('/student/profile', [\App\Http\Controllers\Student\StudentProfileController::class, 'show'])->name('student.profile');
        Route::post('/student/profile', [\App\Http\Controllers\Student\StudentProfileController::class, 'update'])->name('student.profile.update');
        Route::post('/student/profile/password', [\App\Http\Controllers\Student\StudentProfileController::class, 'updatePassword'])->name('student.profile.password');

        // Course Enrollment
        Route::post('/ajax/courses/{id}/enroll', [CourseController::class, 'enroll'])->name('ajax.course.enroll');

        // Sessions & Stream Access
        Route::get('/ajax/sessions/{id}/access', [SessionController::class, 'show'])->name('ajax.session.access');
        Route::get('/ajax/live-sessions/{id}/access', [SessionController::class, 'liveSessionAccess'])->name('ajax.live-session.access');
        Route::post('/ajax/exceptions/submit', [SessionController::class, 'submitException'])->name('ajax.exception.submit');

        // Submissions & Interactive Assignment Solver Page
        Route::get('/student/assignments/{id}/take', [SubmissionController::class, 'take'])->name('student.assignment.take');
        Route::get('/ajax/assignments/{id}/details', [SubmissionController::class, 'show'])->name('ajax.assignment.details');
        Route::post('/ajax/assignments/save-answer', [SubmissionController::class, 'saveDraftAnswer'])->name('ajax.assignment.save-answer');
        Route::post('/ajax/assignments/update-step', [SubmissionController::class, 'updateStepIndex'])->name('ajax.assignment.update-step');
        Route::post('/ajax/assignments/submit', [SubmissionController::class, 'submit'])->name('ajax.assignment.submit');
        Route::post('/ajax/assignments/{id}/security-audit', [SubmissionController::class, 'logSecurityAudit'])->name('ajax.assignment.security-audit');
        Route::post('/ajax/submissions/{id}/grade', [SubmissionController::class, 'grade'])->name('ajax.submission.grade');

        // Parent Portal Domain Routes
        Route::get('/parent-portal', [\App\Http\Controllers\Parent\ParentPortalController::class, 'index'])->name('parent-portal');
        Route::get('/ajax/parent/student/{studentId}/progress', [\App\Http\Controllers\Parent\ParentPortalController::class, 'studentProgress'])->name('ajax.parent.student.progress');

        // FCM Notifications, Deadline Reminders & 30s Test Push
        Route::get('/ajax/notifications', [\App\Http\Controllers\Notification\NotificationController::class, 'feed'])->name('ajax.notifications.feed');
        Route::post('/ajax/notifications/fcm-token', [\App\Http\Controllers\Notification\NotificationController::class, 'registerToken'])->name('ajax.notifications.token');
        Route::post('/api/notifications/fcm-token', [\App\Http\Controllers\Notification\NotificationController::class, 'registerToken']);
        Route::post('/api/device-tokens', [\App\Http\Controllers\Notification\NotificationController::class, 'registerToken']);
        Route::post('/ajax/notifications/test-push', [\App\Http\Controllers\Notification\NotificationController::class, 'triggerTestPush'])->name('ajax.notifications.test-push');
        Route::post('/ajax/notifications/send-custom', [\App\Http\Controllers\Notification\NotificationController::class, 'sendCustomNotification'])->name('ajax.notifications.send-custom');
    });

// Firebase Web Messaging Service Worker Route
Route::get('/firebase-messaging-sw.js', function () {
    $webConfig = config('fcm.web_config');
    $projectId = config('fcm.v1.project_id', 'elite-academy-67a15');

    $authDomain = $webConfig['auth_domain'] ?? 'elite-academy-67a15.firebaseapp.com';
    $storageBucket = $webConfig['storage_bucket'] ?? 'elite-academy-67a15.firebasestorage.app';
    $senderId = $webConfig['messaging_sender_id'] ?? '116144754233756448435';

    $configPairs = [
        'messagingSenderId: "' . $senderId . '"',
        'projectId: "' . $projectId . '"',
        'authDomain: "' . $authDomain . '"',
        'storageBucket: "' . $storageBucket . '"',
    ];

    if (! empty($webConfig['api_key'])) {
        $configPairs[] = 'apiKey: "' . addslashes($webConfig['api_key']) . '"';
    }
    if (! empty($webConfig['app_id'])) {
        $configPairs[] = 'appId: "' . addslashes($webConfig['app_id']) . '"';
    }

    $configObject = "{\n  " . implode(",\n  ", $configPairs) . "\n}";

    $swContent = <<<JS
// Firebase Messaging Service Worker for Elite Academy LMS
importScripts('https://www.gstatic.com/firebasejs/9.23.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/9.23.0/firebase-messaging-compat.js');

firebase.initializeApp({$configObject});

const messaging = firebase.messaging();

messaging.onBackgroundMessage(function(payload) {
  console.log('[firebase-messaging-sw.js] Received background message ', payload);
  const notificationTitle = payload.notification ? payload.notification.title : (payload.data ? payload.data.title : 'Elite Academy Notification');
  const notificationOptions = {
    body: payload.notification ? payload.notification.body : (payload.data ? payload.data.body : ''),
    icon: payload.notification ? payload.notification.image : '/images/logo.png',
    data: payload.data || {}
  };

  self.registration.showNotification(notificationTitle, notificationOptions);
});
JS;

    return response($swContent, 200, [
        'Content-Type' => 'application/javascript; charset=utf-8',
        'Cache-Control' => 'no-cache, no-store, must-revalidate',
    ]);
});
});
