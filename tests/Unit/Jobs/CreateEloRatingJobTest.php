<?php

namespace Tests\Unit\Jobs;

use App\Jobs\CalculateEloRatingJob;
use App\Models\Event;
use App\Models\Season;
use App\Models\Team;
use App\Models\TeamResult;
use App\Models\TeamResultUser;
use App\Models\TeamUser;
use App\Models\User;
use App\Models\UserEloRating;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;


class CreateEloRatingJobTest extends TestCase
{
    /**
     * Can calculate general elo rating for two players
     * 
     * @author Sander van Ooijen <sandervo+github@proton.me>
     * @version 1.0.0
     */
    public function testCanCalculateGeneralEloRatingForTwoPlayersWithoutScore(): void
    {
        // Arrange
        $eloRatingScores = Config::get('elo');

        $season = Season::factory()
            ->create();

        $event = Event::factory()
            ->create([
                'season_id' => $season->id,
                'status' => 1,
                'start_date' => Carbon::now()->subDays(1)
            ]);

        $user = User::factory()
            ->create();

        $opponent = User::factory()
            ->create();

        $team = Team::factory()
            ->create();

        $oponentTeam = Team::factory()
            ->create();

        TeamUser::factory()
            ->create([
                'user_id' => $user->id,
                'team_id' => $team->id
            ]);

        TeamUser::factory()
            ->create([
                'user_id' => $opponent->id,
                'team_id' => $oponentTeam->id
            ]);

        $teamResult = TeamResult::factory()
            ->create([
                'score' => 10,
                'team_id' => $team->id,
                'event_id' => $event->id
            ]);

        $opponentTeamResult = TeamResult::factory()
            ->create([
                'score' => 5,
                'team_id' => $oponentTeam->id,
                'event_id' => $event->id
            ]);

        TeamResultUser::factory()
            ->create([
                'score' => 10,
                'team_result_id' => $teamResult->id,
                'user_id' => $user->id
            ]);

        TeamResultUser::factory()
            ->create([
                'score' => 5,
                'team_result_id' => $opponentTeamResult->id,
                'user_id' => $opponent->id
            ]);

        // Act
        $job = new CalculateEloRatingJob($event);
        $job->handle();

        // Assert
        $eloRatings = UserEloRating::get();

        $this->assertEquals(
            $eloRatings
                ->where('user_id', $user->id)
                ->where('event_id', $event->id)
                ->whereNull('objectable_type')
                ->whereNull('objectable_id')
                ->first()
                ->elo_rating,
            ($eloRatingScores['defaultRating'] + 400) / 1
        );

        $this->assertEquals(
            $eloRatings
                ->where('user_id', $opponent->id)
                ->where('event_id', $event->id)
                ->whereNull('objectable_type')
                ->whereNull('objectable_id')
                ->first()
                ->elo_rating,
            ($eloRatingScores['defaultRating'] - 400) / 1
        );
    }

    /**
     * Can calculate general elo rating for two players with 2 wins and 1 loss
     * 
     * @author Sander van Ooijen <sandervo+github@proton.me>
     * @version 1.0.0
     */
    public function testCanCalculateGeneralEloRatingForTwoPlayersWithTwoWinsAndOneLoss(): void
    {
        // Arrange
        $limit = 3;

        $season = Season::factory()
            ->create();

        $user = User::factory()
            ->create();

        $opponent = User::factory()
            ->create();

        $team = Team::factory()
            ->create();

        $oponentTeam = Team::factory()
            ->create();

        for ($index = $limit; $index > 0; $index--) {
            $event = Event::factory()
                ->create([
                    'season_id' => $season->id,
                    'status' => 1,
                    'start_date' => Carbon::now()->subDays($index + 1)
                ]);

            TeamUser::factory()
                ->create([
                    'user_id' => $user->id,
                    'team_id' => $team->id
                ]);

            TeamUser::factory()
                ->create([
                    'user_id' => $opponent->id,
                    'team_id' => $oponentTeam->id
                ]);

            $teamResult = TeamResult::factory()
                ->create([
                    'score' => $index === 1 ? 5 : 10,
                    'team_id' => $team->id,
                    'event_id' => $event->id
                ]);

            $opponentTeamResult = TeamResult::factory()
                ->create([
                    'score' => $index === 1 ? 10 : 5,
                    'team_id' => $oponentTeam->id,
                    'event_id' => $event->id
                ]);

            TeamResultUser::factory()
                ->create([
                    'score' => $index === 1 ? 5 : 10,
                    'team_result_id' => $teamResult->id,
                    'user_id' => $user->id
                ]);

            TeamResultUser::factory()
                ->create([
                    'score' => $index === 1 ? 10 : 5,
                    'team_result_id' => $opponentTeamResult->id,
                    'user_id' => $opponent->id
                ]);

            // Act
            $job = new CalculateEloRatingJob($event);
            $job->handle();
        }

        // Assert
        $eloRatings = UserEloRating::query()
            ->with([
                'event'
            ])
            ->get();

        $eloRatingQuery = $eloRatings
            ->whereNull('objectable_type')
            ->whereNull('objectable_id');

        $ownTotalElo = $eloRatingQuery
            ->where('user_id', $user->id)
            ->sum('elo_rating');

        $opponentTotalElo = $eloRatingQuery
            ->where('user_id', $opponent->id)
            ->sum('elo_rating');

        $this->assertEquals(
            $eloRatingQuery
                ->where('user_id', $user->id)
                ->sortBy(
                    function ($data) {
                        return Carbon::parse($data->event->start_date)->toTimeString();
                    },
                    true
                )
                ->first()
                ->elo_rating,
            ($ownTotalElo + 400 * (2 - 1)) / $limit
        );

        $this->assertEquals(
            $eloRatingQuery
                ->where('user_id', $opponent->id)
                ->sortBy(
                    function ($data) {
                        return Carbon::parse($data->event->start_date)->toTimeString();
                    },
                    true
                )
                ->first()
                ->elo_rating,
            ($opponentTotalElo + 400 * (1 - 2)) / $limit
        );
    }
}
