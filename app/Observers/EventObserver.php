<?php

namespace App\Observers;

use App\Jobs\CalculateEloRatingJob;
use App\Models\Event;

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
        CalculateEloRatingJob::dispatch($event->id);
    }
}
