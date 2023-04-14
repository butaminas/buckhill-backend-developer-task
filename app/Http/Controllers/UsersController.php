<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
{
    public function myDetails(): JsonResponse
    {
        return response()->json(Auth::user());
    }

    public function store(UserRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();

            return response()->json(User::create($validatedData));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(UserRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();

            return response()->json(User::find(Auth::id())?->update($validatedData));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy(): JsonResponse
    {
        return response()->json(Auth::user()?->delete());
    }
}
