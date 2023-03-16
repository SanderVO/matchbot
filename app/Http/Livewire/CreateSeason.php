<?php

namespace App\Http\Livewire;

use App\Models\Season;
use Livewire\Component;

class CreateSeason extends Component
{
    public Season $season;

    protected $rules = [
        'season.name' => [
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
        $this->season = new Season();
    }

    /**
     * Save season action
     * 
     * @author Sander van Ooijen <sandervo+github@proton.me>
     * @version 1.0.0
     */
    public function save(): void
    {
        $this->validate();

        $this->season->save();

        $this->saveIsSuccessful = true;

        $this->emitTo('season-table', 'refreshSeasons');
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