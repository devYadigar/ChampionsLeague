<?php

namespace App\Services;

use App\Repositories\Contracts\LeagueClubRepositoryInterface;

class LeagueClubService
{
    public function __construct(private LeagueClubRepositoryInterface $leagueClubRepository)
    {       
    }

    public function associateClubsWithLeague(int $leagueId, array $clubIds): void
    {
        $data = array_map(fn($clubId) => [
            'club_id' => $clubId,
            'league_id' => $leagueId
        ], $clubIds);

        $this->leagueClubRepository->insert($data);
    }

}