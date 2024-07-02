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
 *  @OA\Get(
 *     path="/auth/google/callback",
 *     tags={"Google Authentication"},
 *     summary="Handle Google callback",
 *     description="Handles the callback from Google after authentication",
 *     @OA\Response(
 *         response=200,
 *         description="Successfully handled Google callback"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Failed google callback. Please try later."
 *     )
 *  )
 */

class GoogleAuthController extends Controller
{
}
