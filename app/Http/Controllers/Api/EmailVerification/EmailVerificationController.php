<?php

namespace App\Http\Controllers\Api\EmailVerification;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginUserRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Log;

class EmailVerificationController extends Controller
{
    function verify($id, Request $request): RedirectResponse | Redirector
    {
        try {
            if (!$request->hasValidSignature()) {
                return redirect(
                    config('app.frontend_url') . "/email-verification" . '?message=' . urlencode('Invalid URL provided.')
                );
            }
            $user = User::findOrFail($id);
            if (!$user->hasVerifiedEmail()) {
                $user->markEmailAsVerified();
            }

            return redirect(
                config('app.frontend_url') . "/email-verification" . '?message=' . urlencode('Email verified successfully.')
            );
        } catch (ModelNotFoundException $e) {
            return redirect(
                config('app.frontend_url') . "/email-verification" . '?message=' . urlencode("User not found.")
            );
        } catch (\Throwable $th) {
            Log::error("Failed to verify email: " . $th->getMessage());
            return redirect(
                config('app.frontend_url') . "/email-verification" . '?message=' . urlencode('Failed to verify email. Please try later.')
            );
        }
    }

    function resendEmail(LoginUserRequest $request): JsonResponse
    {
        try {
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return response()->json(['error' => 'User not found.'], 404);
            }
            if (!auth()->attempt($request->only('email', 'password'))) {
                return response()->json([
                    'error' => 'Email or password does not match the record.'
                ], 401);
            }
            if (!$user->hasVerifiedEmail()) {
                $user->sendEmailVerificationNotification();
                return response()->json(['message' => 'Verification link resent! Check your email.'], 200);
            }
            return response()->json(['error' => 'Email is already verified.'], 400);
        } catch (\Throwable $th) {
            Log::error("Failed to resend verification email: " . $th->getMessage());
            return response()->json([
                'error' => 'Failed to resend verification email. Please try later.'
            ], 500);
        }
    }
}
