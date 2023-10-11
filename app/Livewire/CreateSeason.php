<?php

namespace App\Livewire;

use App\Models\Season;
use Livewire\Component;
use Livewire\Attributes\Rule;

class CreateSeason extends Component
{
    #[Rule('required|string')]
    public string $name = '';

    public bool $saveIsSuccessful = false;

    /**
     * Save season action
     * 
     * @author Sander van Ooijen <sandervo+github@proton.me>
     * @version 1.0.0
     */
    public function save(): void
    {
        $this->validate();

        Season::create(
            $this->only(['name'])
        );

        $this->saveIsSuccessful = true;

        $this->dispatch('refreshSeasons')
            ->to('season-table');

        $this->dispatch('season-created');
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
        return view('livewire.create-season');
    }
}
