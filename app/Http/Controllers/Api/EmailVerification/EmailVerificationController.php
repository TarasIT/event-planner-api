<?php

namespace App\Http\Controllers\Api\EmailVerification;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginUserRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EmailVerificationController extends Controller
{
    function verify($id, Request $request)
    {
        try {
            if (!$request->hasValidSignature()) {
                return redirect()
                    ->to('http://localhost:3000/email-verification?message=' . urlencode('Invalid URL provided.'));
            }
            $user = User::findOrFail($id);
            if (!$user->hasVerifiedEmail()) {
                $user->markEmailAsVerified();
            }

            return redirect()
                ->to('http://localhost:3000/email-verification?message=' . urlencode('Email verified successfully.'));
        } catch (ModelNotFoundException $e) {
            return redirect()
                ->to('http://localhost:3000/email-verification?message=' . urlencode("User with id='$id' is not found."));
        } catch (\Throwable $th) {
            Log::error("Failed to verify email: " . $th->getMessage());
            return redirect()
                ->to('http://localhost:3000/email-verification?message=' . urlencode('Failed to verify email. Please try later.'));
        }
    }

    function resendEmail(LoginUserRequest $request)
    {
        try {
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return response(['error' => 'User not found.'], 404);
            }
            if (!auth()->attempt($request->only('email', 'password'))) {
                return response([
                    'error' => 'Email or password does not match the record.'
                ], 401);
            }
            if (!$user->hasVerifiedEmail()) {
                $user->sendEmailVerificationNotification();
                return response(['message' => 'Verification link resent! Check your email.'], 200);
            }
            return response(['error' => 'Email is already verified.'], 400);
        } catch (\Throwable $th) {
            Log::error("Failed to resend verification email: " . $th->getMessage());
            return response([
                'error' => 'Failed to resend verification email. Please try later.'
            ], 500);
        }
    }
}
