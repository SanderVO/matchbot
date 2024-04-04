<?php

namespace App\Livewire;

use App\Models\Event;
use App\Models\EventType;
use App\Models\EventTypeSport;
use App\Models\Organization;
use App\Models\Season;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\Rule;
use Livewire\Component;

class CreateEvent extends Component
{
    public Collection $seasons;
    public Collection $sports;
    public Collection $types;
    public Collection $users;

    public $seasonId = null;
    public $sportId = null;
    public $eventTypeId = null;
    public $userIds = [];

    public bool $saveIsSuccessful = false;

    protected $listeners = ['userAdded', 'userRemoved'];

    /**
     * On mount
     * 
     * @author Sander van Ooijen <sandervo+github@proton.me>
     * @version 1.0.0
     */
    public function mount()
    {
        $this->sports = EventTypeSport::query()
            ->orderBy('name')
            ->get();

        $this->seasons = Season::query()
            ->orderBy('name')
            ->get();

        $this->users = User::query()
            ->orderBy('name')
            ->get();
    }

    /**
     * Rules for form
     * 
     * @author Sander van Ooijen <sandervo+github@proton.me>
     * @version 1.0.0
     */
    public function rules()
    {
        return [
            'seasonId' => [
                'required',
                Rule::exists(Season::class, 'id')
            ],
            'sportId' => [
                'required',
                Rule::exists(EventTypeSport::class, 'id')
            ],
            'eventTypeId' => [
                'required',
                Rule::exists(EventType::class, 'id')
            ],
            'userIds' => [
                'required',
                'array',
                'min:2'
            ],
        ];
    }

    /**
     * Save event action
     * 
     * @author Sander van Ooijen <sandervo+github@proton.me>
     * @version 1.0.0
     */
    public function save(): void
    {
        $this->validate();

        Event::create(
            [
                'status' => 0,
                'event_type_id' => $this->eventTypeId,
                'season_id' => $this->seasonId
            ]
        );

        $this->saveIsSuccessful = true;
    }

    /**
     * Get event types based on chosen sport
     * 
     * @author Sander van Ooijen <sandervo+github@proton.me>
     * @version 1.0.0
     */
    public function getEventTypes(): void
    {
        $this->types = EventType::query()
            ->where('event_type_sport_id', $this->sportId)
            ->get();
    }

    /**
     * Add user
     * 
     * @author Sander van Ooijen <sandervo+github@proton.me>
     * @version 1.0.0
     */
    public function userAdded(int $userId)
    {
        $this->userIds[] = $userId;
    }

    /**
     * Remove user
     * 
     * @author Sander van Ooijen <sandervo+github@proton.me>
     * @version 1.0.0
     */
    public function userRemoved(int $userId)
    {
        $this->userIds = array_diff($this->userIds, [$userId]);
    }

    /**
     * Render component
     * 
     * @return View
     * 
     * @author Sander van Ooijen <sandervo+github@proton.me>
     * @version 1.0.0
     */
    public function render()
    {
        return view('livewire.create-event');
    }
}
