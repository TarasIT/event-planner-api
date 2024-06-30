<?php

namespace App\Http\Controllers\Swagger;

use App\Http\Controllers\Controller;

/**
 * @OA\Post(
 *      path="/users/auth/signup",
 *      summary="Signup a new user",
 *      tags={"Authentication"},
 *      description="Signup a new user",
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
 * )
 */


class AuthController extends Controller
{
    //
}
