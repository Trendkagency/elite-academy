<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SecureVideoController extends Controller
{
    /**
     * Generate a short-lived temporary signed URL for video playback.
     */
    public function generateToken(Request $request, Course $course)
    {
        $user = Auth::user();

        // 1. Check if video playback is authorized (free demo or enrolled student/admin)
        $isEnrolled = false;
        if ($user) {
            $isEnrolled = $course->enrollments()->where('student_user_id', $user->id)->exists();
        }

        if (! $course->has_free_demo && ! $isEnrolled && (! $user || ! $user->isAdmin())) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access. Enrollment required.',
            ], 403);
        }

        // 2. Generate temporary signed URL valid for 60 seconds
        $expiresAt = now()->addSeconds(60);
        $signedUrl = URL::temporarySignedRoute(
            'secure-video.stream',
            $expiresAt,
            [
                'course' => $course->id,
                'user' => $user?->id ?: 'guest',
                'ip' => $request->ip(),
            ]
        );

        return response()->json([
            'success' => true,
            'signed_url' => $signedUrl,
            'expires_in' => 60,
            'watermark' => [
                'name' => $user?->name ?: 'Guest Student',
                'phone' => $user?->phone ?: 'ID: Guest',
                'ip' => $request->ip(),
                'time' => now()->format('Y-m-d H:i:s'),
            ],
        ]);
    }

    /**
     * Stream protected video content with signed URL verification.
     */
    public function stream(Request $request, Course $course)
    {
        if (! $request->hasValidSignature()) {
            abort(403, 'Invalid or expired video playback token.');
        }

        $videoData = $course->getVideoEmbedData();

        if ($videoData['type'] === 'youtube' || $videoData['type'] === 'vimeo') {
            return redirect($videoData['embed_url']);
        }

        $rawUrl = $course->getDemoVideoUrl();

        // Local video file streaming
        if (str_contains($rawUrl, '/videos/')) {
            $filename = basename(parse_url($rawUrl, PHP_URL_PATH));
            $path = public_path('videos/' . $filename);

            if (! file_exists($path)) {
                $path = public_path('videos/physics_demo.mp4');
            }

            if (file_exists($path)) {
                return response()->file($path, [
                    'Content-Type' => 'video/mp4',
                    'Cache-Control' => 'no-cache, private',
                    'X-Content-Type-Options' => 'nosniff',
                ]);
            }
        }

        return redirect($rawUrl);
    }
}
