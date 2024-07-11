<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginUserRequest;
use App\Http\Requests\Auth\SignupUserRequest;
use App\Jobs\DeleteAllPictures;
use App\Models\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function signup(SignupUserRequest $request)
    {
        try {
            $user = User::create($request->validated());
            event(new Registered($user));
            return response(
                ['message' => 'Registration successful. Please check your email to verify your account.'],
                201
            );
        } catch (\Throwable $th) {
            Log::error("Failed to signup: " . $th->getMessage());
            return response([
                'error' => 'Failed to signup. Please try later.'
            ], 500);
        }
    }

    public function login(LoginUserRequest $request)
    {
        try {
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return response(['error' => 'User not found.'], 404);
            }
            if (!auth()->attempt($request->only(['email', 'password']))) {
                return response([
                    'error' => 'Email or password does not match the record.'
                ], 401);
            }
            $token = $user->createToken("auth_token")->plainTextToken;
            return response(['token' => $token], 200);
        } catch (\Throwable $th) {
            Log::error("Failed to login: " . $th->getMessage());
            return response([
                'error' => 'Failed to login. Please try later.'
            ], 500);
        }
    }

    public function getUser()
    {
        try {
            return response([
                'id' => auth()->user()->id
            ], 200);
        } catch (\Throwable $th) {
            Log::error("Failed to get user: " . $th->getMessage());
            return response([
                'error' => 'Failed to get user. Please try later.'
            ], 500);
        }
    }

    public function logout()
    {
        try {
            auth()->user()->tokens()->delete();
            return response([
                'message' => 'Logged out successfully.'
            ], 200);
        } catch (\Throwable $th) {
            Log::error("Failed to logout: " . $th->getMessage());
            return response([
                'error' => 'Failed to logout. Please try later.'
            ], 500);
        }
    }

    public function deleteUser()
    {
        try {
            $userId = auth()->user()->id;
            $user = User::findOrFail($userId);
            DeleteAllPictures::dispatch($userId);
            Event::where('user_id', $userId)->delete();
            $user->delete();
            return response(['message' => 'Your profile and all events deleted successfully.'], 200);
        } catch (\Throwable $th) {
            Log::error("Failed to delete user profile: " . $th->getMessage());
            return response([
                'error' => 'Failed to delete user profile. Please try later.'
            ], 500);
        }
    }
}
