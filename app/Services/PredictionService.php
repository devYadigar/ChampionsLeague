<?php

namespace App\Services;

use App\Models\League;

class PredictionService
{
    public function get(string $sessionId): array
    {
        $league = League::query()
            ->where('ulid', $sessionId)
            ->with('clubs')
            ->firstOrFail()
            ->toArray();

        // calculate the highest point the leader currently has    
        $highestPoint = $this->calculateClubPoints($league['clubs'][0]);
        $numberOfClubs = count($league['clubs']);
        $totalWeeks = ($numberOfClubs - 1 + $numberOfClubs % 2) * 2;
        $remainingWeeks = $totalWeeks - $league['week'] + 1;
        $probabilities = [];
        $totalScore = 0;
        foreach ($league['clubs'] as $key => $club) {
            
            // if league is finished, all other teams except leader has 0 probability to win the league.
            if ($league['finished']) {
                $clubScore = $key == 0 ? 1 : 0;
            } else {
                $goalDifference = $club['pivot']['gf'] - $club['pivot']['ga'];
                $strengh = ($club['attack'] + $club['defense']) / 20;
                $clubPoints = $this->calculateClubPoints($club);
            
                // if in remaining weeks club cannot outscore 1st place, then it has 0 probability to win anything.
                $clubScore = $clubPoints + $remainingWeeks * 3 < $highestPoint ? 0 : max(0,$clubPoints + $goalDifference * 0.5 + $strengh * 2); 
            }
            $totalScore += $clubScore;
            $clubScores[$club['name']] = $clubScore;
        }

        foreach ($clubScores as $club => $score) {
            $probabilities[$club] = ($score / $totalScore) *100;
        }
        return $probabilities;
    }

    private function calculateClubPoints(array $club): int
    {
        return $club['pivot']['won'] * 3 + $club['pivot']['drawn'];
    }
}