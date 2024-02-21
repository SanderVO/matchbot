<?php

namespace App\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;

class EloSupport
{
    /**
     * Calculate the elo rating based on scores and elo ratings for team
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
    public static function calculateTeamEloRating(int $ownScore, int $opponentScore, int $ownEloRating, int $opponentEloRating)
    {
        $eloConfig = Config::get('elo');

        $actualScore = +$opponentScore === +$ownScore ? $eloConfig['draw'] : (+$opponentScore > +$ownScore ? $eloConfig['lose'] : $eloConfig['win']);
        $expectedScore = 1 / (1 + 10 ^ ((+$opponentEloRating - +$ownEloRating) / $eloConfig['incrementalScore']));

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
        $eloConfig = Config::get('elo');

        $actualScore = +$opponentScore === +$ownScore ? $eloConfig['draw'] : (+$opponentScore > +$ownScore ? $eloConfig['lose'] : $eloConfig['win']);

        $expectedScore = 0;

        $opponentEloRatings
            ->each(function (int $opponentEloRating) use (&$expectedScore, $ownEloRating, $eloConfig) {
                $expectedScore += 1 / (1 + 10 ^ ((+$opponentEloRating - +$ownEloRating) / $eloConfig['incrementalScore']));
            });

        $expectedScore = $expectedScore / $opponentEloRatings->count();

        return round(+$ownEloRating + $eloConfig['kFactor'] * (+$actualScore - +$expectedScore), 0);
    }
}
