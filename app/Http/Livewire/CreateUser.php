<?php

namespace App\Http\Livewire;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class CreateUser extends Component
{
    public User $user;

    protected $rules = [
        'user.name' => [
            'required',
            'string'
        ],
        'user.email' => [
            'required',
            'string',
            'email'
        ],
        'user.password' => [
            'required',
            'string',
            'min:8'
        ],
        'user.organization_id' => [
            'required',
            'numeric',
            'exists:organizations,id'
        ]
    ];

    public Collection $organizations;

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

        $this->user->save();
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