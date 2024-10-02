<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DecodeBase64Picture
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $picture = $request->input('picture');
            if ($request->method() === "PUT" && $picture) {
                if (filter_var($picture, FILTER_VALIDATE_URL)) {
                    return $next($request);
                }

                if (strpos($picture, 'base64,') !== false) {
                    $picture = explode('base64,', $picture)[1];
                }

                $decodedPicture = base64_decode($picture, true);
                if ($decodedPicture === false) {
                    return response(['error' => 'Invalid base64 encoding.'], 400);
                }

                $imageSize = strlen($decodedPicture);
                $maxImageSize = 20 * 1024;

                if ($imageSize > $maxImageSize) {
                    return response(['error' => 'Image size should be less than 20 kB.'], 413);
                }

                $imageData = getimagesizefromstring($decodedPicture);
                if ($imageData === false) {
                    return response(['error' => 'Invalid image data.'], 400);
                }

                $mime = $imageData['mime'];
                $extension = match ($mime) {
                    'image/jpeg' => 'jpg',
                    'image/png' => 'png',
                    'image/gif' => 'gif',
                    'image/webp' => 'webp',
                    'image/avif' => 'avif',
                    'image/svg+xml' => 'svg',
                    default => response(['error' => 'Unsupported image extension'], 400),
                };

                $directory = storage_path('app/tmp/pictures');
                if (!file_exists($directory)) {
                    mkdir($directory, 0755, true);
                }

                $fileName = uniqid('event_', true) . "." . $extension;
                $filePath = $directory . '/' . $fileName;
                file_put_contents($filePath, $decodedPicture);

                $request->merge(['picture' => $filePath]);
            }
            return $next($request);
        } catch (\Throwable $th) {
            Log::error("Failed to process picture: " . $th->getMessage());
            return response(['error' => 'Failed to process picture'], 500);
        }
    }
}
