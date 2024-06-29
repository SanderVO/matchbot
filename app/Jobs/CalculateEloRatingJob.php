<?php

namespace App\Jobs;

use App\Models\Event;
use App\Models\EventType;
use App\Models\EventTypeSport;
use App\Models\Team;
use App\Models\TeamResult;
use App\Models\TeamResultUser;
use App\Models\User;
use App\Models\UserEloRating;
use App\Support\EloSupport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class CalculateEloRatingJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $eventId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $eventId)
    {
        $this->eventId = $eventId;
    }

    /**
     * Get the unique ID for the job.
     */
    public function uniqueId(): string
    {
        return $this->eventId;
    }

    /**
     * Execute the job.
     * 
     * Uses the official ELO rating formula: 
     * https://en.wikipedia.org/wiki/Elo_rating_system
     *
     * @return void
     */
    public function handle()
    {
        $event = Event::find($this->eventId);

        $eloConfig = Config::get('elo');

        $userEventDataArray = collect([]);

        $eventTypes = [
            'all',
            'type',
            'sport'
        ];

        $teamResults = TeamResult::query()
            ->where('event_id', $event->id)
            ->with([
                'teamResultUsers.user'
            ])
            ->get();

        $teamResults->each(function (TeamResult $teamResult) use ($event, $eventTypes, $eloConfig, $userEventDataArray, $teamResults) {
            foreach ($eventTypes as $eventType) {
                $objectableType = $eventType === 'type' ? EventType::class : ($eventType === 'sport' ? EventTypeSport::class : null);
                $objectableId = $eventType === 'type' ? $event->eventType->id : ($eventType === 'sport' ? $event->eventType->sport->id : null);

                $opponentResult = TeamResult::query()
                    ->where('event_id', $event->id)
                    ->where('id', '!=', $teamResult->id)
                    ->with([
                        'teamResultUsers.user'
                    ])
                    ->first();

                // Users ELO rating if available
                $teamResult
                    ->teamResultUsers
                    ->each(function (TeamResultUser $teamResultUser) use ($event, &$userEventDataArray, $eloConfig, $objectableType, $objectableId, $teamResult, $opponentResult) {
                        $opponentEloRatings = collect([]);

                        $userEloRating = optional(UserEloRating::query()
                            ->where('objectable_type', $objectableType)
                            ->where('objectable_id', $objectableId)
                            ->where('scorable_type', User::class)
                            ->where('scorable_id', $teamResultUser->user_id)
                            ->whereHas(
                                'event',
                                function ($query) use ($event) {
                                    $query
                                        ->where('start_date', '<=', $event->start_date);
                                }
                            )
                            ->orderBy(
                                Event::selectRaw('start_date')
                                    ->where('status', 1)
                                    ->whereColumn('user_elo_ratings.event_id', 'events.id'),
                                'DESC'
                            )
                            ->first())
                            ->elo_rating ?? $eloConfig['defaultRating'];

                        $opponentResult
                            ->teamResultUsers
                            ->each(function (TeamResultUser $teamResultUser) use ($event, &$opponentEloRatings, $eloConfig, $objectableType, $objectableId) {
                                $opponentEloRatings->push(
                                    optional(UserEloRating::query()
                                        ->where('objectable_type', $objectableType)
                                        ->where('objectable_id', $objectableId)
                                        ->where('scorable_type', User::class)
                                        ->where('scorable_id', '!=', $teamResultUser->user_id)
                                        ->whereHas(
                                            'event',
                                            function ($query) use ($event) {
                                                $query
                                                    ->where('start_date', '<=', $event->start_date);
                                            }
                                        )
                                        ->orderBy(
                                            Event::selectRaw('start_date')
                                                ->where('status', 1)
                                                ->whereColumn('user_elo_ratings.event_id', 'events.id'),
                                            'DESC'
                                        )
                                        ->first())
                                        ->elo_rating ?? $eloConfig['defaultRating']
                                );
                            });

                        $newEloRating = EloSupport::calculateTeamUserEloRating(+$teamResult->score, +$opponentResult->score, +$userEloRating, $opponentEloRatings);

                        $userEventData = [];

                        $userEventData['elo_rating'] = $newEloRating;
                        $userEventData['elo_rating_difference'] = +$newEloRating - +$userEloRating;
                        $userEventData['event_id'] = $teamResult->event_id;
                        $userEventData['scorable_type'] = User::class;
                        $userEventData['scorable_id'] = $teamResultUser->user_id;
                        $userEventData['objectable_type'] = $objectableType;
                        $userEventData['objectable_id'] = $objectableId;

                        $userEventDataArray
                            ->push($userEventData);
                    });



                // Team ELO rating 
                $lastEloRating = optional(UserEloRating::query()
                    ->where('objectable_type', $objectableType)
                    ->where('objectable_id', $objectableId)
                    ->where('scorable_type', Team::class)
                    ->where('scorable_id', $teamResult->team_id)
                    ->whereHas(
                        'event',
                        function ($query) use ($event) {
                            $query
                                ->where('start_date', '<=', $event->start_date);
                        }
                    )
                    ->orderBy(
                        Event::selectRaw('start_date')
                            ->where('status', 1)
                            ->whereColumn('user_elo_ratings.event_id', 'events.id'),
                        'DESC'
                    )
                    ->first())
                    ->elo_rating ?? $eloConfig['defaultRating'];

                $opponentId = $teamResults
                    ->where('team_id', '!=', $teamResult->team_id)
                    ->first()
                    ->team_id;

                $opponentEloRating = optional(UserEloRating::query()
                    ->where('objectable_type', $objectableType)
                    ->where('objectable_id', $objectableId)
                    ->where('scorable_type', Team::class)
                    ->where('scorable_id', $opponentId)
                    ->whereHas(
                        'event',
                        function ($query) use ($event) {
                            $query
                                ->where('start_date', '<=', $event->start_date);
                        }
                    )
                    ->orderBy(
                        Event::selectRaw('start_date')
                            ->where('status', 1)
                            ->whereColumn('user_elo_ratings.event_id', 'events.id'),
                        'DESC'
                    )
                    ->first())
                    ->elo_rating ?? $eloConfig['defaultRating'];

                $newEloRating = EloSupport::calculateTeamEloRating(+$teamResult->score, +$opponentResult->score, +$lastEloRating, +$opponentEloRating);

                $userEventData = [];

                $userEventData['elo_rating'] = $newEloRating;
                $userEventData['elo_rating_difference'] = +$newEloRating - +$lastEloRating;
                $userEventData['event_id'] = $teamResult->event_id;
                $userEventData['scorable_type'] = Team::class;
                $userEventData['scorable_id'] = $teamResult->team_id;
                $userEventData['objectable_type'] = $objectableType;
                $userEventData['objectable_id'] = $objectableId;

                $userEventDataArray
                    ->push($userEventData);
            }
        });

        DB::beginTransaction();

        $userEventDataArray
            ->each(function ($userEventData) {
                UserEloRating::updateOrCreate(
                    [
                        'event_id' => $userEventData['event_id'],
                        'objectable_type' => $userEventData['objectable_type'],
                        'objectable_id' => $userEventData['objectable_id'],
                        'scorable_type' => $userEventData['scorable_type'],
                        'scorable_id' => $userEventData['scorable_id']
                    ],
                    [
                        'elo_rating' => $userEventData['elo_rating'],
                        'elo_rating_difference' => $userEventData['elo_rating_difference']
                    ]
                );
            });

        DB::commit();
    }
}
