<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthRequest;
use App\Models\User;
use Firebase\JWT\JWT;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /**
     * Handle an authentication attempt.
     */
    public function login(AuthRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        // Validate the user's credentials
        $user = User::where('email', $credentials['email'])->first();
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Generate a JWT token for the user
        $payload = [
            'sub' => $user->id,
            'iat' => time(),
            'exp' => time() + (60 * 60), // Token expires in 1 hour
        ];
        $token = JWT::encode($payload, config('jwt.key'), config('jwt.algo'));

        return response()->json(['token' => $token]);
    }
}
