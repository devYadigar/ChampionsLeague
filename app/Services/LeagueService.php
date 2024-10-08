<?php

namespace App\Services;

use App\Models\Club;
use App\Models\League;
use App\Models\LeagueClub;

class LeagueService
{
    public function create(string $sessionId): array
    {
        //check if league already exists
        if (League::query()->where('ulid', $sessionId)->exists()) {
            return [
                'data' => [
                    'message' => 'Resource already exists',
                    'data' => $this->get($sessionId)
                ],
                'status' => 409
            ];
        }

        // create new league
        $league = League::create([
            'name' => 'Premier league',
            'ulid' => $sessionId,
        ]);
        

        // Prepare bulk insert data
        $clubIds = Club::all()->pluck('id');
        $data = $clubIds->map(fn($clubId) => [
            'club_id' => $clubId,
            'league_id' => $league->id
        ])->toArray();

        // Bulk insert league-club relationships
        LeagueClub::query()->insert($data);

        return [
            'data' => [
                'data' => $this->get($sessionId)
            ],
            'status' => 201
        ];
    }

    public function get(string $sessionId): array
    {
        return League::query()
            ->where('ulid', $sessionId)
            ->with('clubs')
            ->firstOrFail()
            ->toArray(); 
    }
}