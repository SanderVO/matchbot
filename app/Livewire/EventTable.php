<?php

namespace App\Livewire;

use App\Models\Event;
use App\Models\Team;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class EventTable extends Component
{
    use WithPagination;

    #[Url]
    public ?int $teamId;

    #[Url]
    public ?int $userId;

    #[Url]
    public ?string $resultType;

    protected $events = [];
    protected $teams = [];
    protected $users = [];

    /**
     * Load events
     * 
     * @author Sander van Ooijen <sandervo+github@proton.me>
     * @version 1.0.0
     */
    public function loadEvents()
    {
        $this->events = Event::query()
            ->has('teamResults')
            ->when(
                isset($this->teamId),
                function ($query) {
                    $query
                        ->whereHas(
                            'teamResults.team',
                            function ($query) {
                                $query
                                    ->where('id', $this->teamId);
                            }
                        );
                }
            )
            ->when(
                isset($this->userId),
                function ($query) {
                    $query
                        ->whereHas(
                            'teamResults.team.users',
                            function ($query) {
                                $query
                                    ->where('id', $this->userId);
                            }
                        );
                }
            )
            ->when(
                isset($this->resultType),
                function ($query) {
                    $query
                        ->when(
                            $this->resultType === 'maxCrawls',
                            function ($query) {
                                $query
                                    ->whereHas(
                                        'teamResults',
                                        function ($query) {
                                            $query
                                                ->where('crawl_score', 4);
                                        }
                                    );
                            }
                        )
                        ->when(
                            $this->resultType === 'human',
                            function ($query) {
                                $query
                                    ->whereHas(
                                        'teamResults',
                                        function ($query) {
                                            $query
                                                ->where('crawl_score', '>', 0);
                                        },
                                        '=',
                                        2
                                    );
                            }
                        )
                        ->when(
                            $this->resultType === 'overtime',
                            function ($query) {
                                $query
                                    ->whereHas(
                                        'teamResults',
                                        function ($query) {
                                            $query
                                                ->where('score', '>', 10);
                                        }
                                    );
                            }
                        );
                }
            )
            ->with([
                'season',
                'eventType.sport',
                'teamResults.team.users'
            ])
            ->orderBy(
                'created_at',
                'desc'
            )
            ->paginate();
    }

    /**
     * Load teams for dropdown
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
                '>=',
                2
            )
            ->orderBy('name')
            ->get();
    }

    /**
     * Load players for dropdown
     * 
     * @author Sander van Ooijen <sandervo+github@proton.me>
     * @version 1.0.0
     */
    public function loadUsers()
    {
        $this->users = User::query()
            ->where('status', 1)
            ->orderBy('name')
            ->get();
    }

    /**
     * Remove event
     * 
     * @author Sander van Ooijen <sandervo+github@proton.me>
     * @version 1.0.0
     */
    public function destroyEvent(int $eventId)
    {
        Event::destroy($eventId);

        $this->loadEvents();
    }

    /**
     * Load events again on team change
     * 
     * @param int $teamId
     * 
     * @author Sander van Ooijen <sandervo+github@proton.me>
     * @version 1.0.0
     */
    public function onTeamChange(int $teamId)
    {
        $this->userId = null;

        $this->teamId = $teamId !== 0 ? $teamId : null;

        $this->loadEvents();
    }

    /**
     * Load events again on player change
     * 
     * @param int $userId
     * 
     * @author Sander van Ooijen <sandervo+github@proton.me>
     * @version 1.0.0
     */
    public function onUserChange(int $userId)
    {
        $this->teamId = null;

        $this->userId = $userId !== 0 ? $userId : null;

        $this->loadEvents();
    }

    /**
     * Load events again on result type change
     * 
     * @param string $resultType
     * 
     * @author Sander van Ooijen <sandervo+github@proton.me>
     * @version 1.0.0
     */
    public function onResultTypeChange(string $resultType)
    {
        $this->resultType = $resultType !== '' ? $resultType : null;

        $this->loadEvents();
    }

    /**
     * Render component
     * 
     * @author Sander van Ooijen <sandervo+github@proton.me>
     * @version 1.0.0
     */
    public function render()
    {
        $this->loadEvents();
        $this->loadTeams();
        $this->loadUsers();

        return view(
            'livewire.event-table',
            [
                'events' => $this->events,
                'teams' => $this->teams,
                'users' => $this->users
            ]
        );
    }
}
