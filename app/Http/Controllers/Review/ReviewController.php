<?php

namespace App\Http\Controllers\Review;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(Request $request): View
    {
        $typeFilter = $request->query('type', 'all');
        $ratingFilter = $request->query('rating');
        $searchQuery = $request->query('search');

        $query = Testimonial::query();

        if ($typeFilter && in_array($typeFilter, ['student', 'parent', 'teacher'], true)) {
            $query->where('reviewer_type', $typeFilter);
        }

        if ($ratingFilter && is_numeric($ratingFilter)) {
            $query->where('rating', (int) $ratingFilter);
        }

        if ($searchQuery) {
            $query->where(function ($q) use ($searchQuery) {
                $q->where('name', 'like', "%{$searchQuery}%")
                  ->orWhere('content', 'like', "%{$searchQuery}%")
                  ->orWhere('course_name', 'like', "%{$searchQuery}%");
            });
        }

        $reviews = $query->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->paginate(12)
            ->withQueryString();

        // Statistical calculations
        $totalReviews = Testimonial::count();
        $avgRating = $totalReviews > 0 ? round(Testimonial::avg('rating'), 1) : 5.0;
        
        $ratingCounts = [
            5 => Testimonial::where('rating', 5)->count(),
            4 => Testimonial::where('rating', 4)->count(),
            3 => Testimonial::where('rating', 3)->count(),
            2 => Testimonial::where('rating', 2)->count(),
            1 => Testimonial::where('rating', 1)->count(),
        ];

        $roleCounts = [
            'student' => Testimonial::where('reviewer_type', 'student')->count(),
            'parent'  => Testimonial::where('reviewer_type', 'parent')->count(),
            'teacher' => Testimonial::where('reviewer_type', 'teacher')->count(),
        ];

        $verifiedCount = Testimonial::where('is_verified', true)->count();

        // Logged-in user context
        $user = Auth::user();
        $userRole = 'student';
        if ($user) {
            if (method_exists($user, 'hasRole')) {
                if ($user->hasRole('teacher') || $user->hasRole('instructor')) {
                    $userRole = 'teacher';
                } elseif ($user->hasRole('parent')) {
                    $userRole = 'parent';
                }
            } elseif (isset($user->role)) {
                $userRole = in_array($user->role, ['teacher', 'instructor', 'parent', 'student']) ? $user->role : 'student';
            }
        }

        return view('pages.reviews', [
            'pageTitle'      => __('Ratings & Student Reviews — Elite Academy'),
            'pageDescription'=> __('Explore honest ratings and reviews from students, parents, and teachers at Elite Academy.'),
            'activeNav'      => 'reviews',
            'reviews'        => $reviews,
            'totalReviews'   => $totalReviews,
            'avgRating'      => $avgRating,
            'ratingCounts'   => $ratingCounts,
            'roleCounts'     => $roleCounts,
            'verifiedCount'  => $verifiedCount,
            'currentType'    => $typeFilter,
            'currentRating'  => $ratingFilter,
            'searchQuery'    => $searchQuery,
            'user'           => $user,
            'userRole'       => $userRole,
        ]);
    }

    public function submitAjax(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'reviewer_type' => 'required|in:student,parent,teacher',
            'course_name'   => 'nullable|string|max:255',
            'rating'        => 'required|integer|min:1|max:5',
            'content'       => 'required|string|min:8|max:2000',
            'avatar'        => 'nullable|image|max:2048',
        ]);

        $avatarPath = null;
        $user = Auth::user();

        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('testimonials', 'public');
        } elseif ($user && !empty($user->avatar)) {
            $avatarPath = $user->avatar;
        }

        $testimonial = Testimonial::create([
            'user_id'       => $user?->id,
            'name'          => $validated['name'],
            'avatar'        => $avatarPath,
            'content'       => $validated['content'],
            'course_name'   => $validated['course_name'] ?: ($validated['reviewer_type'] === 'teacher' ? 'Faculty Instructor' : 'General Academic Track'),
            'rating'        => (int) $validated['rating'],
            'reviewer_type' => $validated['reviewer_type'],
            'is_verified'   => $user !== null, // Auto-verify authenticated platform members
            'is_featured'   => false,
            'sort_order'    => 0,
        ]);

        return response()->json([
            'success'   => true,
            'message'   => __('Thank you! Your review and rating have been submitted successfully.'),
            'review_id' => $testimonial->id,
        ], 201);
    }
}
