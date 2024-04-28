<?php

namespace App\Livewire;

use App\Models\Team;
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
        $this->userEloRatings =  UserEloRating::query()
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
                'scorable'
            ])
            ->orderBy('elo_rating', 'DESC')
            ->paginate()
            ->setPath(route('leaderboard.index'));
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
