<?php

namespace App\Observers;

use App\Jobs\CalculateEloRatingJob;
use App\Models\Event;
use Illuminate\Database\Eloquent\Collection;

class EventObserver
{
    /**
     * On saved event listener
     *
     * @param Event $event
     * 
     * @author Sander van Ooijen <sandervo+github@proton.me>
     * @version 1.0.0
     */
    public function saved(Event $event)
    {
        if ($event->status === 1) {
            Event::query()
                ->where('status', 1)
                ->where('start_date', '>=', $event->start_date)
                ->orderBy('start_date')
                ->chunk(500, function (Collection $events) {
                    $events
                        ->each(function (Event $event) {
                            CalculateEloRatingJob::dispatch($event->id);
                        });
                });
        }
    }
}
