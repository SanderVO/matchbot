<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Collection;
use Livewire\Component;

class SearchUsersTypeahead extends Component
{
    protected $listeners = [
        'refreshUsers' => 'loadUsers'
    ];

    public Collection $users;
    public Collection $selectedUsers;

    public int $organizationId;

    public string $searchTerm;

    public function mount(int $organizationId)
    {
        $this->organizationId = $organizationId;

        $this->users = collect();
        $this->selectedUsers = collect();

        $this->searchTerm = '';
    }

    public function loadUsers()
    {
        $this->users = User::query()
            ->where('organization_id', $this->organizationId)
            ->where('name', 'LIKE', "%{$this->searchTerm}%")
            ->orderBy(
                'name',
                'asc'
            )
            ->limit(10)
            ->get();
    }

    public function resetUsers()
    {
        $this->users = collect();
    }

    public function addUser(int $userId)
    {
        $selectedUser = $this->users
            ->where('id', $userId)
            ->first();

        $this->selectedUsers->push($selectedUser);

        $this->resetUsers();

        $this->searchTerm = '';

        $this->dispatch('userAdded', $userId);
    }

    public function removeUser(int $userId)
    {
        $this->selectedUsers = $this->selectedUsers
            ->reject(function (User $selectedUser) use ($userId) {
                return $userId === $selectedUser->id;
            });

        $this->dispatch('userRemoved', $userId);
    }

    public function render()
    {
        return view(
            'livewire.search-users-typeahead'
        );
    }
}
