<?php

namespace App\Http\Controllers\Api\PingDatabase;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PingDatabaseController extends Controller
{
    public function ping(): JsonResponse
    {
        try {
            DB::connection()->getPdo();
            return response()->json(['status' => 'success', 'message' => 'Database connection is alive.'], 200);
        } catch (\Throwable $th) {
            Log::error("Database connection failed: " . $th->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Database connection failed.'], 500);
        }
    }
}
