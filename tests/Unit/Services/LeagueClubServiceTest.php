<?php

namespace Tests\Unit\Services;

use App\Repositories\Contracts\LeagueClubRepositoryInterface;
use App\Services\LeagueClubService;
use Mockery;
use Tests\TestCase;

class LeagueClubServiceTest extends TestCase
{
    protected object $leagueClubRepository;
    protected object $leagueClubService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->leagueClubRepository = Mockery::mock(LeagueClubRepositoryInterface::class);
        $this->leagueClubService = new LeagueClubService($this->leagueClubRepository);
    }

    public function test_associate_clubs_with_league(): void
    {
        $leagueId = 1;
        $clubIds = [1, 2, 3];
        $expectedData = [
            ['club_id' => 1, 'league_id' => $leagueId],
            ['club_id' => 2, 'league_id' => $leagueId],
            ['club_id' => 3, 'league_id' => $leagueId],
        ];

        $this->leagueClubRepository
            ->shouldReceive('insert')
            ->once()
            ->with($expectedData);

        $this->leagueClubService->associateClubsWithLeague($leagueId, $clubIds);

        $this->assertTrue(true); 
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}