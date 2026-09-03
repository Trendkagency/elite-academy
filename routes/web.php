<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ValidationController;
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
        cookie()->queue(cookie()->forever('elite_locale', $locale));
    }

    return redirect()->back(fallback: route('home'));
})->name('lang.switch');

Route::middleware(SetLocale::class)->group(function () {

    // 1. Static & Public Pages
    Route::get('/', [PageController::class, 'show'])->defaults('page', 'home')->name('home');
    Route::get('/about', [\App\Http\Controllers\Cms\AboutController::class, 'show'])->name('about');
    // Public Catalog Pages (Open to All Users)
    Route::get('/subjects', [\App\Http\Controllers\Subject\SubjectController::class, 'index'])->name('subjects');
    Route::get('/subject-details/{slug?}', [\App\Http\Controllers\Subject\SubjectController::class, 'show'])->name('subject-details');
    Route::get('/teachers', [TeacherController::class, 'index'])->name('teachers');
    Route::get('/teacher-profile/{slug?}', [TeacherController::class, 'show'])->name('teacher-profile');

    Route::get('/courses', [CourseController::class, 'index'])->name('courses');
    Route::get('/course-details/{slug?}', [CourseController::class, 'show'])->name('course-details');

    Route::redirect('/instructors', '/teachers');
    Route::get('/instructor-profile/{slug}', function (string $slug) {
        return redirect()->route('teacher-profile', ['slug' => $slug]);
    });

    Route::get('/events', [PageController::class, 'show'])->defaults('page', 'events')->name('events');
    Route::get('/event-details/{slug?}', [PageController::class, 'show'])->defaults('page', 'event-details')->name('event-details');
    Route::get('/blog', [\App\Http\Controllers\Blog\BlogController::class, 'index'])->name('blog');
    Route::get('/blog-details/{slug?}', [\App\Http\Controllers\Blog\BlogController::class, 'show'])->name('blog-details');
    Route::get('/contact', [\App\Http\Controllers\Cms\ContactController::class, 'show'])->name('contact');
    Route::post('/ajax/contact/submit', [\App\Http\Controllers\Cms\ContactController::class, 'submitAjax'])->middleware('throttle:contact')->name('ajax.contact.submit');
    Route::get('/faq', [PageController::class, 'show'])->defaults('page', 'faq')->name('faq');

    // 2. Authentication Domain Routes (Protected with Brute Force & Rate Limit Protection)
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/ajax/login', [LoginController::class, 'login'])->middleware('throttle:login')->name('ajax.login');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/ajax/register', [RegisterController::class, 'register'])->middleware('throttle:register')->name('ajax.register');

    // Real-Time Auth Field Validation Endpoints
    Route::post('/ajax/validate/check-email-exists', [ValidationController::class, 'checkEmailExists'])->middleware('throttle:ajax_interactive')->name('ajax.validate.email-exists');
    Route::post('/ajax/validate/check-email-available', [ValidationController::class, 'checkEmailAvailable'])->middleware('throttle:ajax_interactive')->name('ajax.validate.email-available');
    Route::post('/ajax/validate/check-phone-available', [ValidationController::class, 'checkPhoneAvailable'])->middleware('throttle:ajax_interactive')->name('ajax.validate.phone-available');

    // 3. Media & Stream Routes
    Route::get('/ajax/secure-video/token/{course}', [\App\Http\Controllers\SecureVideoController::class, 'generateToken'])->name('ajax.secure-video.token');
    Route::get('/secure-video/stream/{course}', [\App\Http\Controllers\SecureVideoController::class, 'stream'])->name('secure-video.stream');

    // 4. Protected Student, Parent & Teacher Portals
    Route::middleware('auth')->group(function () {

        // Student Role Protected Domain Routes
        Route::middleware([\App\Http\Middleware\EnsureStudentRole::class])->group(function () {
            Route::get('/student-portal', [\App\Http\Controllers\Student\StudentPortalController::class, 'index'])->name('student-portal');
            Route::get('/student/profile', [\App\Http\Controllers\Student\StudentProfileController::class, 'show'])->name('student.profile');
            Route::post('/student/profile', [\App\Http\Controllers\Student\StudentProfileController::class, 'update'])->name('student.profile.update');
            Route::post('/student/profile/password', [\App\Http\Controllers\Student\StudentProfileController::class, 'updatePassword'])->name('student.profile.password');

            // Course Enrollment
            Route::post('/ajax/courses/{id}/enroll', [CourseController::class, 'enroll'])->middleware('throttle:strict_actions')->name('ajax.course.enroll');

            // Sessions & Stream Access
            Route::get('/ajax/sessions/{id}/access', [SessionController::class, 'show'])->name('ajax.session.access');
            Route::get('/ajax/live-sessions/{id}/access', [SessionController::class, 'liveSessionAccess'])->name('ajax.live-session.access');
            Route::post('/ajax/exceptions/submit', [SessionController::class, 'submitException'])->middleware('throttle:strict_actions')->name('ajax.exception.submit');

            // In-System Live Meeting & Attendance Routes
            Route::get('/student/sessions/{id}/meeting', [\App\Http\Controllers\Meeting\MeetingAccessController::class, 'show'])->name('student.meeting.show');
            Route::post('/ajax/sessions/{id}/meeting/join', [\App\Http\Controllers\Meeting\MeetingAccessController::class, 'join'])->middleware('throttle:strict_actions')->name('ajax.meeting.join');
            Route::post('/ajax/sessions/{id}/meeting/heartbeat', [\App\Http\Controllers\Meeting\MeetingAccessController::class, 'heartbeat'])->middleware('throttle:ajax_interactive')->name('ajax.meeting.heartbeat');
            Route::post('/ajax/sessions/{id}/meeting/leave', [\App\Http\Controllers\Meeting\MeetingAccessController::class, 'leave'])->name('ajax.meeting.leave');
            Route::post('/ajax/sessions/{id}/meeting/security-event', [\App\Http\Controllers\Meeting\MeetingAccessController::class, 'logSecurityEvent'])->middleware('throttle:ajax_interactive')->name('ajax.meeting.security-event');

            // Submissions & Interactive Assignment Solver Page
            Route::get('/student/assignments/{id}/take', [SubmissionController::class, 'take'])->name('student.assignment.take');
            Route::get('/ajax/assignments/{id}/details', [SubmissionController::class, 'show'])->name('ajax.assignment.details');
            Route::post('/ajax/assignments/save-answer', [SubmissionController::class, 'saveDraftAnswer'])->middleware('throttle:ajax_interactive')->name('ajax.assignment.save-answer');
            Route::post('/ajax/assignments/update-step', [SubmissionController::class, 'updateStepIndex'])->middleware('throttle:ajax_interactive')->name('ajax.assignment.update-step');
            Route::post('/ajax/assignments/submit', [SubmissionController::class, 'submit'])->middleware('throttle:strict_actions')->name('ajax.assignment.submit');
            Route::post('/ajax/assignments/{id}/security-audit', [SubmissionController::class, 'logSecurityAudit'])->middleware('throttle:ajax_interactive')->name('ajax.assignment.security-audit');
        });

        // Grading Submission (Authorized for Teachers / Admins via Policy Gate)
        Route::post('/ajax/submissions/{id}/grade', [SubmissionController::class, 'grade'])->middleware('throttle:strict_actions')->name('ajax.submission.grade');

        // Parent Role Protected Domain Routes
        Route::middleware([\App\Http\Middleware\EnsureParentRole::class])->group(function () {
            Route::get('/parent-portal', [\App\Http\Controllers\Parent\ParentPortalController::class, 'index'])->name('parent-portal');
            Route::get('/ajax/parent/student/{studentId}/progress', [\App\Http\Controllers\Parent\ParentPortalController::class, 'studentProgress'])->name('ajax.parent.student.progress');
            Route::post('/ajax/parent/link-child', [\App\Http\Controllers\Parent\ParentPortalController::class, 'linkChildByPhone'])->middleware('throttle:strict_actions')->name('ajax.parent.link-child');
        });

        // Teacher Role Protected Domain Routes (Strict Security & Authorization)
        Route::middleware([\App\Http\Middleware\EnsureTeacherRole::class])->group(function () {
            Route::get('/teacher-portal', [\App\Http\Controllers\Teacher\TeacherPortalController::class, 'index'])->name('teacher-portal');
            Route::post('/ajax/teacher/recurring-schedules/preview', [\App\Http\Controllers\Teacher\TeacherPortalController::class, 'previewRecurringSchedule'])->middleware('throttle:strict_actions')->name('ajax.teacher.recurring.preview');
            Route::post('/ajax/teacher/recurring-schedules/create', [\App\Http\Controllers\Teacher\TeacherPortalController::class, 'createRecurringSchedule'])->middleware('throttle:strict_actions')->name('ajax.teacher.recurring.create');
            Route::post('/ajax/teacher/sessions/create', [\App\Http\Controllers\Teacher\TeacherPortalController::class, 'createSession'])->middleware('throttle:strict_actions')->name('ajax.teacher.sessions.create');
            Route::post('/ajax/teacher/sessions/{id}/override', [\App\Http\Controllers\Teacher\TeacherPortalController::class, 'updateSessionOverride'])->middleware('throttle:strict_actions')->name('ajax.teacher.sessions.override');
            Route::post('/ajax/teacher/sessions/{id}/update', [\App\Http\Controllers\Teacher\TeacherPortalController::class, 'updateSession'])->middleware('throttle:strict_actions')->name('ajax.teacher.sessions.update');
            Route::post('/ajax/teacher/sessions/{id}/link', [\App\Http\Controllers\Teacher\TeacherPortalController::class, 'updateMeetingLink'])->middleware('throttle:strict_actions')->name('ajax.teacher.sessions.link');
            Route::post('/ajax/teacher/sessions/{id}/reschedule', [\App\Http\Controllers\Teacher\TeacherPortalController::class, 'rescheduleSession'])->middleware('throttle:strict_actions')->name('ajax.teacher.sessions.reschedule');
            Route::post('/ajax/teacher/sessions/{id}/cancel', [\App\Http\Controllers\Teacher\TeacherPortalController::class, 'cancelSession'])->middleware('throttle:strict_actions')->name('ajax.teacher.sessions.cancel');
            Route::get('/ajax/teacher/calendar-feed', [\App\Http\Controllers\Teacher\TeacherPortalController::class, 'getCalendarEvents'])->name('ajax.teacher.calendar.feed');
            Route::post('/ajax/teacher/assignments/create', [\App\Http\Controllers\Teacher\TeacherPortalController::class, 'createAssignment'])->middleware('throttle:strict_actions')->name('ajax.teacher.assignments.create');
            Route::get('/ajax/teacher/submissions/{submissionId}/review-details', [\App\Http\Controllers\Teacher\TeacherPortalController::class, 'getSubmissionReview'])->name('ajax.teacher.submissions.review-details');
            Route::post('/ajax/teacher/submissions/{id}/review', [\App\Http\Controllers\Teacher\TeacherPortalController::class, 'reviewSubmission'])->middleware('throttle:strict_actions')->name('ajax.teacher.submissions.review');
            Route::post('/ajax/teacher/sessions/{sessionId}/attendance', [\App\Http\Controllers\Teacher\TeacherPortalController::class, 'markAttendance'])->middleware('throttle:strict_actions')->name('ajax.teacher.attendance.mark');
            Route::get('/ajax/teacher/sessions/{sessionId}/attendance-roster', [\App\Http\Controllers\Teacher\TeacherPortalController::class, 'getSessionAttendanceRoster'])->name('ajax.teacher.sessions.attendance-roster');
            Route::get('/ajax/teacher/students/{studentUserId}/details', [\App\Http\Controllers\Teacher\TeacherPortalController::class, 'getStudentDetails'])->name('ajax.teacher.students.details');
            Route::get('/teacher/students/{studentUserId}', [\App\Http\Controllers\Teacher\TeacherPortalController::class, 'showStudentProfile'])->name('teacher.students.show');
            Route::post('/ajax/teacher/students/{studentUserId}/notes', [\App\Http\Controllers\Teacher\TeacherPortalController::class, 'storeStudentNote'])->middleware('throttle:strict_actions')->name('ajax.teacher.students.notes.create');
        });

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

self.addEventListener('notificationclick', function(event) {
  event.notification.close();
  const targetUrl = event.notification.data && event.notification.data.url ? event.notification.data.url : '/student-portal';
  event.waitUntil(
    clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function(clientList) {
      for (let i = 0; i < clientList.length; i++) {
        let client = clientList[i];
        if (client.url.includes(targetUrl) && 'focus' in client) {
          return client.focus();
        }
      }
      if (clients.openWindow) {
        return clients.openWindow(targetUrl);
      }
    })
  );
});
JS;

    return response($swContent, 200, [
        'Content-Type' => 'application/javascript; charset=utf-8',
        'Cache-Control' => 'no-cache, no-store, must-revalidate',
    ]);
});
});

