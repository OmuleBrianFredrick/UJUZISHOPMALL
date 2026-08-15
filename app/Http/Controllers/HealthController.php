<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class HealthController extends Controller
{
    public function __invoke(): JsonResponse
    {
        try {
            DB::connection()->getPdo();
        } catch (\Throwable $e) {
            return response()->json(['status' => 'unhealthy', 'checks' => ['database' => 'failed']], 503);
        }

        return response()->json([
            'status' => 'ok',
            'checks' => ['database' => 'ok'],
        ]);
    }
}
