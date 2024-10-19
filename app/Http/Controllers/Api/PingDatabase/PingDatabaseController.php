<?php

namespace App\Http\Controllers\Api\PingDatabase;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class PingDatabaseController extends Controller
{
    public function ping()
    {
        try {
            DB::connection()->getPdo();
            return response()->json(['status' => 'success', 'message' => 'Database connection is alive.'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Database connection failed.'], 500);
        }
    }
}
