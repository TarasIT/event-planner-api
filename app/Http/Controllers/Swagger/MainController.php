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
 *      url="https://event-planner-api.onrender.com/api",
 *      description="API server"
 *  ),
 *  @OA\PathItem(
 *      path="/api"
 *  ),
 *  @OA\Components(
 *      @OA\SecurityScheme(
 *          securityScheme="bearerAuth",
 *          type="http",
 *          scheme="bearer"
 *      )
 *  )
 **/

class MainController extends Controller
{
    //
}
