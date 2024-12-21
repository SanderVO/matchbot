<?php

namespace App\Console\Commands;

use App\Models\UserEloRating;
use Illuminate\Console\Command;

class MigrateEventStartDatesForUserEloRatings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-event-start-dates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds event start dates to user elo ratings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        UserEloRating::query()
            ->with('event')
            ->chunk(500, function ($eloRatings) {
                $eloRatings->each(function ($eloRating) {
                    $eloRating->event_start_date = $eloRating->event->start_date;
                    $eloRating->save();
                });
            });
    }
}