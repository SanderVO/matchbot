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
use App\Support\EloSupport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;


class CreateEloRatingJobTest extends TestCase
{
    /**
     * Can calculate general elo rating for two teams and players without score
     * 
     * @author Sander van Ooijen <sandervo+github@proton.me>
     * @version 1.0.0
     */
    public function testCanCalculateGeneralEloRatingForTwoTeamsAndPlayersWithoutScore(): void
    {
        // Arrange
        $eloConfig = Config::get('elo');

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

        $opponentTeam = Team::factory()
            ->create();

        TeamUser::factory()
            ->create([
                'user_id' => $user->id,
                'team_id' => $team->id
            ]);

        TeamUser::factory()
            ->create([
                'user_id' => $opponent->id,
                'team_id' => $opponentTeam->id
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
                'team_id' => $opponentTeam->id,
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
        $job = new CalculateEloRatingJob($event->id);
        $job->handle();

        // Assert
        $eloRatings = UserEloRating::get();

        $newTeamOwnEloRating = EloSupport::calculateEloRating(+$teamResult->score, +$opponentTeamResult->score, +$eloConfig['defaultRating'], +$eloConfig['defaultRating']);
        $opponentTeamOwnEloRating = EloSupport::calculateEloRating(+$opponentTeamResult->score, +$teamResult->score, +$eloConfig['defaultRating'], +$eloConfig['defaultRating']);

        $this->assertEquals(
            $eloRatings
                ->where('event_id', $event->id)
                ->whereNull('objectable_type')
                ->whereNull('objectable_id')
                ->where('scorable_type', Team::class)
                ->where('scorable_id', $team->id)
                ->first()
                ->elo_rating,
            $newTeamOwnEloRating
        );

        $this->assertEquals(
            $eloRatings
                ->where('event_id', $event->id)
                ->whereNull('objectable_type')
                ->whereNull('objectable_id')
                ->where('scorable_type', Team::class)
                ->where('scorable_id', $opponentTeam->id)
                ->first()
                ->elo_rating,
            $opponentTeamOwnEloRating
        );
    }

    /**
     * Can calculate general elo rating for two teams and players with 2 wins and 1 loss
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

        $opponentTeam = Team::factory()
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
                    'team_id' => $opponentTeam->id
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
                    'team_id' => $opponentTeam->id,
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
            $job = new CalculateEloRatingJob($event->id);
            $job->handle();
        }

        // Assert
        $lastEvent = Event::query()
            ->with([
                'teamResults'
            ])
            ->orderByDesc('start_date')
            ->first();

        $ownScore = $lastEvent
            ->teamResults
            ->where('team_id', $team->id)
            ->first()
            ->score;

        $opponentScore = $lastEvent
            ->teamResults
            ->where('team_id', $opponentTeam->id)
            ->first()
            ->score;

        $ownEloRating = UserEloRating::query()
            ->whereNull('objectable_type')
            ->whereNull('objectable_id')
            ->where('scorable_type', Team::class)
            ->where('scorable_id', $team->id)
            ->orderBy(
                Event::selectRaw('start_date')
                    ->whereColumn('user_elo_ratings.event_id', 'events.id'),
                'DESC'
            )
            ->first()
            ->elo_rating;

        $ownUserEloRating = UserEloRating::query()
            ->whereNull('objectable_type')
            ->whereNull('objectable_id')
            ->where('scorable_type', User::class)
            ->where('scorable_id', $user->id)
            ->orderBy(
                Event::selectRaw('start_date')
                    ->whereColumn('user_elo_ratings.event_id', 'events.id'),
                'DESC'
            )
            ->first()
            ->elo_rating;

        $ownOldEloRating = UserEloRating::query()
            ->whereNull('objectable_type')
            ->whereNull('objectable_id')
            ->where('scorable_type', Team::class)
            ->where('scorable_id', $team->id)
            ->orderBy(
                Event::selectRaw('start_date')
                    ->whereColumn('user_elo_ratings.event_id', 'events.id'),
                'DESC'
            )
            ->skip(1)
            ->first()
            ->elo_rating;

        $opponentEloRating = UserEloRating::query()
            ->whereNull('objectable_type')
            ->whereNull('objectable_id')
            ->where('scorable_type', Team::class)
            ->where('scorable_id', $opponentTeam->id)
            ->orderBy(
                Event::selectRaw('start_date')
                    ->whereColumn('user_elo_ratings.event_id', 'events.id'),
                'DESC'
            )
            ->first()
            ->elo_rating;

        $opponentUserEloRating = UserEloRating::query()
            ->whereNull('objectable_type')
            ->whereNull('objectable_id')
            ->where('scorable_type', User::class)
            ->where('scorable_id', $opponent->id)
            ->orderBy(
                Event::selectRaw('start_date')
                    ->whereColumn('user_elo_ratings.event_id', 'events.id'),
                'DESC'
            )
            ->first()
            ->elo_rating;

        $opponentOldEloRating = UserEloRating::query()
            ->whereNull('objectable_type')
            ->whereNull('objectable_id')
            ->where('scorable_type', Team::class)
            ->where('scorable_id', $opponentTeam->id)
            ->orderBy(
                Event::selectRaw('start_date')
                    ->whereColumn('user_elo_ratings.event_id', 'events.id'),
                'DESC'
            )
            ->skip(1)
            ->first()
            ->elo_rating;

        $newOwnEloRating = EloSupport::calculateEloRating(+$ownScore, +$opponentScore, +$ownOldEloRating, +$opponentOldEloRating);
        $newOpponentEloRating = EloSupport::calculateEloRating(+$opponentScore, +$ownScore, +$opponentOldEloRating, +$ownOldEloRating);

        $newOwnUserEloRating = EloSupport::calculateTeamUserEloRating(+$ownScore, +$opponentScore, +$ownOldEloRating, collect([+$opponentOldEloRating]));
        $newOpponentUserEloRating = EloSupport::calculateTeamUserEloRating(+$opponentScore, +$ownScore, +$opponentOldEloRating, collect([+$ownOldEloRating]));

        $this->assertEquals(
            $ownEloRating,
            $newOwnEloRating
        );

        $this->assertEquals(
            $opponentEloRating,
            $newOpponentEloRating
        );

        $this->assertEquals(
            $ownUserEloRating,
            $newOwnUserEloRating
        );

        $this->assertEquals(
            $opponentUserEloRating,
            $newOpponentUserEloRating
        );
    }
}
