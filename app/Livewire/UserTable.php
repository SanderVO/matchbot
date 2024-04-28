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

    public $selectedStatusses = [];

    /**
     * Mount component
     * 
     * @author Sander van Ooijen <sandervo+github@proton.me>
     * @version 1.0.0
     */
    public function mount()
    {
        $this->loadUsers();
    }

    /**
     * Load users
     * 
     * @author Sander van Ooijen <sandervo+github@proton.me>
     * @version 1.0.0
     */
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
            ->paginate()
            ->setPath(route('users.index'));
    }

    /**
     * Update user status
     *
     * @param int $userId
     * @param string $target
     * 
     * @author Sander van Ooijen <sandervo+github@proton.me>
     * @version 1.0.0
     */
    public function updateUserStatus(int $userId, string $target)
    {
        User::query()
            ->where('id', $userId)
            ->update([
                'status' => +$target
            ]);

        $this->loadUsers();
    }

    /**
     * Render component
     * 
     * @author Sander van Ooijen <sandervo+github@proton.me>
     * @version 1.0.0
     */
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
