<?php

namespace App\Services;

use App\Repositories\Contracts\ClubRepositoryInterface;
use App\Repositories\Contracts\LeagueRepositoryInterface;
use Illuminate\Support\Facades\DB;

class LeagueService
{
    public function __construct(
        private LeagueRepositoryInterface $leagueRepository,
        private ClubRepositoryInterface $clubRepository,
        private LeagueClubService $leagueClubService
    )
    {    
    }

    public function create(string $sessionId): array
    {
        return DB::transaction(function () use ($sessionId) {
            if ($this->leagueRepository->exists($sessionId)) {
                return $this->leagueAlreadyExistsResponse( $sessionId);
            }
    
            $league = $this->leagueRepository->create([
                'name' => 'Premier league',
                'ulid' => $sessionId,
            ]);
            $clubIds = $this->clubRepository->pluck('id');
            $this->leagueClubService->associateClubsWithLeague($league['id'],$clubIds);
    
            return $this->leagueCreatedResponse($sessionId);
        });
    }

    public function get(string $sessionId): array
    {
        return $this->leagueRepository->getWithClubs($sessionId); 
    }

    private function leagueAlreadyExistsResponse(string $sessionId): array
    {
        return [
            'data' => [
                'message' => 'Resource already exists',
                'data' => $this->get($sessionId)
            ],
            'status' => 409
        ];
    }

    private function leagueCreatedResponse(string $sessionId): array
    {
        return [
            'data' => [
                'data' => $this->get($sessionId)
            ],
            'status' => 201
        ];
    }
}