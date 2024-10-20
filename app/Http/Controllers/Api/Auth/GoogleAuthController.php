<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirectToGoogle(): JsonResponse | RedirectResponse | Redirector
    {
        try {
            return Socialite::driver('google')->stateless()->redirect();
        } catch (\Throwable $th) {
            Log::error("Failed google redirect: " . $th->getMessage());
            return response()->json([
                'error' => 'Failed google redirect. Please try later.'
            ], 500);
        }
    }

    public function handleGoogleCallback(): JsonResponse | RedirectResponse | Redirector
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
            $user = User::where('email', $googleUser->email)->first();

            if ($user && !$user->google_id) {
                $user->update([
                    'google_id' => $googleUser->id,
                ]);
            }
            if (!$user) {
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'password' => null,
                    'email_verified_at' => now(),
                ]);
            }
            $token = $user->createToken("auth_token")->plainTextToken;
            $redirectUrl = config('app.frontend_url') . "?token=" . urlencode($token);
            return redirect($redirectUrl);
        } catch (\Throwable $th) {
            Log::error("Failed google callback: " . $th->getMessage());
            return response()->json([
                'error' => 'Failed google callback. Please try later.'
            ], 500);
        }
    }
}
