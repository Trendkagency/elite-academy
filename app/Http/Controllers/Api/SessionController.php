<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SessionResource;
use App\Repositories\Contracts\SessionRepositoryInterface;
use Illuminate\Support\Facades\Gate;

class SessionController extends Controller
{
    public function __construct(
        protected SessionRepositoryInterface $sessionRepository
    ) {}

    public function show(int $id)
    {
        $session = $this->sessionRepository->find($id);

        if (! $session) {
            return response()->json(['message' => 'Session not found'], 404);
        }

        if (Gate::denies('view', $session)) {
            return response()->json(['message' => 'Session is locked. Complete previous assignments to unlock.'], 403);
        }

        return new SessionResource($session);
    }
}
