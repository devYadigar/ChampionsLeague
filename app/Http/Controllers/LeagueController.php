<?php

namespace App\Http\Controllers;

use App\Services\LeagueService;
use App\Http\Requests\EntityRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

class LeagueController extends Controller
{
    public function __construct(private LeagueService $leagueService)
    {
    }

    public function create(EntityRequest $request): JsonResponse
    {
        try {
            $league = $this->leagueService->create($request->input('session_id'));
            return response()->json([
                'data' => $league['data']
            ], $league['status']);
        } catch (Throwable $e) {
            return response()->json(['message' => 'Error occurred.'], 500);
        }
    }

    public function get(EntityRequest $request): JsonResponse
    {
        try {
            $league = $this->leagueService->get($request->input('session_id'));
            return response()->json($league, 200);
        } catch (Throwable $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Error occurred.'], 500);
        }
    }
}
