<?php

namespace App\Http\Controllers\Api\Event;

use App\Http\Controllers\Controller;
use App\Http\Requests\Events\StoreEventRequest;
use App\Http\Requests\Events\UpdateEventRequest;
use App\Http\Resources\EventResource;
use App\Jobs\DeleteAllPictures;
use App\Jobs\DeletePicture;
use App\Models\Event;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Log;

class EventController extends Controller
{
    public function index(Request $request): JsonResponse | ResourceCollection
    {
        try {
            $eventsCount = Event::count();
            if (!$eventsCount) {
                return response()->json(['error' => 'No events found.'], 404);
            }
            $perPage = $request->input('per_page', 10);

            $search = $request->input('search', '');

            $category = $request->input('category', '');

            $sort = $request->input('sort', '');
            $ascending = $request->input('ascending', '');
            $direction = strtolower($ascending) === 'false' ? 'desc' : 'asc';
            $sortColumns = ['title', 'date', 'priority'];

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

            if ($category && $category !== "all") {
                $events->where('category', $category);
            }

            if ($sort) {
                if (in_array($sort, $sortColumns)) {
                    switch ($sort) {
                        case 'title':
                            $events->orderBy($sort, $direction);
                            break;
                        case 'date':
                            $events->orderBy('date', $direction);
                            break;
                        case 'priority':
                            $priorityOrder = ['Low', 'Medium', 'High'];
                            $events->orderByRaw("FIELD(priority, '" . implode("','", $priorityOrder) . "') $direction");
                            break;
                    }
                } else {
                    $events->orderBy('date', 'asc');
                }
            }

            $paginatedEvents = $events->paginate($perPage);
            return EventResource::collection($paginatedEvents);
        } catch (\Throwable $th) {
            Log::error("Failed to get events: " . $th->getMessage());
            return response()->json(['error' => 'Failed to get events. Please, try later.'], 500);
        }
    }

    public function store(StoreEventRequest $request): JsonResponse | EventResource
    {
        try {
            $user_id = auth()->user()->id;
            $picture = $request->file('picture');
            if ($picture) {
                $uploadedFileUrl = Cloudinary::upload(
                    $picture->getRealPath(),
                    ['folder' => "events/{$user_id}"]
                )->getSecurePath();

                $created_event = Event::create([
                    'user_id' => $user_id,
                    ...$request->validated(),
                    'picture' => $uploadedFileUrl
                ]);
            } else {
                $created_event = Event::create([
                    'user_id' => $user_id,
                    ...$request->validated()
                ]);
            }
            if (!Event::where('id', $created_event->id)->exists()) {
                throw new \Exception('Event is not present in the list.');
            }
            return new EventResource($created_event);
        } catch (\Throwable $th) {
            Log::error("Failed to create an event: " . $th->getMessage());
            return response()->json(['error' => 'Failed to create an event. Please, try later.'], 500);
        }
    }

    public function show($id): JsonResponse | EventResource
    {
        try {
            $event = Event::findOrFail($id);
            return new EventResource($event);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => "Event not found."], 404);
        } catch (\Throwable $th) {
            Log::error("Failed to retrieve an event: " . $th->getMessage());
            return response()->json(['error' => 'Failed to retrieve an event. Please, try later.'], 500);
        }
    }

    public function update(UpdateEventRequest $request, $id): JsonResponse | EventResource
    {
        try {
            $user_id = auth()->user()->id;
            $event = Event::findOrFail($id);
            $picture = $request->input('picture');

            switch (true) {
                case $picture && !filter_var($picture, FILTER_VALIDATE_URL):
                    if ($event->picture) {
                        $publicId = "events/$user_id/" . pathinfo($event->picture, PATHINFO_FILENAME);
                        DeletePicture::dispatch($publicId);
                    }
                    $uploadedFileUrl = Cloudinary::upload(
                        $picture,
                        ['folder' => "events/{$user_id}"]
                    )->getSecurePath();
                    $event->update([
                        ...$request->validated(),
                        'picture' => $uploadedFileUrl
                    ]);
                    unlink($picture);
                    break;
                case !$picture && $event->picture:
                    $pattern = '/\/image\/upload\/(?:v\d+\/)?(events\/\d+\/[^\/]+)\.\w+$/';
                    if (preg_match($pattern, $event->picture, $matches)) {
                        DeletePicture::dispatch($matches[1]);
                        $event->update([
                            ...$request->validated(),
                            'picture' => null
                        ]);
                    }
                    break;
                default:
                    $event->update($request->validated());
                    break;
            }

            return new EventResource($event);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => "Event not found."], 404);
        } catch (\Throwable $th) {
            Log::error("Failed to update an event: " . $th->getMessage());
            return response()->json(['error' => 'Failed to update an event. Please, try later.'], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $user_id = auth()->user()->id;
            $event = Event::findOrFail($id);
            $publicId = "events/$user_id/" . pathinfo($event->picture, PATHINFO_FILENAME);
            DeletePicture::dispatch($publicId);
            $event->delete();
            if (Event::where("id", $id)->exists()) {
                throw new \Exception('Event is still present in the list.');
            }
            return response()->json(['message' => 'Event deleted successfully.'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => "Event not found."], 404);
        } catch (\Throwable $th) {
            Log::error("Failed to delete an event: " . $th->getMessage());
            return response()->json(['error' => 'Failed to delete an event. Please, try later.'], 500);
        }
    }

    public function destroyAll(): JsonResponse
    {
        try {
            $user_id = auth()->user()->id;
            $eventsCount = Event::count();
            if (!$eventsCount) {
                return response()->json(['error' => 'No events found.'], 404);
            }
            DeleteAllPictures::dispatch($user_id);
            Event::query()->delete();
            if (Event::count() > 0) {
                throw new \Exception('Some events could not be deleted.');
            }
            return response()->json(['message' => 'All events deleted successfully.'], 200);
        } catch (\Throwable $th) {
            Log::error("Failed to delete all events: " . $th->getMessage());
            return response()->json(['error' => 'Failed to delete all events. Please, try later.'], 500);
        }
    }
}
