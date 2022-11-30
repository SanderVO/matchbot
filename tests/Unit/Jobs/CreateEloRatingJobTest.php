<?php

namespace Tests\Unit\Jobs;

use App\Jobs\CalculateEloRatingJob;
use App\Models\Event;
use App\Models\EventParticipant;
use App\Models\User;
use PHPUnit\Framework\TestCase;

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
        $event = Event::factory()
            ->create([
                'status' => 1
            ]);

        $user = User::factory()
            ->create();

        $opponent = User::factory()
            ->create();

        EventParticipant::factory()
            ->create([
                'event_id' => $event->id,
                'score' => 10,
                'user_id' => $user->id
            ]);

        EventParticipant::factory()
            ->create([
                'event_id' => $event->id,
                'score' => 5,
                'user_id' => $opponent->id
            ]);

        // Act
        $job = new CalculateEloRatingJob($event);
        $job->handle();

        // Assert

    }
}