// Dynamic XML Sitemap for AI Crawlers & Search Engine Indexing
Route::get('/sitemap.xml', function () {
    $baseUrl = url('/');
    $now = now()->toAtomString();

    $staticRoutes = [
        ['url' => $baseUrl . '/', 'priority' => '1.0', 'changefreq' => 'daily'],
        ['url' => $baseUrl . '/about', 'priority' => '0.9', 'changefreq' => 'weekly'],
        ['url' => $baseUrl . '/subjects', 'priority' => '0.9', 'changefreq' => 'daily'],
        ['url' => $baseUrl . '/courses', 'priority' => '0.9', 'changefreq' => 'daily'],
        ['url' => $baseUrl . '/teachers', 'priority' => '0.8', 'changefreq' => 'weekly'],
        ['url' => $baseUrl . '/blog', 'priority' => '0.8', 'changefreq' => 'daily'],
        ['url' => $baseUrl . '/faq', 'priority' => '0.9', 'changefreq' => 'weekly'],
        ['url' => $baseUrl . '/contact', 'priority' => '0.7', 'changefreq' => 'monthly'],
    ];

    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

    foreach ($staticRoutes as $item) {
        $xml .= '  <url>' . "\n";
        $xml .= '    <loc>' . $item['url'] . '</loc>' . "\n";
        $xml .= '    <lastmod>' . $now . '</lastmod>' . "\n";
        $xml .= '    <changefreq>' . $item['changefreq'] . '</changefreq>' . "\n";
        $xml .= '    <priority>' . $item['priority'] . '</priority>' . "\n";
        $xml .= '  </url>' . "\n";
    }

    $xml .= '</urlset>';

    return response($xml, 200, [
        'Content-Type' => 'application/xml; charset=utf-8',
        'Cache-Control' => 'public, max-age=3600',
    ]);
})->name('sitemap');

