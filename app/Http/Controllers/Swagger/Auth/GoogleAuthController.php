<?php

namespace App\Http\Controllers\Swagger\Auth;

use App\Http\Controllers\Controller;

/**
 *  @OA\Get(
 *     path="/auth/google/redirect",
 *     tags={"Google Authentication"},
 *     summary="Redirect to Google for authentication",
 *     description="Redirects the user to Google for authentication",
 *     @OA\Response(
 *         response=302,
 *         description="Redirect to Google"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Failed google redirect. Please try later."
 *     )
 *  ),
 * @OA\Get(
 *     path="/auth/google/callback",
 *     tags={"Google Authentication"},
 *     summary="Handle Google callback",
 *     description="Handles the callback from Google after authentication and redirects to the frontend with an authentication token",
 *     @OA\Parameter(
 *         name="token",
 *         in="query",
 *         description="Authentication token received after successful Google authentication",
 *         required=true,
 *         @OA\Schema(
 *             type="string",
 *             example="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c" // Example token value
 *         )
 *     ),
 *     @OA\Response(
 *         response=302,
 *         description="Redirected to the frontend with an authentication token"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Failed Google callback. Please try later."
 *     )
 * )
 */

class GoogleAuthController extends Controller {}
