<?php

namespace App\Console\Commands;

use App\Models\TeamResult;
use App\Models\TeamResultUser;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

class GenerateTeamResultUsersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate-team-result-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate team result users based on existing team results';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        TeamResult::query()
            ->doesntHave('teamResultUsers')
            ->with([
                'team.users'
            ])
            ->chunk(500, function (Collection $teamResults) {
                $teamResults
                    ->each(function (TeamResult $teamResult) {
                        $teamResult->team->users->each(function (User $user) use ($teamResult) {
                            TeamResultUser::updateOrCreate(
                                [
                                    'user_id' => $user->id,
                                    'team_result_id' => $teamResult->id
                                ],
                                [
                                    'score' => 0,
                                    'crawl_score' => 0
                                ]
                            );
                        });
                    });
            });
    }
}
