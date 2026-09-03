<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ValidationController extends Controller
{
    /**
     * Check if an email exists for login validation.
     */
    public function checkEmailExists(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'valid'   => false,
                'exists'  => false,
                'message' => __('app.auth.invalid_email_format'),
            ], 422);
        }

        $email = trim(strtolower($request->input('email')));
        $userExists = User::where('email', $email)->exists();

        return response()->json([
            'valid'   => true,
            'exists'  => $userExists,
            'message' => $userExists ? __('app.auth.email_exists') : __('app.auth.email_not_found'),
        ]);
    }

    /**
     * Check if an email is available (not taken) for registration.
     */
    public function checkEmailAvailable(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'valid'     => false,
                'available' => false,
                'message'   => __('app.auth.invalid_email_format'),
            ], 422);
        }

        $email = trim(strtolower($request->input('email')));
        $isTaken = User::where('email', $email)->exists();

        return response()->json([
            'valid'     => true,
            'available' => !$isTaken,
            'message'   => $isTaken ? __('app.auth.email_already_registered') : __('app.auth.email_available'),
        ]);
    }

    /**
     * Check if a phone number is available (not taken) for registration.
     */
    public function checkPhoneAvailable(Request $request): JsonResponse
    {
        $phone = trim($request->input('phone', ''));

        if (strlen($phone) < 6) {
            return response()->json([
                'valid'     => false,
                'available' => false,
                'message'   => __('app.auth.invalid_phone_format'),
            ], 422);
        }

        $isTaken = User::where('phone', $phone)->exists();

        return response()->json([
            'valid'     => true,
            'available' => !$isTaken,
            'message'   => $isTaken ? __('app.auth.phone_already_registered') : __('app.auth.phone_available'),
        ]);
    }
}
