<?php

namespace App\Livewire;

use App\Models\Event;
use App\Models\Team;
use App\Models\TeamResult;
use App\Models\User;
use App\Models\UserEloRating;
use Livewire\Component;
use Livewire\WithPagination;

class LeaderboardTable extends Component
{
    use WithPagination;

    protected $userEloRatings;

    public $userIsActive = true;

    /**
     * Mount component
     * 
     * @author Sander van Ooijen <sandervo+github@proton.me>
     * @version 1.0.0
     */
    public function mount()
    {
        $this->loadLeaderboard();
    }

    /**
     * Load users
     * 
     * @author Sander van Ooijen <sandervo+github@proton.me>
     * @version 1.0.0
     */
    public function loadLeaderboard()
    {
        $userEloRatings =  UserEloRating::query()
            ->whereNull('objectable_type')
            ->whereNull('objectable_id')
            ->where('scorable_type', Team::class)
            ->whereIn('id', function ($query) {
                $query
                    ->selectRaw('MAX(id)')
                    ->from('user_elo_ratings')
                    ->whereNull('objectable_type')
                    ->whereNull('objectable_id')
                    ->where('scorable_type', Team::class)
                    ->groupBy('scorable_id');
            })
            ->when(
                $this->userIsActive,
                function ($query) {
                    $query
                        ->whereHasMorph(
                            'scorable',
                            Team::class,
                            function ($query) {
                                $query
                                    ->whereHas(
                                        'users',
                                        function ($query) {
                                            $query
                                                ->where('status', 1);
                                        },
                                        '=',
                                        2
                                    );
                            }
                        );
                }
            )
            ->with([
                'scorable',
                'event.teamResults'
            ])
            ->orderBy('elo_rating', 'DESC')
            ->paginate()
            ->setPath(route('leaderboard.index'));

        $userEloRatings->through(function (UserEloRating $userEloRating) {
            $userEloRating->wins = 0;
            $userEloRating->losses = 0;

            Event::query()
                ->where('status', 1)
                ->whereHas(
                    'teamResults',
                    function ($query) use ($userEloRating) {
                        $query
                            ->where('team_id', $userEloRating->scorable_id);
                    }
                )
                ->with([
                    'teamResults'
                ])
                ->get()
                ->each(function (Event $event) use (&$userEloRating) {
                    $teamScore = $event
                        ->teamResults
                        ->where('team_id', $userEloRating->scorable_id)
                        ->first()
                        ->score;

                    $oppScore = $event
                        ->teamResults
                        ->where('team_id', '!=', $userEloRating->scorable_id)
                        ->first()
                        ->score;

                    if ($teamScore > $oppScore) {
                        $userEloRating->wins++;
                    } else {
                        $userEloRating->losses++;
                    }
                });

            $userEloRating->win_lose_percentage = round(($userEloRating->wins / ($userEloRating->wins + $userEloRating->losses) * 100), 0);

            return $userEloRating;
        });

        $this->userEloRatings = $userEloRatings;
    }

    /**
     * Render component
     * 
     * @author Sander van Ooijen <sandervo+github@proton.me>
     * @version 1.0.0
     */
    public function render()
    {
        return view(
            'livewire.leaderboard-table',
            [
                'userEloRatings' => $this->userEloRatings
            ]
        );
    }
}
