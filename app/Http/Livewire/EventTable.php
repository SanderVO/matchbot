<?php

namespace App\Http\Livewire;

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
            ->with([
                'season',
                'eventType',
                'teams.users'
            ])
            ->orderBy(
                'created_at',
                'desc'
            )
            ->paginate();
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