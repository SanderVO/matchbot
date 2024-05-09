<?php

namespace App\Livewire;

use App\Models\Team;
use Livewire\Component;
use Livewire\WithPagination;

class TeamTable extends Component
{
    use WithPagination;

    protected $listeners = [
        'refreshTeams' => 'loadTeams'
    ];

    protected $teams;

    /**
     * On mount, load teams
     * 
     * @author Sander van Ooijen <sandervo+github@proton.me>
     * @version 1.0.0
     */
    public function mount()
    {
        $this->loadTeams();
    }

    /**
     * Load teams for the table with extra data
     * 
     * @author Sander van Ooijen <sandervo+github@proton.me>
     * @version 1.0.0
     */
    public function loadTeams()
    {
        $this->teams = Team::query()
            ->whereHas(
                'users',
                function ($query) {
                    $query
                        ->where('status', 1);
                },
                '=',
                2
            )
            ->withCount([
                'users'
            ])
            ->orderBy(
                'name',
                'asc'
            )
            ->paginate()
            ->setPath(route('teams.index'));
    }

    /**
     * Update the name of the team
     * 
     * @param int $teamId
     * @param string $name
     * 
     * @author Sander van Ooijen <sandervo+github@proton.me>
     * @version 1.0.0
     */
    public function updateTeam(int $teamId, string $name)
    {
        Team::query()
            ->where('id', $teamId)
            ->update([
                'name' => $name
            ]);

        $this->loadTeams();
    }

    /**
     * Render team table view
     * 
     * @author Sander van Ooijen <sandervo+github@proton.me>
     * @version 1.0.0
     */
    public function render()
    {
        return view(
            'livewire.team-table',
            [
                'teams' => $this->teams
            ]
        );
    }
}
