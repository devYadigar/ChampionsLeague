<?php

namespace App\Services;

use App\Exceptions\CustomException;
use App\Helpers\RandomHelper;
use App\Models\Club;
use App\Models\League;
use App\Models\LeagueClub;
use App\Models\Matches;
use Illuminate\Support\Facades\DB;

class MatchService
{
    public function create(string $sessionId): array
    {
        $league = League::query()->where('ulid', $sessionId)->firstOrFail();
        // Check if matches already exist for this league
        if (Matches::where('league_id', $league->getAttribute('id'))->exists()) {
            return $this->get($sessionId);
        }

        $clubs = Club::all()->toArray();
        $numberOfClubs = count($clubs);
        
        // Add a null club for odd number of clubs
        if ($numberOfClubs % 2 != 0) {
            $clubs[] = null;
            $numberOfClubs ++;
        }
        
        $numberOfWeeks = $numberOfClubs - 1;
        $numberOfMatchesPerWeek = $numberOfClubs / 2;

        $fixtures = [];

        // set up fixtures
        for($week = 0; $week < $numberOfWeeks; $week ++) {
            for ($match = 0; $match < $numberOfMatchesPerWeek; $match ++) {
                $home = ($week + $match) % ($numberOfClubs - 1);
                $away = ($numberOfClubs - 1 - $match + $week) % ($numberOfClubs - 1);    

                if ($match == 0) {
                    $away = $numberOfClubs - 1;
                }
                if ($clubs[$home] and $clubs[$away]) {
                    $fixtures[] = [
                        'home_club_id' => $clubs[$home]['id'], 
                        'away_club_id' => $clubs[$away]['id'],
                        'league_id' => $league->getAttribute('id'),
                        'week' => $week + 1
                    ];
                    $fixtures[] = [
                        'home_club_id' => $clubs[$away]['id'], 
                        'away_club_id' => $clubs[$home]['id'],
                        'league_id' => $league->getAttribute('id'),
                        'week' => $week + 1 + $numberOfWeeks
                    ];
                }
                
            }
        }
        Matches::query()->insert($fixtures);
        return $this->get($sessionId);
    }

    public function get(string $sessionId): array
    {
        $league = League::where('ulid', $sessionId)->firstOrFail();
        $matches = Matches::where('league_id', $league->id)->orderBy('week')->get()->groupBy('week')->toArray();
        return $matches;
    }

    public function playWeek(string $sessionId, int $week): array
    {
        DB::beginTransaction();
        try {
            $league = League::where('ulid', $sessionId)->with('clubs')->firstOrFail();
            $matches = Matches::where(['league_id' => $league->getAttribute('id'), 'week' => $week])->get();
            
            $clubStats = [];

            foreach($matches as &$match) {
                if ($match->status != 'scheduled') {
                    DB::rollBack();
                    return ['data' => [
                        'message' => 'Match already played.',
                        'data' => $matches
                    ], 'status' => 409];
                }

                $homeClub = Club::query()->find($match['home_club_id']);
                $awayClub = Club::query()->find($match['away_club_id']);

                $homeGoals = max(0, ($homeClub->attack - $awayClub->defense)
                 + RandomHelper::getBiasedRandomNumber($homeClub->attack < $awayClub->defense));
                $awayGoals = max(0, ($awayClub->attack - $homeClub->defense) 
                + RandomHelper::getBiasedRandomNumber($awayClub->attack < $homeClub->defense));

                $match->setAttribute('home_goals', $homeGoals);
                $match->setAttribute('away_goals', $awayGoals);
                $match->setAttribute('status', 'completed');
                
                $homeResult = $awayResult = 'lost';
                if ($homeGoals == $awayGoals) {
                    $homeResult = 'drawn';
                    $awayResult = 'drawn';
                } elseif ($homeGoals > $awayGoals) {
                    $homeResult = 'won';
                } else {
                    $awayResult = 'won';
                }

                // Store club updates to be executed in bulk
                $clubStats[$match->home_club_id][] = ['result' => $homeResult, 'gf' => $homeGoals, 'ga' => $awayGoals];
                $clubStats[$match->away_club_id][] = ['result' => $awayResult, 'gf' => $awayGoals, 'ga' => $homeGoals];
                
                $match->save();
            }

            // Bulk update clubs stats
            foreach ($clubStats as $clubId => $stats) {
                $leagueClub = LeagueClub::where(['club_id' => $clubId, 'league_id' => $league->id])->firstOrFail();
                foreach ($stats as $stat) {
                    $leagueClub->increment('played', 1);
                    $leagueClub->increment($stat['result'], 1);
                    $leagueClub->increment('gf', $stat['gf']);
                    $leagueClub->increment('ga', $stat['ga']);
                }
                $leagueClub->update();
            }

            $numberOfClubs = $league->clubs->count();
            // increment week after finished simulating a week's games
            $totalWeeks = ($numberOfClubs - 1 + ($numberOfClubs % 2)) * 2;
            if ($league->getAttribute('week') <  $totalWeeks) {
                $league->increment('week');
            } else {
                // if all weeks played, change finished to true
                $league->setAttribute('finished', true);
                $league->save();
            }
             
            DB::commit();
            return ['data' => $matches->toArray(), 'status' => 200];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['data' => ['message' => 'Error occurred.'], 'status' => 500];
        }
    }

    public function playAll(string $sessionId)
    {
        $numberOfClubs = Club::count();
        // if even numbers, n -1 , else n weeks per hal period
        $numberOfWeeks = ($numberOfClubs - 1 + ($numberOfClubs % 2)) * 2;
        $allMatches = [];

        for($week = 1; $week <= $numberOfWeeks; $week++)
        {
            $matches = $this->playWeek($sessionId, $week)['data'];
            $allMatches[$week] = array_key_exists('data', $matches) ? $matches['data'] : $matches;
        }
        return $allMatches; 
    }
}