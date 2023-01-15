<?php

namespace App\Http\Livewire;

use App\Models\Organization;
use Livewire\Component;

class CreateOrganization extends Component
{
    public Organization $organization;

    protected $rules = [
        'organization.name' => [
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
        $this->organization = new Organization();
    }

    /**
     * Save organization action
     * 
     * @author Sander van Ooijen <sandervo+github@proton.me>
     * @version 1.0.0
     */
    public function save(): void
    {
        $this->validate();

        $this->organization->save();

        $this->saveIsSuccessful = true;

        $this->emitTo('organization-table', 'refreshOrganizations');
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
        return view('livewire.create-organization');
    }
}