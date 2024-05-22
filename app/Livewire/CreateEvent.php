<?php

namespace App\Livewire;

use App\Models\Event;
use App\Models\EventType;
use App\Models\EventTypeSport;
use App\Models\Organization;
use App\Models\Season;
use App\Models\Team;
use App\Models\TeamResult;
use App\Models\TeamResultUser;
use App\Models\User;
use Carbon\Carbon;
use Closure;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
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

        $this->sportId = $this->sports[0]->id;

        $this->seasons = Season::query()
            ->orderBy('name')
            ->get();

        $this->seasonId = $this->seasons[0]->id;

        $this->users = User::query()
            ->where('status', 1)
            ->orderBy('name')
            ->get();

        $this->getEventTypes();
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
                function (string $attribute, mixed $value, Closure $fail) {
                    $eventType = EventType::find($this->eventTypeId);

                    if (!isset($eventType)) {
                        $fail("No event type selected");
                        return;
                    }

                    if (
                        $eventType->min_players > count($value) ||
                        (count($value) % $eventType->min_teams !== 0 && $eventType->min_teams !== $eventType->min_teams) ||
                        (isset($eventType->max_players) && $eventType->max_players < count($value))
                    ) {
                        $fail("Incorrect amount of players selected");
                    }
                },
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

        $eventType = EventType::find($this->eventTypeId);

        DB::beginTransaction();

        $event = Event::create(
            [
                'status' => 0,
                'event_type_id' => $this->eventTypeId,
                'season_id' => $this->seasonId,
                'start_date' => Carbon::now()
            ]
        );

        $userIds = $this->userIds;

        shuffle($userIds);

        if ($eventType->min_teams === $eventType->max_teams) {
            $teamAmount = $eventType->min_teams;
            $userTeamAmount = $eventType->min_players / $teamAmount;
        } else {
            $teamAmount = count($this->userIds) / $eventType->min_teams;
            $userTeamAmount = count($this->userIds) / $teamAmount;
        }

        for ($counter = 0; $counter < $teamAmount; $counter++) {
            if (count($userIds) > $teamAmount) {
                $teamIds = [];

                for ($keyCounter = 0; $keyCounter < $userTeamAmount; $keyCounter++) {
                    $randomKey = random_int(0, count($userIds) - 1);
                    $teamIds[] = $userIds[$randomKey];
                    unset($userIds[$randomKey]);
                    $userIds = array_values($userIds);
                }
            } else {
                $teamIds = $userIds;
            }

            $team = Team::query()
                ->where(function ($query) use ($teamIds) {
                    foreach ($teamIds as $teamId) {
                        $query
                            ->whereHas('users', function ($query) use ($teamId) {
                                $query
                                    ->where('id', $teamId);
                            });
                    }
                })
                ->first();

            if (!isset($team)) {
                $teamName = User::query()
                    ->whereIn('id', $teamIds)
                    ->get()
                    ->pluck('name')
                    ->join('+');

                $team = Team::create([
                    'name' => $teamName
                ]);

                $team->users()->sync($teamIds);
            }

            $teamResult = TeamResult::create([
                'score' => 0,
                'crawl_score' => 0,
                'team_id' => $team->id,
                'event_id' => $event->id
            ]);

            foreach ($team->users as $user) {
                TeamResultUser::create([
                    'score' => 0,
                    'crawl_score' => 0,
                    'team_result_id' => $teamResult->id,
                    'user_id' => $user->id
                ]);
            }
        }

        DB::commit();

        redirect("/events/{$event->id}");
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

        $this->eventTypeId = $this->types[0]->id;
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
