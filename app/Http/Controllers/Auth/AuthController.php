<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\SignupUserRequest;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function signup(SignupUserRequest $request)
    {
        try {
            $user = User::create($request->validated());
            $token = $user->createToken("auth_token")->plainTextToken;
            return response()->json([
                'token' => $token
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'Internal Server Error' => $th->getMessage()
            ], 500);
        }
    }

    public function login(LoginUserRequest $request)
    {
        try {
            if (!Auth::attempt($request->only(['email', 'password']))) {
                return response()->json([
                    'error' => 'Email or password does not match the record'
                ], 401);
            }
            $user = User::where('email', $request->email)->first();
            $token = $user->createToken("auth_token")->plainTextToken;
            return response()->json([
                'token' => $token
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'Internal Server Error' => $th->getMessage()
            ], 500);
        }
    }

    public function getUser()
    {
        try {
            $user = auth()->user();
            return response()->json([
                'id' => $user->id
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'Internal Server Error' => $th->getMessage()
            ], 500);
        }
    }

    public function logout()
    {
        try {
            auth()->user()->tokens()->delete();
            return response()->json([
                'message' => 'Logged out successfully'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'Internal Server Error' => $th->getMessage()
            ], 500);
        }
    }
}
