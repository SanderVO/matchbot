<?php

namespace App\Observers;

use App\Jobs\CalculateEloRatingJob;
use App\Models\Event;
use App\Models\TeamResult;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
use Illuminate\Database\Eloquent\Collection;

class TeamResultObserver implements ShouldHandleEventsAfterCommit
{
    /**
     * On saved team result listener
     *
     * @param TeamResult $teamResult
     * 
     * @author Sander van Ooijen <sandervo+github@proton.me>
     * @version 1.0.0
     */
    public function saved(TeamResult $teamResult)
    {
        if ($teamResult->event->status === 0) {
            return;
        }

        Event::query()
            ->where('status', 1)
            ->where('start_date', '>=', $teamResult->event->start_date)
            ->orderBy('start_date')
            ->chunk(500, function (Collection $events) {
                $events
                    ->each(function (Event $event) {
                        CalculateEloRatingJob::dispatch($event->id);
                    });
            });
    }
}
