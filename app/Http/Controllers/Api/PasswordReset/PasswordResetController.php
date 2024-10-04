<?php

namespace App\Http\Controllers\Api\PasswordReset;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResetPassword\ResetPasswordRequest;
use App\Http\Requests\ResetPassword\SendResetLinkRequest;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    public function sendResetPasswordLinkToEmail(SendResetLinkRequest $request)
    {
        try {
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return response()->json(['error' => 'User not found.'], 404);
            }

            $status = Password::sendResetLink(
                $request->only('email')
            );

            return $status === Password::RESET_LINK_SENT
                ? response()->json(['message' => __($status)], 200)
                : response()->json(['error' => __($status)], 400);
        } catch (\Throwable $th) {
            Log::error("Failed to send reset link: " . $th->getMessage());
            return response()->json([
                'error' => 'Failed to send reset link. Please try later.'
            ], 500);
        }
    }

    public function reset(ResetPasswordRequest $request)
    {
        try {
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return response()->json(['error' => 'User not found.'], 404);
            }

            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function (User $user, string $password) {
                    $user->forceFill([
                        'password' => Hash::make($password),
                    ])->setRememberToken(Str::random(60));

                    $user->save();

                    event(new PasswordReset($user));
                }
            );

            return $status === Password::PASSWORD_RESET
                ? response()->json(['message' => __($status)], 200)
                : response()->json(['error' => __($status)], 400);
        } catch (\Throwable $th) {
            Log::error("Failed to reset password: " . $th->getMessage());
            return response()->json([
                'error' => 'Failed to reset password. Please try later.'
            ], 500);
        }
    }
}
