<?php

namespace App\Livewire;

use App\Models\Event;
use App\Models\Team;
use App\Models\UserEloRating;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class LeaderboardTable extends Component
{
    use WithPagination;

    protected $userEloRatings;

    public $userIsActive = true;
    public $daysBack = null;

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
                isset($this->daysBack),
                function ($query) {
                    $startDate = Carbon::now()->subDays($this->daysBack);

                    $query
                        ->whereHas(
                            'event',
                            function ($query) use ($startDate) {
                                $query
                                    ->where('start_date', '>', $startDate->format('Y-m-d'));
                            }
                        );
                }
            )
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
                'event.teamResults',
                'scorable' => function ($query) {
                    $query
                        ->morphWith([
                            Team::class => ['results']
                        ]);
                }
            ])
            ->orderBy('elo_rating', 'DESC')
            ->paginate()
            ->setPath(route('leaderboard.index'));

        $userEloRatings->through(function (UserEloRating $userEloRating) {
            $userEloRating->wins = 0;
            $userEloRating->losses = 0;

            $streak = [];
            $currentStreak = 0;
            $currentStreakType = null;
            $currentStreakFlag = false;

            $userEloRating->streak = [];

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
                ->orderBy('start_date', 'DESC')
                ->get()
                ->each(function (Event $event) use (&$userEloRating, &$streak, &$currentStreak, &$currentStreakType, &$currentStreakFlag) {
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

                        if (count($streak) < 5) {
                            $streak[] = 'W';
                        }

                        if (!isset($currentStreakType)) {
                            $currentStreakType = 'W';
                        }

                        if (!$currentStreakFlag) {
                            if ($currentStreakType === 'W') {
                                $currentStreak += 1;
                            } else {
                                $currentStreakFlag = true;
                            }
                        }
                    } else {
                        $userEloRating->losses++;

                        if (count($streak) < 5) {
                            $streak[] = 'L';
                        }

                        if (!isset($currentStreakType)) {
                            $currentStreakType = 'L';
                        }

                        if (!$currentStreakFlag) {
                            if ($currentStreakType === 'L') {
                                $currentStreak += 1;
                            } else {
                                $currentStreakFlag = true;
                            }
                        }
                    }
                });

            $userEloRating->streak = $streak;
            $userEloRating->current_streak = $currentStreak;
            $userEloRating->current_streak_type = $currentStreakType;

            $userEloRating->win_lose_percentage = round(($userEloRating->wins / ($userEloRating->wins + $userEloRating->losses) * 100), 0);

            $userEloRating->total_crawl_score = $userEloRating->scorable->results->sum('crawl_score');
            $userEloRating->total_score = $userEloRating->scorable->results->sum('score');
            $userEloRating->avg_score = round($userEloRating->scorable->results->sum('score') / $userEloRating->scorable->results->count('score'), 1);

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
