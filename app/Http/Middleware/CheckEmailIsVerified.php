<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CheckEmailIsVerified
{
    public function handle(Request $request, Closure $next): JsonResponse
    {
        try {
            $user = $request->user() ?: User::where('email', $request->email)->first();
            if ($user && !$user->hasVerifiedEmail()) {
                return response()->json(['error' => 'Email is not verified.'], 403);
            }
            return $next($request);
        } catch (\Throwable $th) {
            Log::error("Failed to check is email verified: " . $th->getMessage());
            return response()->json(['error' => 'Failed to check is email verified.'], 500);
        }
    }
}
