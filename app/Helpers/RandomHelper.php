<?php

namespace App\Helpers;

class RandomHelper
{
    /**
     * Generate a biased random number.
     * 
     * @return int
     */
    public static function getBiasedRandomNumber(bool $isWeakerTeam): int
    {   // -0.1 make sure it leans towards minus to get more surprized results
        $random = (mt_rand() / mt_getrandmax()) + ($isWeakerTeam ? 0.1 : 0);

        // Skew the random number based on whether the team is weaker
        $biasFactor = $isWeakerTeam ? 1.5 : 0.5;

        // it will get more likely little number to make surprize result less likely
        $biasedNumber = pow(2 * $random - 1, 3) * 5 * $biasFactor;
        return (int)$biasedNumber;
    }
}