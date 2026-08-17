<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\SessionController;
use App\Http\Controllers\Api\SubmissionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — Elite Academy
|--------------------------------------------------------------------------
*/

// Public Authentication Endpoints
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Public Course Browsing
Route::get('/courses', [CourseController::class, 'index']);
Route::get('/courses/{id}', [CourseController::class, 'show']);

// Protected API Routes
Route::middleware('auth')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Course Enrollment
    Route::post('/courses/{id}/enroll', [CourseController::class, 'enroll']);

    // Session Content Streaming (Unlocked Check)
    Route::get('/sessions/{id}', [SessionController::class, 'show']);

    // Homework & Assignment Submissions
    Route::post('/submissions', [SubmissionController::class, 'submit']);
    Route::post('/submissions/{id}/grade', [SubmissionController::class, 'grade']);
});
