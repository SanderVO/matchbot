<?php

namespace App\Livewire;

use App\Models\Event;
use Livewire\Component;
use Livewire\WithPagination;

class EventTable extends Component
{
    use WithPagination;

    protected $listeners = [
        'refreshEvents' => 'loadEvents'
    ];

    protected $events;

    public function mount()
    {
        $this->loadEvents();
    }

    public function loadEvents()
    {
        $this->events = Event::query()
            ->has('teamResults')
            ->with([
                'season',
                'eventType.sport',
                'teamResults.team.users'
            ])
            ->orderBy(
                'created_at',
                'desc'
            )
            ->paginate()
            ->setPath(route('events.index'));
    }

    public function destroyEvent(int $eventId)
    {
        Event::destroy($eventId);

        $this->loadEvents();
    }

    public function render()
    {
        return view(
            'livewire.event-table',
            [
                'events' => $this->events
            ]
        );
    }
}
