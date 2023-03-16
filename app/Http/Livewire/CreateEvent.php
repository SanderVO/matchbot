<?php

namespace App\Http\Livewire;

use App\Models\Event;
use Livewire\Component;

class CreateEvent extends Component
{
    public Event $event;

    protected $rules = [
        'event.name' => [
            'required',
            'string'
        ],
    ];

    public bool $saveIsSuccessful = false;

    /**
     * On component mount
     * 
     * @author Sander van Ooijen <sandervo+github@proton.me>
     * @version 1.0.0
     */
    public function mount()
    {
        $this->event = new Event();
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

        $this->event->save();

        $this->saveIsSuccessful = true;

        $this->emitTo('event-table', 'refreshEvents');
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