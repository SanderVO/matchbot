<?php

namespace App\Livewire;

use App\Models\Event;
use App\Models\Team;
use App\Models\TeamResult;
use App\Models\TeamResultUser;
use App\Models\User;
use App\Models\UserEloRating;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class LeaderboardTable extends Component
{
    use WithPagination;

    protected $userEloRatings;

    #[Url]
    public string $scorableType = Team::class;

    public $userIsActive = true;
    public $daysBack = null;

    /**
     * Load users
     * 
     * @author Sander van Ooijen <sandervo+github@proton.me>
     * @version 1.0.0
     */
    public function loadLeaderboard()
    {
        $userEloRatings =  UserEloRating::query()
            ->joinSub(
                UserEloRating::selectRaw('uer2.scorable_id, MAX(uer2.event_start_date) AS latest_event_date')
                    ->from('user_elo_ratings AS uer2')
                    ->groupBy('uer2.scorable_id'),
                'latest',
                function ($join) {
                    $join
                        ->on('user_elo_ratings.scorable_id', '=', 'latest.scorable_id')
                        ->on('user_elo_ratings.event_start_date', '=', 'latest.latest_event_date');
                }
            )
            ->whereNull('user_elo_ratings.objectable_type')
            ->whereNull('user_elo_ratings.objectable_id')
            ->where('user_elo_ratings.scorable_type', $this->scorableType)
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
                            [Team::class, User::class],
                            function ($query, $type) {
                                if ($type === Team::class) {
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
                                } else {
                                    $query
                                        ->where('status', 1);
                                }
                            }
                        );
                }
            )
            ->with([
                'event.teamResults',
                'scorable' => function ($query) {
                    $query
                        ->morphWith([
                            Team::class => ['results'],
                            User::class => ['teamResultUsers.teamResult']
                        ]);
                }
            ])
            ->orderBy('user_elo_ratings.elo_rating', 'DESC')
            ->paginate()
            ->setPath(route('leaderboard.index'));

        $userEloRatings->through(function (UserEloRating $userEloRating) {
            $userEloRating->played = 0;
            $userEloRating->wins = 0;
            $userEloRating->losses = 0;

            $streak = [];
            $currentStreak = 0;
            $currentStreakType = null;
            $currentStreakFlag = false;

            $userEloRating->streak = [];

            Event::query()
                ->where('status', 1)
                ->when(
                    $this->scorableType === Team::class,
                    function ($query) use ($userEloRating) {
                        $query
                            ->whereHas(
                                'teamResults',
                                function ($query) use ($userEloRating) {
                                    $query
                                        ->where('team_id', $userEloRating->scorable_id);
                                }
                            )
                            ->with([
                                'teamResults.team'
                            ]);
                    },
                    function ($query) use ($userEloRating) {
                        $query
                            ->whereHas(
                                'teamResults.teamResultUsers',
                                function ($query) use ($userEloRating) {
                                    $query
                                        ->where('user_id', $userEloRating->scorable_id);
                                }
                            )
                            ->with([
                                'teamResults.team.users',
                            ]);
                    }
                )
                ->orderBy('start_date', 'DESC')
                ->get()
                ->each(function (Event $event) use (&$userEloRating, &$streak, &$currentStreak, &$currentStreakType, &$currentStreakFlag, &$defEvents) {
                    $userEloRating->played++;

                    $teamScore = $event
                        ->teamResults
                        ->when(
                            $this->scorableType === Team::class,
                            function (Collection $teamResults) use ($userEloRating) {
                                return $teamResults
                                    ->where('team_id', $userEloRating->scorable_id);
                            },
                            function (Collection $teamResults) use ($userEloRating) {
                                return $teamResults
                                    ->filter(function (TeamResult $teamResult) use ($userEloRating) {
                                        return $teamResult
                                            ->team
                                            ->users
                                            ->where('id',  $userEloRating->scorable_id)
                                            ->first();
                                    });
                            },
                        )
                        ->first()
                        ->score;

                    $oppScore = $event
                        ->teamResults
                        ->when(
                            $this->scorableType === Team::class,
                            function (Collection $teamResults) use ($event, $userEloRating) {
                                $oppId = $event
                                    ->teamResults
                                    ->where('team_id', '!=', $userEloRating->scorable_id)
                                    ->first()
                                    ->team_id;

                                return $teamResults
                                    ->where('team_id', $oppId);
                            },
                            function (Collection $teamResults) use ($userEloRating) {
                                return $teamResults
                                    ->filter(function (TeamResult $teamResult) use ($userEloRating) {
                                        return $teamResult
                                            ->team
                                            ->users
                                            ->where('id', '!=', $userEloRating->scorable_id)
                                            ->count() === 2;
                                    });
                            },
                        )
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
                        $userEloRating->losses = $userEloRating->losses + 1;

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

            if ($userEloRating->wins > 0) {
                $userEloRating->win_lose_percentage = round(($userEloRating->wins / ($userEloRating->wins + $userEloRating->losses) * 100), 0);
            } else {
                $userEloRating->win_lose_percentage = 0;
            }

            if ($this->scorableType === Team::class) {
                $userEloRating->total_crawl_score = $userEloRating->scorable->results->sum('crawl_score');
                $userEloRating->total_score = $userEloRating->scorable->results->sum('score');
                $userEloRating->avg_score = round($userEloRating->scorable->results->sum('score') / $userEloRating->scorable->results->count(), 2);
                $userEloRating->avg_crawl = round($userEloRating->scorable->results->sum('crawl_score') / $userEloRating->scorable->results->count(), 2);

                $userEloRating->scorable->original_name = $userEloRating->scorable->users->map(function ($user) {
                    return $user->name;
                })->implode(' + ');
            } else {
                $userEloRating->total_crawl_score = $userEloRating->scorable->teamResultUsers->sum('teamResult.crawl_score');
                $userEloRating->total_score = $userEloRating->scorable->teamResultUsers->sum('teamResult.score');
                $userEloRating->avg_score = round($userEloRating->scorable->teamResultUsers->sum('teamResult.score') / $userEloRating->scorable->teamResultUsers->count('teamResult.score'), 2);
                $userEloRating->avg_crawl = round($userEloRating->scorable->teamResultUsers->sum('teamResult.crawl_score') / $userEloRating->scorable->teamResultUsers->count('teamResult.crawl_score'), 2);

                $userEloRating->scorable->original_name = $userEloRating->scorable->name;
            }

            return $userEloRating;
        });

        $this->userEloRatings = $userEloRatings;
    }

    /**
     * Load leaderboard again on scorable type change
     * 
     * @param string $scorableType
     * 
     * @author Sander van Ooijen <sandervo+github@proton.me>
     * @version 1.0.0
     */
    public function onScorableTypeChange(string $scorableType)
    {
        $this->scorableType = $scorableType;

        $this->loadLeaderboard();
    }

    /**
     * Render component
     * 
     * @author Sander van Ooijen <sandervo+github@proton.me>
     * @version 1.0.0
     */
    public function render()
    {
        $this->loadLeaderboard();

        return view(
            'livewire.leaderboard-table',
            [
                'userEloRatings' => $this->userEloRatings
            ]
        );
    }
}