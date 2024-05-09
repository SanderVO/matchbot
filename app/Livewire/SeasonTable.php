<?php

namespace App\Livewire;

use App\Models\Season;
use Livewire\Component;
use Livewire\WithPagination;

class SeasonTable extends Component
{
    use WithPagination;

    protected $listeners = [
        'refreshSeasons' => 'loadSeasons'
    ];

    protected $seasons;

    public function mount()
    {
        $this->loadSeasons();
    }

    public function loadSeasons()
    {
        $this->seasons = Season::query()
            ->withCount('events')
            ->orderBy(
                'name',
                'asc'
            )
            ->paginate();
    }

    public function destroySeason(int $seasonId)
    {
        Season::destroy($seasonId);

        $this->loadSeasons();
    }

    public function render()
    {
        return view(
            'livewire.season-table',
            [
                'seasons' => $this->seasons
            ]
        );
    }
}
