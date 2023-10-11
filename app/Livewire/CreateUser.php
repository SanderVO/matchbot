<?php

namespace App\Livewire;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;
use Livewire\Attributes\Rule;

class CreateUser extends Component
{
    public Collection $organizations;

    #[Rule('required|string')]
    public string $name = '';

    #[Rule('required|string')]
    public string $email = '';

    #[Rule('required|numeric|exists:organizations,id')]
    public ?int $organization_id = null;

    public bool $saveIsSuccessful = false;

    /**
     * On component mount
     * 
     * @author Sander van Ooijen <sandervo+github@proton.me>
     * @version 1.0.0
     */
    public function mount()
    {
        $this->organizations = Organization::query()
            ->get();

        $this->organization_id = $this->organizations[0]->id;
    }

    /**
     * Save user action
     * 
     * @author Sander van Ooijen <sandervo+github@proton.me>
     * @version 1.0.0
     */
    public function save(): void
    {
        $this->validate();

        User::create(
            $this->only(['name', 'email', 'organization_id'])
        );

        $this->saveIsSuccessful = true;

        $this->dispatch('refreshUsers')
            ->to('user-table');

        $this->dispatchBrowserEvent('close-modal')
            ->to('user-table');

        $this->dispatch('user-created');
    }

    /**
     * Render component
     * 
     * @return View
     * 
     * @author Sander van Ooijen <sandervo+github@proton.me>
     * @version 1.0.0
     */
    public function render(): View
    {
        return view('livewire.create-user');
    }
}
