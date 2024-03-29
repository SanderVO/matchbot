<?php

namespace App\Livewire;

use App\Models\Event;
use Livewire\Component;
use Livewire\Attributes\Rule;

class CreateEvent extends Component
{
    public Event $event;

    #[Rule('required|string')]
    public string $name = '';

    public bool $saveIsSuccessful = false;

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
            $this->only(['name'])
        );

        $this->saveIsSuccessful = true;

        $this->dispatch('refreshEvents')
            ->to('event-table');

        $this->dispatch('event-created');
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
