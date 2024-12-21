<?php

namespace App\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;

class EloSupport
{
    /**
     * Calculate the elo rating based on scores and elo ratings
     * According to: https://en.wikipedia.org/wiki/Elo_rating_system#Mathematical_details
     *
     * @param int $ownScore
     * @param int $opponentScore
     * @param int $ownEloRating
     * @param int $opponentEloRating
     * 
     * @author Sander van Ooijen <sandervo+github@proton.me>
     * @version 1.0.0
     */
    public static function calculateEloRating(int $ownScore, int $opponentScore, int $ownEloRating, int $opponentEloRating)
    {
        $eloConfig = Config::get('elo');

        $actualScore = +$opponentScore === +$ownScore ? $eloConfig['draw'] : (+$opponentScore > +$ownScore ? $eloConfig['lose'] : $eloConfig['win']);
        $expectedScore = self::calculateExpectedScore($opponentEloRating, $ownEloRating);

        return round(+$ownEloRating + $eloConfig['kFactor'] * (+$actualScore - +$expectedScore), 0);
    }

    /**
     * Calculate the elo rating based on scores and elo ratings for team user
     * According to: https://en.wikipedia.org/wiki/Elo_rating_system#Mathematical_details
     *
     * @param int $ownScore
     * @param int $opponentScore
     * @param int $ownEloRating
     * @param Collection $opponentEloRatings
     * 
     * @author Sander van Ooijen <sandervo+github@proton.me>
     * @version 1.0.0
     */
    public static function calculateTeamUserEloRating(int $ownScore, int $opponentScore, int $ownEloRating, Collection $opponentEloRatings)
    {
        $opponentEloRating = $opponentEloRatings->avg();

        return self::calculateEloRating($ownScore, $opponentScore, $ownEloRating, $opponentEloRating);
    }

    /**
     * Calculate the expected score
     *
     * @param int $opponentEloRating
     * @param int $ownEloRating
     * 
     * @return float
     * 
     * @author Sander van Ooijen <sandervo+github@proton.me>
     * @version 1.0.0
     */
    public static function calculateExpectedScore($opponentEloRating, $ownEloRating): float
    {
        $eloConfig = Config::get('elo');

        $rating = 1 + pow(10, (((+$opponentEloRating - +$ownEloRating) !== 0 ? (+$opponentEloRating - +$ownEloRating) : 1) / $eloConfig['incrementalScore']));

        return 1 / ($rating === 0 ? 1 : $rating);
    }
}