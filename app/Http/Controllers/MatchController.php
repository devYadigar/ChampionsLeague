<?php

namespace App\Http\Controllers;

use App\Services\MatchService;
use App\Http\Requests\EntityRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

class MatchController extends Controller
{
    public function __construct(private MatchService $matchService)
    {
    }

    public function create(EntityRequest $request): JsonResponse
    {
        try {
            $matches = $this->matchService->create($request->input('session_id'));
            return response()->json($matches, 201);
        } catch (Throwable $e) {
            Log::error($e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine());
            return response()->json(['message' => 'Error occurred.'], 500);
        }
        
    }

    public function get(EntityRequest $request): JsonResponse
    {
        try {
            return response()->json($this->matchService->get($request->input('session_id')), 200);
        } catch (Throwable $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Error occurred.'], 500);
        }     
    }


    public function playWeek(EntityRequest $request): JsonResponse
    {
        try {
            $matches = $this->matchService->playWeek($request->input('session_id'), $request->input('week'));
            return response()->json($matches['data'], $matches['status']);
        } catch (Throwable $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Error occurred.'], 500);
        } 
    }

    public function playAll(EntityRequest $request): JsonResponse
    {
        try{
            $matches = $this->matchService->playAll($request->input('session_id'));
            return response()->json($matches, 200);
        } catch (Throwable $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Error occurred.'], 500);
        } 
    }
}