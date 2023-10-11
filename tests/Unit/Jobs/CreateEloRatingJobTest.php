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
        $season = Season::factory()
            ->create();

        $event = Event::factory()
            ->create([
                'season_id' => $season->id,
                'status' => 1,
                'created_at' => Carbon::now()->subDay(1)
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
        dd(UserEloRating::get()->toArray());
    }
}
