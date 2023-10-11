<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class UserTable extends Component
{
    use WithPagination;

    protected $listeners = [
        'refreshUsers' => 'loadUsers'
    ];

    protected $users;

    public function mount()
    {
        $this->loadUsers();
    }

    public function loadUsers()
    {
        $this->users = User::query()
            ->with([
                'organization'
            ])
            ->orderBy(
                'name',
                'asc'
            )
            ->paginate();
    }

    public function render()
    {
        return view(
            'livewire.user-table',
            [
                'users' => $this->users
            ]
        );
    }
}