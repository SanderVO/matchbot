<?php

namespace App\Jobs;

use App\Models\Event;
use App\Models\EventType;
use App\Models\EventTypeSport;
use App\Models\TeamResultUser;
use App\Models\UserEloRating;
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
     * Get the unique ID for the job.
     */
    public function uniqueId(): string
    {
        return $this->event->id;
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

        $eloRating = Config::get('elo.defaultRating');
        $ratingIncrementer = Config::get('elo.incrementalScore');

        $userEventDataArray = collect([]);

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

        $teamResultUsers->each(function (TeamResultUser $teamResultUser) use ($eventTypes, $eloRating, $ratingIncrementer, $userEventDataArray) {
            foreach ($eventTypes as $eventType) {
                $objectableType = $eventType === 'type' ? EventType::class : ($eventType === 'sport' ? EventTypeSport::class : null);
                $objectableId = $eventType === 'type' ? $this->event->eventType->id : ($eventType === 'sport' ? $this->event->eventType->sport->id : null);

                $objectableTypeQuery = isset($objectableType) ? "= '{$objectableType}'" : "IS NULL";
                $objectableIdQuery = isset($objectableId) ? "= '{$objectableId}'" : "IS NULL";

                $totalEloRating = UserEloRating::query()
                    ->whereHas(
                        'event',
                        function ($query) use ($teamResultUser) {
                            $query
                                ->where('start_date', '<', $this->event->start_date)
                                ->whereHas(
                                    'teamResults.teamResultUsers',
                                    function ($query) use ($teamResultUser) {
                                        $query
                                            ->where('user_id', $teamResultUser->user_id);
                                    }
                                );
                        }
                    )
                    ->where('user_id', '!=', $teamResultUser->user_id)
                    ->whereRaw("objectable_type {$objectableTypeQuery}")
                    ->whereRaw("objectable_id {$objectableIdQuery}")
                    ->selectRaw("CONVERT(IFNULL(SUM(elo_rating), {$eloRating}), UNSIGNED) AS total")
                    ->first()
                    ->total ?? $eloRating;

                $winAmount = Event::query()
                    ->join(
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
                    ->whereRaw("
                        opponentTeam.score < {$teamResultUser->teamResult->score}
                    ")
                    ->count();

                $loseAmount = Event::query()
                    ->join(
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
                    ->whereRaw("
                        opponentTeam.score > {$teamResultUser->teamResult->score}
                    ")
                    ->count();

                $userEventData = [];

                $userEventData['elo_rating'] = ($totalEloRating + $ratingIncrementer * ($winAmount - $loseAmount)) / ($winAmount + $loseAmount);
                $userEventData['user_id'] = $teamResultUser->user_id;
                $userEventData['event_id'] = $teamResultUser->teamResult->event_id;
                $userEventData['objectable_type'] = $eventType === 'type' ? EventType::class : ($eventType === 'sport' ? EventTypeSport::class : null);
                $userEventData['objectable_id'] = $eventType === 'type' ? $this->event->eventType->id : ($eventType === 'sport' ? $this->event->eventType->sport->id : null);

                $userEventDataArray
                    ->push($userEventData);
            }
        });

        $userEventDataArray
            ->each(function ($userEventData) {
                UserEloRating::updateOrCreate(
                    [
                        'user_id' => $userEventData['user_id'],
                        'event_id' => $userEventData['event_id'],
                        'objectable_type' => $userEventData['objectable_type'],
                        'objectable_id' => $userEventData['objectable_id']
                    ],
                    [
                        'elo_rating' => $userEventData['elo_rating']
                    ]
                );
            });

        DB::commit();
    }
}
