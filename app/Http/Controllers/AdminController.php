<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class AdminController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            return response()->json(User::where('is_admin', false)
                ->orderBy('created_at', 'desc')->paginate());
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function store(UserRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $validatedData['is_admin'] = true;

            return response()->json(User::create($validatedData));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(UserRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();

            $user = User::findOrFail($request->uuid);

            if ($user->is_admin) {
                return response()->json(['error' => 'You can not update admin user'], 500);
            }

            return response()->json($user->update($validatedData));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy(User $uuid): JsonResponse
    {
        try {
            if ($uuid->is_admin) {
                return response()->json(['error' => 'You can not delete admin user'], 500);
            }
            return response()->json($uuid->delete());
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
