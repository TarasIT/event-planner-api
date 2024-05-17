<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Events\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Http\Resources\EventResource;
use App\Models\Event;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EventController extends Controller
{
    public function index()
    {
        try {
            return EventResource::collection(Event::all());
        } catch (\Throwable $th) {
            return response(['error' => 'Failed to get all events. Please, try later.'], 500);
        }
    }

    public function store(StoreEventRequest $request)
    {
        try {
            $user_id = auth()->user()->id;
            $created_event = Event::create([
                'user_id' => $user_id,
                ...$request->validated()
            ]);
            return new EventResource($created_event);
        } catch (\Throwable $th) {
            return response(['error' => 'Failed to create an event. Please, try later.'], 500);
        }
    }

    public function show($id)
    {
        try {
            if (!is_numeric($id)) {
                return response(['error' => "Invalid event id='$id'"], 400);
            }
            $event = Event::findOrFail($id);
            return new EventResource($event);
        } catch (ModelNotFoundException $e) {
            return response(['error' => "Event with id='$id' is not found"], 404);
        } catch (\Throwable $th) {
            return response(['error' => 'Failed to retrieve an event. Please, try later.'], 500);
        }
    }

    public function update(UpdateEventRequest $request, $id)
    {
        try {
            if (!is_numeric($id)) {
                return response(['error' => "Invalid event id='$id'"], 400);
            }
            $event = Event::findOrFail($id);
            $event->update($request->validated());
            return new EventResource($event);
        } catch (ModelNotFoundException $e) {
            return response(['error' => "Event with id='$id' is not found"], 404);
        } catch (\Throwable $th) {
            return response(['error' => 'Failed to update an event. Please, try later.'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            if (!is_numeric($id)) {
                return response(['error' => "Invalid event id='$id'"], 400);
            }
            $event = Event::findOrFail($id);
            $event->delete();
            return response(['message' => 'Event deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => "Event with id='$id' is not found"], 404);
        } catch (\Throwable $th) {
            return response(['error' => 'Failed to delete an event. Please, try later.'], 500);
        }
    }
}
