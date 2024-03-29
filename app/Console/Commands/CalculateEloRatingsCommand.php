<?php

namespace App\Console\Commands;

use App\Jobs\CalculateEloRatingJob;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

class CalculateEloRatingsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calculate-elo-ratings {days?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate ELO ratings of events of specified days back';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->argument('days');

        Event::query()
            ->where('status', 1)
            ->when(
                isset($days),
                function ($query) use ($days) {
                    $beginDate = Carbon::now()->subDays($days);

                    $query
                        ->where('start_date', '>=', $beginDate);
                }
            )
            ->orderBy('start_date')
            ->chunk(500, function (Collection $events) {
                $events
                    ->each(function (Event $event) {
                        CalculateEloRatingJob::dispatch($event->id);
                    });
            });
    }
}
