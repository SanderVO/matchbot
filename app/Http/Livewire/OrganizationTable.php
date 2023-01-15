<?php

namespace App\Http\Livewire;

use App\Models\Organization;
use Livewire\Component;
use Livewire\WithPagination;

class OrganizationTable extends Component
{
    use WithPagination;

    protected $listeners = [
        'refreshOrganizations' => 'loadOrganizations'
    ];

    protected $organizations;

    public function mount()
    {
        $this->loadOrganizations();
    }

    public function loadOrganizations()
    {
        $this->organizations = Organization::query()
            ->orderBy(
                'name',
                'asc'
            )
            ->paginate();
    }

    public function render()
    {
        return view(
            'livewire.organization-table',
            [
                'organizations' => $this->organizations
            ]
        );
    }
}