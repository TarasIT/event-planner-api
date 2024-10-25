<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\LoginUserRequest;
use App\Http\Requests\Auth\SignupUserRequest;
use App\Jobs\DeleteAllPictures;
use App\Models\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function signup(SignupUserRequest $request): JsonResponse
    {
        try {
            $user = User::create($request->validated());
            event(new Registered($user));
            return response()->json(
                ['message' => 'Registration successful. Please check your email to verify your account.'],
                201
            );
        } catch (\Throwable $th) {
            Log::error("Failed to signup: " . $th->getMessage());
            return response()->json([
                'error' => 'Failed to signup. Please try later.'
            ], 500);
        }
    }

    public function login(LoginUserRequest $request): JsonResponse
    {
        try {
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return response()->json(['error' => 'User not found.'], 404);
            }
            if (!auth()->attempt($request->only(['email', 'password']))) {
                return response()->json([
                    'error' => 'Email or password does not match the record.'
                ], 401);
            }
            $token = $user->createToken("auth_token")->plainTextToken;
            return response()->json(['token' => $token], 200);
        } catch (\Throwable $th) {
            Log::error("Failed to login: " . $th->getMessage());
            return response()->json([
                'error' => 'Failed to login. Please try later.'
            ], 500);
        }
    }

    public function getUser(): JsonResponse
    {
        try {
            $user = User::where('id', auth()->user()->id)->first();
            return response()->json([
                'id' => $user->id,
                'google_id' => $user->google_id,
                'name' => $user->name,
                'email' => $user->email,
                'is_password_existed' => $user->password ? true : false,
            ], 200);
        } catch (\Throwable $th) {
            Log::error("Failed to get user: " . $th->getMessage());
            return response()->json([
                'error' => 'Failed to get user. Please try later.'
            ], 500);
        }
    }

    public function logout(): JsonResponse
    {
        try {
            auth()->user()->tokens()->delete();
            return response()->json([
                'message' => 'Logged out successfully.'
            ], 200);
        } catch (\Throwable $th) {
            Log::error("Failed to logout: " . $th->getMessage());
            return response()->json([
                'error' => 'Failed to logout. Please try later.'
            ], 500);
        }
    }

    public function deleteUser(): JsonResponse
    {
        try {
            $userId = auth()->user()->id;
            $user = User::findOrFail($userId);
            DeleteAllPictures::dispatch($userId);
            Event::where('user_id', $userId)->delete();
            $user->delete();
            auth()->user()->tokens()->delete();
            return response()->json(['message' => 'Your profile deleted successfully.'], 200);
        } catch (\Throwable $th) {
            Log::error("Failed to delete profile: " . $th->getMessage());
            return response()->json([
                'error' => 'Failed to delete profile. Please try later.'
            ], 500);
        }
    }

    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        try {
            $user = Auth::user();
            if ($user->password && !Hash::check($request->current_password, $user->password)) {
                return response()->json(['error' => 'Current password is incorrect.'], 400);
            }
            $user->password = Hash::make($request->new_password);
            $user->save();
            return response()->json(['message' => 'Password changed successfully.'], 200);
        } catch (\Throwable $th) {
            Log::error("Failed to change password: " . $th->getMessage());
            return response()->json([
                'error' => 'Failed to change password. Please try later.'
            ], 500);
        }
    }
}
