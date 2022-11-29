<?php

namespace App\Jobs;

use App\Models\Event;
use App\Models\EventParticipant;
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

    public User $user;
    public Event $event;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, Event $event)
    {
        $this->user = $user;
        $this->event = $event;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        DB::beginTransaction();

        $this->event->eventParticipants->each(function (EventParticipant $eventParticipant) {
            $userEloRating = optional(
                UserEloRating::query()
                    ->where('user_id', $eventParticipant->user_id)
                    ->where('type', UserEloRatingTypeEnum::NORMAL)
                    ->orderBy('created_at', 'DESC')
                    ->first()
            )->avg_elo_rating ?? 1500;

            $opponentUserIds = EventParticipant::query()
                ->where('event_id', $eventParticipant->event_id)
                ->where('number', '!=', $eventParticipant->number)
                ->get()
                ->pluck('user_id')
                ->toArray();

            $opponentEloRating = optional(
                UserEloRating::query()
                    ->whereIn('user_id', $opponentUserIds)
                    ->where('type', UserEloRatingTypeEnum::NORMAL)
                    ->groupByRaw('MAX(created_at)')
                    ->selectRaw('SUM(elo_rating) / COUNT(id) AS avg_elo_rating')
                    ->first()
            )->avg_elo_rating ?? 1500;

            UserEloRating::create([
                'type' => UserEloRatingTypeEnum::NORMAL,
                'user_id' => $eventParticipant->user_id,
                'event_id' => $eventParticipant->event_id,
                'elo_rating' => ($opponentEloRating + 400 * 1) / 2
            ]);
        });

        DB::commit();
    }
}
