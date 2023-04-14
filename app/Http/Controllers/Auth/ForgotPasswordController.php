<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\PassResetRequest;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    public function sendResetLinkEmail(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);

        $response = Password::sendResetLink(
            $request->only('email')
        );

        return $response === Password::RESET_LINK_SENT
            ? response()->json(true)
            : response()->json(['error' => trans($response)], 500);
    }

    protected function reset(PassResetRequest $request): JsonResponse
    {
        $response = Password::reset(
            $request->only('email', 'password', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => $password
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return $response === Password::PASSWORD_RESET
            ? response()->json(true)
            : response()->json(['error' => trans($response)], 500);
    }
}
