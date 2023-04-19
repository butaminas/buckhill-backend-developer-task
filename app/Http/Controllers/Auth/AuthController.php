<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthRequest;
use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Handle an authentication attempt.
     */
    public function login(AuthRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        $admin = str_contains($request->path(), 'admin');

        // Validate the user's credentials
        $user = User::where('email', $credentials['email'])->where('is_admin', $admin)->first();
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json(['error' => 'User not found'], 401);
        }

        // Generate a JWT token for the user
        $payload = [
            'sub' => $user->id,
            'iat' => time(),
            'exp' => time() + (60 * config('jwt.expiration')),
            'jti' => uniqid('', true)
        ];
        $token = JWT::encode($payload, config('jwt.key'), config('jwt.algo'));

        return response()->json(['token' => $token]);
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $token = $request->bearerToken();
            if (!$token) {
                return response()->json(['error' => "Token not provided"], 401);
            }
            $payload = JWT::decode($token, new Key(config('jwt.key'), config('jwt.algo')));

            $this->invalidateToken($payload->jti);

            return response()->json(['message' => 'Successfully logged out']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function refreshToken(): JsonResponse
    {
        $token = JWT::encode([
            'user_id' => auth()->id(),
            'jti' => uniqid('', true),
        ], config('jwt.key'), config('jwt.algo'));

        return response()->json(['token' => $token]);
    }

    public function invalidateToken($jti): void
    {
        $key = 'jwt:invalidated:' . $jti;
        Cache::put($key, true, config('jwt.expiration'));
    }
}
