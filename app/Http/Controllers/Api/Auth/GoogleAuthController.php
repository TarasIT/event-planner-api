<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class GoogleAuthController extends Controller
{
    public function redirectToGoogle()
    {
        try {
            return Socialite::driver('google')->stateless()->redirect();
        } catch (\Throwable $th) {
            Log::error("Failed google redirect: " . $th->getMessage());
            return response([
                'error' => 'Failed google redirect. Please try later.'
            ], 500);
        }
    }

    public function handleGoogleCallback()
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
                    'password' => bcrypt(Str::random(16)),
                    'email_verified_at' => now(),
                ]);
            }
            $token = $user->createToken("auth_token")->plainTextToken;

            $frontendAppUrl = 'http://localhost:3000';
            $redirectUrl = "{$frontendAppUrl}?token=" . urlencode($token);

            return redirect($redirectUrl);
        } catch (\Throwable $th) {
            Log::error("Failed google callback: " . $th->getMessage());
            return response([
                'error' => 'Failed google callback. Please try later.'
            ], 500);
        }
    }
}
