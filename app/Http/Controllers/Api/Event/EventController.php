<?php

namespace App\Http\Controllers\Api\Event;

use App\Http\Controllers\Controller;
use App\Http\Requests\Events\StoreEventRequest;
use App\Http\Requests\Events\UpdateEventRequest;
use App\Http\Resources\EventResource;
use App\Models\Event;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request)
    {
        try {
            $eventsCount = Event::count();
            if (!$eventsCount) {
                return response(['error' => 'No events found'], 404);
            }
            $perPage = $request->input('per_page', 10);
            $search = $request->input('search', '');
            $events = Event::query();
            if ($search) {
                $events = $events->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%$search%")
                        ->orWhere('description', 'like', "%$search%")
                        ->orWhere('date', 'like', "%$search%")
                        ->orWhere('time', 'like', "%$search%")
                        ->orWhere('location', 'like', "%$search%")
                        ->orWhere('category', 'like', "%$search%")
                        ->orWhere('priority', 'like', "%$search%");
                });
            }
            $paginatedEvents = $events->paginate($perPage);
            return EventResource::collection($paginatedEvents);
        } catch (\Throwable $th) {
            return response(['error' => 'Failed to get events. Please, try later.'], 500);
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

    public function destroyAll()
    {
        try {
            $eventsCount = Event::count();
            if (!$eventsCount) {
                return response(['error' => 'No events found'], 404);
            }
            Event::query()->delete();
            return response(['message' => 'All events deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response(['error' => "Events are not found"], 404);
        } catch (\Throwable $th) {
            return response(['error' => 'Failed to delete all events. Please, try later.'], 500);
        }
    }
}