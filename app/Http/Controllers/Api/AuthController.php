<?php

namespace App\Http\Controllers\Api;

use App\DTOs\UserRegistrationDTO;
use App\Enums\AccountStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\AdminProfile;
use App\Models\ParentProfile;
use App\Models\StudentProfile;
use App\Models\TeacherProfile;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function __construct(
        protected UserRepositoryInterface $userRepository
    ) {}

    public function register(RegisterRequest $request)
    {
        $dto = UserRegistrationDTO::fromRequest($request->validated());

        $user = $this->userRepository->create([
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => bcrypt($dto->password),
            'phone' => $dto->phone,
            'status' => AccountStatus::APPROVED->value,
            'email_verified_at' => now(),
        ]);

        match ($dto->userType) {
            'teacher' => TeacherProfile::create(['user_id' => $user->id, 'slug' => Str::slug($user->name)]),
            'parent' => ParentProfile::create(['user_id' => $user->id]),
            'admin' => AdminProfile::create(['user_id' => $user->id]),
            default => StudentProfile::create(['user_id' => $user->id, 'grade_level_id' => $dto->gradeLevelId, 'school_name' => $dto->schoolName]),
        };

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Registration successful',
            'user' => new UserResource($user),
            'access_token' => $token,
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if (! auth()->attempt($credentials)) {
            return response()->json(['message' => 'Invalid email or password'], 401);
        }

        $user = auth()->user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => new UserResource($user),
            'access_token' => $token,
        ]);
    }

    public function me()
    {
        return new UserResource(auth()->user());
    }

    public function logout()
    {
        auth()->user()?->tokens()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }
}
