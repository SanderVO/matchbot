<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\TeamResult;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MoveTeamResultCommentsToEvent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'move-team-result-comments-to-event';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Move team result comments to event table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        TeamResult::query()
            ->where('comment', '!=', '')
            ->whereHas(
                'event',
                function ($query) {
                    $query
                        ->whereNull('comment');
                }
            )
            ->groupBy('event_id')
            ->select([
                'event_id',
                DB::raw('MAX(comment) as comment')
            ])
            ->get()
            ->each(function ($teamResult) {
                Event::query()
                    ->where('id', $teamResult->event_id)
                    ->update([
                        'comment' => $teamResult->comment
                    ]);
            });
    }
}
