<?php

namespace App\Jobs;

use App\Models\Event;
use App\Models\EventParticipant;
use App\Models\EventType;
use App\Models\EventTypeSport;
use App\Models\TeamResultUser;
use App\Models\User;
use App\Models\UserEloRating;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use UserEloRatingTypeEnum;

class CalculateEloRatingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Event $event;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Event $event)
    {
        $this->event = $event;
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
        DB::beginTransaction();

        $eventTypes = [
            'all',
            'type',
            'sport'
        ];

        $teamResultUsers = TeamResultUser::query()
            ->whereHas(
                'teamResult',
                function ($query) {
                    $query
                        ->where('event_id', $this->event->id);
                }
            )
            ->with([
                'user',
                'teamResult' => function ($query) {
                    $query
                        ->where('event_id', $this->event->id);
                }
            ])
            ->get();

        $teamResultUsers->each(function (TeamResultUser $teamResultUser) use ($eventTypes) {
            foreach ($eventTypes as $eventType) {
                $userEventData = Event::query()
                    ->leftJoin(
                        'team_results AS opponentTeam',
                        function ($query) use ($teamResultUser) {
                            $query
                                ->on('opponentTeam.event_id', '=', 'events.id')
                                ->where('opponentTeam.id', '!=', $teamResultUser->team_result_id);
                        }
                    )
                    ->where(
                        'events.created_at',
                        '<=',
                        $this->event->created_at
                    )
                    ->when(
                        $eventType === 'type',
                        function ($query) {
                            $query
                                ->where('events.event_type_id', $this->event->eventType->id);
                        }
                    )
                    ->when(
                        $eventType === 'sport',
                        function ($query) {
                            $query
                                ->whereHas(
                                    'eventType',
                                    function ($query) {
                                        $query
                                            ->where('event_type_sport_id', $this->event->eventType->sport->id);
                                    }
                                );
                        }
                    )
                    ->whereHas(
                        'teamResults.teamResultUsers',
                        function ($query) use ($teamResultUser) {
                            $query
                                ->where('user_id', $teamResultUser->user_id);
                        }
                    )
                    ->groupBy(
                        'events.id'
                    )
                    ->selectRaw("
                        COUNT(
                            CASE 
                                WHEN {$teamResultUser->teamResult->score} > opponentTeam.score THEN 1
                                ELSE 0
                            END
                        ) - COUNT(
                            CASE 
                                WHEN {$teamResultUser->teamResult->score} < opponentTeam.score THEN 1
                                ELSE 0
                            END
                        ) AS total_won_lost,
                        COUNT(events.id) AS total_events
                    ")
                    ->first();

                $opponentEloRating = optional(
                    UserEloRating::query()
                        ->when(
                            $eventType === 'type',
                            function ($query) {
                                $query
                                    ->where('objectable_type', EventType::class)
                                    ->where('objectable_id', $this->event->eventType->id);
                            }
                        )
                        ->when(
                            $eventType === 'sport',
                            function ($query) {
                                $query
                                    ->where('objectable_type', EventTypeSport::class)
                                    ->where('objectable_id', $this->event->eventType->sport->id);
                            }
                        )
                        ->whereIn(
                            'user_id',
                            TeamResultUser::query()
                                ->where('user_id', '!=', $teamResultUser->user_id)
                                ->whereHas(
                                    'teamResult',
                                    function ($query) use ($teamResultUser) {
                                        $query
                                            ->where('event_id', $teamResultUser->teamResult->event_id);
                                    }
                                )
                                ->get()
                                ->pluck('user_id')
                                ->toArray()
                        )
                        ->selectRaw('IF(SUM(elo_rating) = 0, 1500, (SUM(elo_rating)/ COUNT(id))) AS avg_elo_rating')
                        ->first()
                )->avg_elo_rating ?? 1500;

                dd(
                    $opponentEloRating,
                    $userEventData->toArray(),
                    ($opponentEloRating + 400 * $userEventData->total_won_lost) / $userEventData->total_events
                );

                UserEloRating::updateOrCreate(
                    [
                        'event_id' => $teamResultUser->teamResult->event_id,
                        'objectable_type' => $eventType === 'type' ? EventType::class : ($eventType === 'sport' ? EventTypeSport::class : null),
                        'objectable_id' => $eventType === 'type' ? $this->event->eventType->id : ($eventType === 'sport' ? $this->event->eventType->sport->id : null)
                    ],
                    [
                        'user_id' => $teamResultUser->user_id,
                        'elo_rating' => ($opponentEloRating + 400 * $userEventData->total_won_lost) / $userEventData->total_events
                    ]
                );
            }
        });

        DB::commit();
    }
}
