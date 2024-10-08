<?php

namespace App\Http\Controllers;

use App\Services\PredictionService;
use App\Http\Requests\EntityRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

class PredictionController extends Controller
{
    public function __construct(private PredictionService $predictionService)
    {    
    }

    public function get(EntityRequest $request): JsonResponse
    {
        try {
            $probabilities = $this->predictionService->get($request->input('session_id'));
            return response()->json($probabilities, 200);
        } catch (Throwable $e) {
            Log::error( $e->getMessage());
            return response()->json(['message' => 'Error occurred.'], 500);
        }
    }
}