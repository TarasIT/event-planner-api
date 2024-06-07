<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckEmailIsVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user() ?: User::where('email', $request->email)->first();
        if ($user && !$user->hasVerifiedEmail()) {
            return response(['error' => 'Email not verified.'], 403);
        }
        return $next($request);
    }
}
