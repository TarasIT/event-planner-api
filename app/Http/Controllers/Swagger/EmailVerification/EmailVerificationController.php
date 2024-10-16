<?php

namespace App\Http\Controllers\Swagger\EmailVerification;

use App\Http\Controllers\Controller;

/**
 *  @OA\Get(
 *     path="/email/verify/{user_id}/{hash}",
 *     tags={"Email Verification"},
 *     summary="Verifies user's email",
 *     description="Verifies the user's email address using a signed URL",
 *     @OA\Parameter(
 *         name="user_id",
 *         in="path",
 *         required=true,
 *         description="User ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="hash",
 *         in="path",
 *         required=true,
 *         description="Hash",
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *         name="expires",
 *         in="query",
 *         required=true,
 *         description="Verification link expiration time",
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *         name="signature",
 *         in="query",
 *         required=true,
 *         description="Verification signature",
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response=302,
 *         description="Redirect to the frontend with a message in a query, e.g. 'Email verified successfully' or 'Invalid URL provided.'"
 *     )
 *  ),
 *  @OA\Post(
 *     path="/email/resend",
 *     tags={"Email Verification"},
 *     summary="Resends email verification link",
 *     description="Resends the email verification link to the user",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email", "password"},
 *             @OA\Property(property="email", type="string", format="email"),
 *             @OA\Property(property="password", type="string", format="password"),
 *             example={"email": "john.doe@example.com", "password": "johnPassword"}
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Verification link resent",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Verification link resent! Check your email.",
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Email is already verified",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="error",
 *                 type="string",
 *                 example="Email is already verified.",
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Wrong credentials",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="error",
 *                 type="string",
 *                 example="Email or password does not match the record.",
 *             )
 *         )
 *  ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="error",
 *                 type="string",
 *                 example="User not found.",
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
 *         description="Failed to resend verification email",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="error",
 *                 type="string",
 *                 example="Failed to resend verification email. Please try later.",
 *             )
 *         )
 *     )
 *  )
 */

class EmailVerificationController extends Controller {}
