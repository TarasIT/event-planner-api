<?php

namespace App\Http\Controllers\Swagger;

use App\Http\Controllers\Controller;

/**
 *  @OA\Info(
 *      version="1.0.0",
 *      title="Event Planner API",
 *      description="Event Planner is a Laravel API designed to manage events. It allows users to create, read, update, and delete events. Additionally, the API provides authentication features, including email verification and Google sign-up.",
 *      contact={
 *          "name": "Taras Maltsev",
 *          "url": "https://github.com/TarasIT",
 *          "email": "taras.maltsev@gmail.com"
 *      }
 *  ),
 *  @OA\Server(
 *      url="http://127.0.0.1:8000/api/documentation",
 *      description="API server"
 *  )
 *  @OA\PathItem(
 *      path="/api/documentation"
 * )
 **/

class MainController extends Controller
{
    //
}
