<?php

namespace App\Http\Controllers\Integrations;

use App\Http\Controllers\Controller;
use App\Services\Integrations\EGroceryIntegrationHealthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EGroceryIntegrationHealthController extends Controller
{
    public function __invoke(Request $request, EGroceryIntegrationHealthService $healthService): JsonResponse
    {
        $hours = max(1, min((int) $request->query('hours', 24), 168));
        $snapshot = $healthService->snapshot($hours);

        return response()->json($snapshot);
    }
}

