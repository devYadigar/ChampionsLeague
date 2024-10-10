<?php

namespace Tests\Unit\Services;

use App\Models\Club;
use App\Models\League;
use App\Services\MatchService;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class MatchServiceTest extends TestCase
{

    protected MatchService $matchService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->matchService = new MatchService();
    }

    public static function provider(): array
    {
        return [
            [3, 1, 6],
            [4, 2, 6],
            [5, 2, 10],
            [6, 3, 10],
            [7, 3, 14],
            [8, 4, 14]

        ];
    }

    #[DataProvider('provider')]
    function test_create(int $numberOfClubs, int $gamesPerWeek, int $expectedWeeks): void
    {
        $league = League::factory()->create([
            'name' => 'Premier League',
            'finished' => false,
        ]);
        
        // Create 4 clubs with specific attack/defense values
        $clubs = Club::factory()->count($numberOfClubs)->create([
            'attack' => 8,
            'defense' => 9,
        ]);
        
        $result = $this->matchService->create($league->getAttribute('ulid'));

        // check if right amount of weeks generated.
        $this->assertCount($expectedWeeks, $result);
        
        // create array of clubs to collect schedulated game number for each team
        $clubGameCount = [];
        foreach($clubs as $club) {
            $clubGameCount[$club->getAttribute('id')] = 0;
        }

        foreach ($result as $week => $matches) {
            // assert right amount of games scheduled per week
            $this->assertCount($gamesPerWeek, $matches);
            foreach( $matches as $match) {
                // check all games not played yet
                $this->assertEquals('scheduled', $match['status']);
                // check if games grouped in a right way
                $this->assertEquals($week, $match['week']);
                // check only existing clubs play 
                $this->assertArrayHasKey($match['home_club_id'], $clubGameCount);
                $this->assertArrayHasKey($match['away_club_id'], $clubGameCount);
                
                // collect schedulated game number for each team 
                $clubGameCount[$match['home_club_id']] += 1;
                $clubGameCount[$match['away_club_id']] += 1;
            }
        }

        foreach($clubGameCount as $c) {
            // check each club will play equally right amount of game
            $this->assertEquals(($numberOfClubs - 1) *2 , $c);
        }
    }
}