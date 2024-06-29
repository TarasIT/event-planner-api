<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckEmailIsVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $user = $request->user() ?: User::where('email', $request->email)->first();
            if ($user && !$user->hasVerifiedEmail()) {
                return response(['error' => 'Email is not verified.'], 403);
            }
            return $next($request);
        } catch (\Throwable $th) {
            Log::error("Failed to check is email verified: " . $th->getMessage());
            return response(['error' => 'Failed to check is email verified.'], 500);
        }
    }
}
