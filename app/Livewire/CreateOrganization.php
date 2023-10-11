<?php

namespace App\Livewire;

use App\Models\Organization;
use Livewire\Component;
use Livewire\Attributes\Rule;

class CreateOrganization extends Component
{
    #[Rule('required|string')]
    public string $name = '';

    public bool $saveIsSuccessful = false;

    /**
     * Save organization action
     * 
     * @author Sander van Ooijen <sandervo+github@proton.me>
     * @version 1.0.0
     */
    public function save(): void
    {
        $this->validate();

        Organization::create(
            $this->only(['name'])
        );

        $this->saveIsSuccessful = true;

        $this->dispatch('refreshOrganizations')
            ->to('organization-table');

        $this->dispatch('organization-created');
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
