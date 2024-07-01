<?php

namespace App\Http\Controllers\Swagger\Auth;

use App\Http\Controllers\Controller;

/**
 *  @OA\Post(
 *      path="/users/auth/signup",
 *      summary="Signs up a new user",
 *      tags={"Authentication"},
 *      description="Signs up a new user",
 *      @OA\RequestBody(
 *          required=true,
 *          @OA\MediaType(
 *              mediaType="application/json",
 *              @OA\Schema(
 *                  type="object",
 *                  @OA\Property(
 *                      property="name",
 *                      type="string"
 *                  ),
 *                  @OA\Property(
 *                      property="email",
 *                      type="string"
 *                  ),
 *                  @OA\Property(
 *                      property="password",
 *                      type="string"
 *                  )
 *              ),
 *              example={"name": "John Doe", "email": "john.doe@example.com", "password": "johnPassword"}
 *          )
 *      ),
 *      @OA\Response(
 *          response=201,
 *          description="Registration successful",
 *          @OA\JsonContent(
 *              type="object",
 *              @OA\Property(
 *                  property="message",
 *                  type="string",
 *                  example="Registration successful. Please check your email to verify your account."
 *              )
 *          )
 *      ),
 *      @OA\Response(
 *          response=422,
 *          description="Request validation error",
 *          @OA\JsonContent(
 *              type="object",
 *              @OA\Property(
 *                  property="message",
 *                  type="string",
 *                  example="The email has already been taken."
 *              )
 *          )
 *      ),
 *      @OA\Response(
 *          response=500,
 *          description="Failed to signup",
 *          @OA\JsonContent(
 *              type="object",
 *              @OA\Property(
 *                  property="error",
 *                  type="string",
 *                  example="Failed to signup. Please try later."
 *              )
 *          )
 *      )
 *  ),
 *  @OA\Post(
 *      path="/users/auth/login",
 *      summary="Logs in a user",
 *      tags={"Authentication"},
 *      description="Logs in a user",
 *      @OA\RequestBody(
 *          required=true,
 *          @OA\MediaType(
 *              mediaType="application/json",
 *              @OA\Schema(
 *                  type="object",
 *                  @OA\Property(
 *                      property="email",
 *                      type="string"
 *                  ),
 *                  @OA\Property(
 *                      property="password",
 *                      type="string"
 *                  )
 *              ),
 *              example={"email": "john.doe@example.com", "password": "johnPassword"}
 *          )
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Login successful",
 *          @OA\JsonContent(
 *              type="object",
 *              @OA\Property(
 *                  property="token",
 *                  type="string",
 *                  example="valid token"
 *              )
 *          )
 *      ),
 *      @OA\Response(
 *          response=401,
 *          description="Wrong credentials",
 *          @OA\JsonContent(
 *              type="object",
 *              @OA\Property(
 *                  property="error",
 *                  type="string",
 *                  example="Email or password does not match the record.",
 *              )
 *          )
 *      ),
 *      @OA\Response(
 *          response=403,
 *          description="Email is not verified",
 *          @OA\JsonContent(
 *              type="object",
 *              @OA\Property(
 *                  property="error",
 *                  type="string",
 *                  example="Email is not verified.",
 *              )
 *          )
 *      ),
 *      @OA\Response(
 *          response=404,
 *          description="User not found",
 *          @OA\JsonContent(
 *              type="object",
 *              @OA\Property(
 *                  property="error",
 *                  type="string",
 *                  example="User not found.",
 *              )
 *          )
 *      ),
 *      @OA\Response(
 *          response=422,
 *          description="Request validation error",
 *          @OA\JsonContent(
 *              type="object",
 *              @OA\Property(
 *                  property="message",
 *                  type="string",
 *                  example="The email field is required.",
 *              )
 *          )
 *      ),
 *      @OA\Response(
 *          response=500,
 *          description="Failed to login",
 *          @OA\JsonContent(
 *              type="object",
 *              @OA\Property(
 *                  property="error",
 *                  type="string",
 *                  example="Failed to login. Please try later."
 *              )
 *          )
 *      )
 *  ),
 *  @OA\Post(
 *      path="/users/auth/logout",
 *      summary="Logs out a user",
 *      tags={"Authentication"},
 *      security={{ "bearerAuth": {} }},
 *      parameters={
 *          @OA\Parameter(
 *              name="Authorization",
 *              in="header",
 *              required=true,
 *              description="Bearer token",
 *              @OA\Schema(
 *                  type="string"
 *              )
 *          )
 *      },
 *      description="Logs out a user",
 *      @OA\Response(
 *          response=200,
 *          description="Logout a user successful",
 *          @OA\JsonContent(
 *              type="object",
 *              @OA\Property(
 *                  property="message",
 *                  type="string",
 *                  example="Logged out successfully."
 *              )
 *          )
 *      ),
 *      @OA\Response(
 *          response=401,
 *          description="Unauthenticated",
 *          @OA\JsonContent(
 *              type="object",
 *              @OA\Property(
 *                  property="message",
 *                  type="string",
 *                  example="Unauthenticated."
 *              )
 *          )
 *      ),
 *      @OA\Response(
 *          response=500,
 *          description="Failed to logout",
 *          @OA\JsonContent(
 *              type="object",
 *              @OA\Property(
 *                  property="error",
 *                  type="string",
 *                  example="Failed to logout. Please try later."
 *              )
 *          )
 *      )
 *  ),
 *  @OA\Get(
 *      path="/users/current",
 *      summary="Gets a current user",
 *      tags={"Authentication"},
 *      security={{ "bearerAuth": {} }},
 *      parameters={
 *          @OA\Parameter(
 *              name="Authorization",
 *              in="header",
 *              required=true,
 *              description="Bearer token",
 *              @OA\Schema(
 *                  type="string"
 *              )
 *          )
 *      },
 *      description="Gets a current user",
 *      @OA\Response(
 *          response=200,
 *          description="Get a current user successful",
 *          @OA\JsonContent(
 *              type="object",
 *              @OA\Property(
 *                  property="id",
 *                  type="string",
 *                  example="userId"
 *              )
 *          )
 *      ),
 *      @OA\Response(
 *          response=401,
 *          description="Unauthenticated",
 *          @OA\JsonContent(
 *              type="object",
 *              @OA\Property(
 *                  property="message",
 *                  type="string",
 *                  example="Unauthenticated."
 *              )
 *          )
 *      ),
 *      @OA\Response(
 *          response=500,
 *          description="Failed to get user",
 *          @OA\JsonContent(
 *              type="object",
 *              @OA\Property(
 *                  property="error",
 *                  type="string",
 *                  example="Failed to get user. Please try later."
 *              )
 *          )
 *      )
 *  ),
 *  @OA\Delete(
 *      path="/users/current",
 *      summary="Deletes a user profile",
 *      tags={"Authentication"},
 *      security={{ "bearerAuth": {} }},
 *      parameters={
 *          @OA\Parameter(
 *              name="Authorization",
 *              in="header",
 *              required=true,
 *              description="Bearer token",
 *              @OA\Schema(
 *                  type="string"
 *              )
 *          )
 *      },
 *      description="Deletes a user profile",
 *      @OA\Response(
 *          response=200,
 *          description="Delete a user profile successful",
 *          @OA\JsonContent(
 *              type="object",
 *              @OA\Property(
 *                  property="message",
 *                  type="string",
 *                  example="Your profile and all events deleted successfully."
 *              )
 *          )
 *      ),
 *      @OA\Response(
 *          response=401,
 *          description="Unauthenticated",
 *          @OA\JsonContent(
 *              type="object",
 *              @OA\Property(
 *                  property="message",
 *                  type="string",
 *                  example="Unauthenticated."
 *              )
 *          )
 *      ),
 *      @OA\Response(
 *          response=500,
 *          description="Failed to delete user profile",
 *          @OA\JsonContent(
 *              type="object",
 *              @OA\Property(
 *                  property="error",
 *                  type="string",
 *                  example="Failed to delete user profile. Please try later."
 *              )
 *          )
 *      )
 *  )
 */

class AuthController extends Controller
{
}
