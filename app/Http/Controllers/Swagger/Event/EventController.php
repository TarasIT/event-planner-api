<?php

namespace App\Http\Controllers\Swagger\Event;

use App\Http\Controllers\Controller;

/**
 * @OA\Get(
 *     path="/events",
 *     summary="Gets list of events",
 *     tags={"Event"},
 *     security={{ "bearerAuth": {} }},
 *     @OA\Parameter(
 *         name="Authorization",
 *         in="header",
 *         required=true,
 *         description="Bearer token",
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="per_page",
 *         in="query",
 *         description="Number of events per page",
 *         required=false,
 *         @OA\Schema(
 *             type="integer",
 *             default=10
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="page",
 *         in="query",
 *         description="Current page of events",
 *         required=false,
 *         @OA\Schema(
 *             type="integer",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="search",
 *         in="query",
 *         description="Searches term for events",
 *         required=false,
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="category",
 *         in="query",
 *         description="Searches events by category",
 *         required=false,
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="sort",
 *         in="query",
 *         description="Sorts events by title, date or priority",
 *         required=false,
 *         @OA\Schema(
 *             type="string",
 *             enum={"name", "date", "priority"},
 *             example="date"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="ascending",
 *         in="query",
 *         description="Sorts events by title, date or priority in ascending or descending order",
 *         required=false,
 *         @OA\Schema(
 *             type="boolean",
 *             enum={"true", "false"},
 *             example="true"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful response",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     ref="#/components/schemas/ResponseEventScheme",
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="No events found",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="error",
 *                 type="string",
 *                 example="No events found."
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Failed to get events",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="error",
 *                 type="string",
 *                 example="Failed to get events. Please, try later."
 *             )
 *         )
 *     )
 * ),
 *
 *
 *
 * @OA\Post(
 *      path="/events",
 *      summary="Creates a new event",
 *      tags={"Event"},
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
 *      description="Creates a new event",
 *      @OA\RequestBody(
 *          required=true,
 *          @OA\MediaType(
 *              mediaType="multipart/form-data",
 *              @OA\Schema(
 *                  ref="#/components/schemas/CreateEvent"
 *              )
 *          )
 *      ),
 *      @OA\Response(
 *          response=201,
 *          description="Event created successfully",
 *          @OA\JsonContent(
 *              type="object",
 *              @OA\Property(
 *                  property="data",
 *                  ref="#/components/schemas/ResponseEventScheme"
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
 *          description="Failed to create an event",
 *          @OA\JsonContent(
 *              type="object",
 *              @OA\Property(
 *                  property="error",
 *                  type="string",
 *                  example="Failed to create an event. Please, try later."
 *              )
 *          )
 *      ),
 * ),
 *
 *
 *
 * @OA\Get(
 *     path="/events/{id}",
 *     summary="Gets the event by id",
 *     tags={"Event"},
 *     security={{ "bearerAuth": {} }},
 *     @OA\Parameter(
 *         name="Authorization",
 *         in="header",
 *         required=true,
 *         description="Bearer token",
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Event ID",
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful response",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="data",
 *                 ref="#/components/schemas/ResponseEventScheme",
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Event not found",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="error",
 *                 type="string",
 *                 example="Event not found."
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Failed to retrieve an event",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="error",
 *                 type="string",
 *                 example="Failed to retrieve an event. Please, try later."
 *             )
 *         )
 *     )
 * ),
 *
 *
 *
 * @OA\Put(
 *     path="/events/{id}",
 *     summary="Updates the event by id",
 *     tags={"Event"},
 *     security={{ "bearerAuth": {} }},
 *     description="Updates the event by id",
 *     @OA\Parameter(
 *         name="Authorization",
 *         in="header",
 *         required=true,
 *         description="Bearer token",
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Event ID",
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 ref="#/components/schemas/UpdateEvent"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful response",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="data",
 *                 ref="#/components/schemas/ResponseEventScheme",
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Event not found",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="error",
 *                 type="string",
 *                 example="Event not found."
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Failed to update an event",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="error",
 *                 type="string",
 *                 example="Failed to update an event. Please, try later."
 *             )
 *         )
 *     )
 * ),
 *
 *
 *
 * @OA\Delete(
 *     path="/events/{id}",
 *     summary="Deletes the event by id",
 *     tags={"Event"},
 *     security={{ "bearerAuth": {} }},
 *     @OA\Parameter(
 *         name="Authorization",
 *         in="header",
 *         required=true,
 *         description="Bearer token",
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Event ID",
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Event deleted successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Event deleted successfully."
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Event not found",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="error",
 *                 type="string",
 *                 example="Event not found."
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Failed to delete an event",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="error",
 *                 type="string",
 *                 example="Failed to delete an event. Please, try later."
 *             )
 *         )
 *     )
 * ),
 *
 *
 *
 * @OA\Delete(
 *     path="/events",
 *     summary="Deletes all events",
 *     tags={"Event"},
 *     security={{ "bearerAuth": {} }},
 *     @OA\Parameter(
 *         name="Authorization",
 *         in="header",
 *         required=true,
 *         description="Bearer token",
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="All events deleted successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="All events deleted successfully."
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="No events found",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="error",
 *                 type="string",
 *                 example="No events found."
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Failed to delete all events",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="error",
 *                 type="string",
 *                 example="Failed to delete all events. Please, try later."
 *             )
 *         )
 *     )
 * ),
 *
 *
 *
 * @OA\Schema(
 *     schema="CreateEvent",
 *     type="object",
 *     required={"title", "date", "time"},
 *     @OA\Property(
 *         property="title",
 *         type="string",
 *         description="Title of the event"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         description="Description of the event"
 *     ),
 *     @OA\Property(
 *         property="date",
 *         type="string",
 *         description="Date of the event"
 *     ),
 *     @OA\Property(
 *         property="time",
 *         type="string",
 *         description="Time of the event"
 *     ),
 *     @OA\Property(
 *         property="location",
 *         type="string",
 *         description="Location of the event"
 *     ),
 *     @OA\Property(
 *         property="category",
 *         type="string",
 *         description="Category of the event"
 *     ),
 *     @OA\Property(
 *         property="priority",
 *         type="string",
 *         enum={"low", "medium", "high"},
 *         description="Priority of the event"
 *     ),
 *     @OA\Property(
 *         property="picture",
 *         type="string",
 *         format="binary",
 *         description="Picture file (jpeg, png, jpg, avif, gif, webp, svg)"
 *     ),
 *     example={
 *         "title": "Meeting",
 *         "description": "This meeting is very important",
 *         "date": "01/07/2024",
 *         "time": "09:00 AM",
 *         "location": "Kyiv",
 *         "category": "Business",
 *         "picture": "binary data of the picture",
 *         "priority": "high"
 *     }
 * ),
 *
 *
 *
 *
 * @OA\Schema(
 *     schema="UpdateEvent",
 *     type="object",
 *     required={"title", "date", "time"},
 *     @OA\Property(
 *         property="title",
 *         type="string",
 *         description="Title of the event"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         description="Description of the event"
 *     ),
 *     @OA\Property(
 *         property="date",
 *         type="string",
 *         description="Date of the event"
 *     ),
 *     @OA\Property(
 *         property="time",
 *         type="string",
 *         description="Time of the event"
 *     ),
 *     @OA\Property(
 *         property="location",
 *         type="string",
 *         description="Location of the event"
 *     ),
 *     @OA\Property(
 *         property="category",
 *         type="string",
 *         description="Category of the event"
 *     ),
 *     @OA\Property(
 *         property="priority",
 *         type="string",
 *         enum={"low", "medium", "high"},
 *         description="Priority of the event"
 *     ),
 *     @OA\Property(
 *         property="picture",
 *         type="string",
 *         description="Base64 encoded string of the event picture"
 *     ),
 *     example={
 *         "title": "Meeting",
 *         "description": "This meeting is very important",
 *         "date": "01/07/2024",
 *         "time": "09:00 AM",
 *         "location": "Kyiv",
 *         "category": "Business",
 *         "picture": "base64-encoded-string",
 *         "priority": "high"
 *     }
 * ),
 *
 *
 *
 * @OA\Schema(
 *     schema="ResponseEventScheme",
 *     type="object",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="Event ID"
 *     ),
 *     @OA\Property(
 *         property="title",
 *         type="string",
 *         description="Title of the event"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         description="Description of the event"
 *     ),
 *     @OA\Property(
 *         property="date",
 *         type="string",
 *         description="Date of the event"
 *     ),
 *     @OA\Property(
 *         property="time",
 *         type="string",
 *         description="Time of the event"
 *     ),
 *     @OA\Property(
 *         property="location",
 *         type="string",
 *         description="Location of the event"
 *     ),
 *     @OA\Property(
 *         property="category",
 *         type="string",
 *         description="Category of the event"
 *     ),
 *     @OA\Property(
 *         property="priority",
 *         type="string",
 *         enum={"low", "medium", "high"},
 *         description="Priority of the event"
 *     ),
 *     @OA\Property(
 *         property="picture",
 *         type="string",
 *         description="Picture of the event"
 *     ),
 *     example={
 *         "id": "4",
 *         "title": "Meeting",
 *         "description": "This meeting is very important",
 *         "date": "01/07/2024",
 *         "time": "09:00 AM",
 *         "location": "Kyiv",
 *         "category": "Business",
 *         "picture": "path/to/the/picture/in/the/cloud/storage",
 *         "priority": "high"
 *     }
 * )
 */


class EventController extends Controller {}
