<?php

namespace App\Http\Controllers\Swagger\PasswordReset;

use App\Http\Controllers\Controller;

/**
 * @OA\Post(
 *     path="/forgot-password",
 *     tags={"Password Reset"},
 *     summary="Sends password reset link to the user's email",
 *     description="Sends a password reset link to the user's email",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email"},
 *             @OA\Property(property="email", type="string", format="email")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Reset link sent",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="We have emailed your password reset link."
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="error",
 *                 type="string",
 *                 example="User not found."
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Request validation error",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="The email field is required.",
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Failed to send reset link.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="error",
 *                 type="string",
 *                 example="Failed to send reset link. Please try later."
 *             )
 *         )
 *     )
 * ),
 * @OA\Post(
 *     path="/reset-password",
 *     tags={"Password Reset"},
 *     summary="Resets password",
 *     description="Resets the user's password using the provided token",
 *     @OA\Parameter(
 *         name="token",
 *         in="path",
 *         required=true,
 *         description="Token for password reset",
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email", "password", "password_confirmation", "token"},
 *             @OA\Property(property="email", type="string", format="email"),
 *             @OA\Property(property="password", type="string", format="password"),
 *             @OA\Property(property="password_confirmation", type="string", format="password"),
 *             example={"email": "user@example.com", "password": "newPassword", "password_confirmation": "newPassword"}
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Password reset successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Your password has been reset."
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid token",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="error",
 *                 type="string",
 *                 example="This password reset token is invalid."
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="error",
 *                 type="string",
 *                 example="User not found."
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Request validation error",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="The email field is required.",
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Failed to reset password. Please try later.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="error",
 *                 type="string",
 *                 example="Failed to reset password. Please try later."
 *             )
 *         )
 *     )
 * )
 */

class PasswordResetController extends Controller
{
}