// Resilient Storage Fallback Route: Serves files directly if public/storage symlink is not followed
Route::get('storage/{path}', function (string $path) {
    // 1. Check if the file exists in storage/app/public/
    $publicPath = storage_path('app/public/' . $path);
    if (file_exists($publicPath) && ! is_dir($publicPath)) {
        return response()->file($publicPath, [
            'Cache-Control' => 'public, max-age=31536000',
        ]);
    }

    // 2. Check if the file was uploaded to storage/app/private/ (e.g. legacy uploads)
    $privatePath = storage_path('app/private/' . $path);
    if (file_exists($privatePath) && ! is_dir($privatePath)) {
        // Auto-migrate file to public storage folder for future direct web server serving
        @mkdir(dirname($publicPath), 0755, true);
        @copy($privatePath, $publicPath);

        return response()->file($privatePath, [
            'Cache-Control' => 'public, max-age=31536000',
        ]);
    }

    // 3. Check directly under storage/app/
    $appPath = storage_path('app/' . $path);
    if (file_exists($appPath) && ! is_dir($appPath)) {
        return response()->file($appPath, [
            'Cache-Control' => 'public, max-age=31536000',
        ]);
    }

    abort(404, __('Resource not found'));
})->where('path', '.*')->name('storage.fallback');

// System Fallback Route for Undefined Paths -> Animated 404 Page
Route::fallback(function () {
    abort(404, __('The page or resource you are looking for does not exist'));
});
