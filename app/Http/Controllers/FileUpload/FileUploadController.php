<?php

namespace App\Http\Controllers\FileUpload;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\FileUpload;
use App\Models\LiveSession;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileUploadController extends Controller
{
    /**
     * Secure File Download Endpoint.
     * Enforces strict ACL authorization checks before streaming the file.
     */
    public function download(Request $request, int $id): StreamedResponse|BinaryFileResponse|JsonResponse
    {
        $file = FileUpload::findOrFail($id);
        $user = auth()->user();

        if (! $file->canAccess($user)) {
            abort(403, __('Unauthorized: You do not have permission to download this file.'));
        }

        if (! Storage::disk('public')->exists($file->file_path)) {
            abort(404, __('The requested file does not exist on storage.'));
        }

        // Increment download counter
        $file->increment('downloads_count');

        return Storage::disk('public')->download($file->file_path, $file->original_name);
    }

    /**
     * Secure Inline Preview Endpoint (for viewing PDFs or Images directly in browser tab).
     */
    public function preview(Request $request, int $id): StreamedResponse|BinaryFileResponse
    {
        $file = FileUpload::findOrFail($id);
        $user = auth()->user();

        if (! $file->canAccess($user)) {
            abort(403, __('Unauthorized: You do not have permission to view this file.'));
        }

        if (! Storage::disk('public')->exists($file->file_path)) {
            abort(404, __('The requested file does not exist on storage.'));
        }

        return Storage::disk('public')->response($file->file_path, $file->original_name, [
            'Content-Disposition' => 'inline; filename="' . addslashes($file->original_name) . '"',
        ]);
    }

    /**
     * Teacher / Faculty File Upload Handler.
     * Allows teachers to upload PDF/Image resources to their courses, sessions, or specific students.
     */
    public function teacherUpload(Request $request): JsonResponse
    {
        $user = auth()->user();
        if (! $user || (! $user->isTeacher() && ! $user->isAdmin())) {
            return response()->json(['success' => false, 'message' => __('Unauthorized')], 403);
        }

        $teacherProfile = $user->teacherProfile;
        if (! $teacherProfile && ! $user->isAdmin()) {
            return response()->json(['success' => false, 'message' => __('Teacher profile not found.')], 403);
        }

        $validated = $request->validate([
            'file' => [
                'required',
                'file',
                'mimes:pdf,jpeg,png,jpg,webp,gif',
                'max:25600', // 25 MB
            ],
            'title' => 'required|string|max:200',
            'description' => 'nullable|string|max:1000',
            'course_id' => 'nullable|integer|exists:courses,id',
            'student_user_id' => 'nullable|integer|exists:users,id',
            'live_session_id' => 'nullable|integer|exists:live_sessions,id',
        ]);

        $uploadedFile = $request->file('file');
        $extension = strtolower($uploadedFile->getClientOriginalExtension());
        $fileType = $extension === 'pdf' ? 'pdf' : 'image';

        // Store file with secure hashed name in dedicated educational_files directory
        $storedPath = $uploadedFile->store('educational_files', 'public');

        $record = FileUpload::create([
            'user_id' => $user->id,
            'title' => trim($validated['title']),
            'description' => $validated['description'] ?? null,
            'file_path' => $storedPath,
            'original_name' => $uploadedFile->getClientOriginalName(),
            'file_size' => $uploadedFile->getSize(),
            'mime_type' => $uploadedFile->getMimeType() ?: ($fileType === 'pdf' ? 'application/pdf' : 'image/' . $extension),
            'file_type' => $fileType,
            'student_user_id' => $validated['student_user_id'] ?? null,
            'teacher_profile_id' => $teacherProfile?->id,
            'course_id' => $validated['course_id'] ?? null,
            'live_session_id' => $validated['live_session_id'] ?? null,
            'downloads_count' => 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => __('File uploaded successfully.'),
            'file' => [
                'id' => $record->id,
                'title' => $record->title,
                'original_name' => $record->original_name,
                'file_type' => $record->file_type,
                'formatted_size' => $record->formatted_size,
                'created_at' => $record->created_at->format('Y-m-d H:i'),
                'download_url' => route('portal.files.download', $record->id),
                'preview_url' => route('portal.files.preview', $record->id),
            ],
        ]);
    }

    /**
     * Student File Upload Handler (e.g. Homework submission, solved worksheets, or study questions).
     */
    public function studentUpload(Request $request): JsonResponse
    {
        $user = auth()->user();
        if (! $user || (! $user->isStudent() && ! $user->isAdmin())) {
            return response()->json(['success' => false, 'message' => __('Unauthorized')], 403);
        }

        $validated = $request->validate([
            'file' => [
                'required',
                'file',
                'mimes:pdf,jpeg,png,jpg,webp,gif',
                'max:25600', // 25 MB
            ],
            'title' => 'required|string|max:200',
            'description' => 'nullable|string|max:1000',
            'course_id' => 'nullable|integer|exists:courses,id',
            'teacher_profile_id' => 'nullable|integer|exists:teacher_profiles,id',
            'live_session_id' => 'nullable|integer|exists:live_sessions,id',
        ]);

        $uploadedFile = $request->file('file');
        $extension = strtolower($uploadedFile->getClientOriginalExtension());
        $fileType = $extension === 'pdf' ? 'pdf' : 'image';

        // Automatically infer teacher_profile_id from course if not explicitly passed
        $teacherProfileId = $validated['teacher_profile_id'] ?? null;
        if (! $teacherProfileId && ! empty($validated['course_id'])) {
            $course = Course::find($validated['course_id']);
            $teacherProfileId = $course?->teacher_id;
        }

        // Store file with secure path
        $storedPath = $uploadedFile->store('educational_files/student_submissions', 'public');

        $record = FileUpload::create([
            'user_id' => $user->id,
            'title' => trim($validated['title']),
            'description' => $validated['description'] ?? null,
            'file_path' => $storedPath,
            'original_name' => $uploadedFile->getClientOriginalName(),
            'file_size' => $uploadedFile->getSize(),
            'mime_type' => $uploadedFile->getMimeType() ?: ($fileType === 'pdf' ? 'application/pdf' : 'image/' . $extension),
            'file_type' => $fileType,
            'student_user_id' => $user->id,
            'teacher_profile_id' => $teacherProfileId,
            'course_id' => $validated['course_id'] ?? null,
            'live_session_id' => $validated['live_session_id'] ?? null,
            'downloads_count' => 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => __('File uploaded successfully.'),
            'file' => [
                'id' => $record->id,
                'title' => $record->title,
                'original_name' => $record->original_name,
                'file_type' => $record->file_type,
                'formatted_size' => $record->formatted_size,
                'created_at' => $record->created_at->format('Y-m-d H:i'),
                'download_url' => route('portal.files.download', $record->id),
                'preview_url' => route('portal.files.preview', $record->id),
            ],
        ]);
    }

    /**
     * Delete an uploaded file (only uploader or admin).
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $file = FileUpload::findOrFail($id);
        $user = auth()->user();

        if (! $user) {
            return response()->json(['success' => false, 'message' => __('Unauthorized')], 401);
        }

        // Only the uploader or an admin can delete
        if (! $user->isAdmin() && (int) $file->user_id !== (int) $user->id) {
            return response()->json(['success' => false, 'message' => __('Unauthorized to delete this file.')], 403);
        }

        // Remove from physical disk
        if (Storage::disk('public')->exists($file->file_path)) {
            Storage::disk('public')->delete($file->file_path);
        }

        $file->delete();

        return response()->json([
            'success' => true,
            'message' => __('File deleted successfully.'),
        ]);
    }
}
