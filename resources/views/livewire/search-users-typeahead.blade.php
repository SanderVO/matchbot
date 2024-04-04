<label class="block" for="type">
    <span>Users</span>

    <div class="relative">
        <input class="text-black w-full" wire:model.live="searchTerm" wire:keydown.debounce.300ms="loadUsers()">

        @if (count($users) > 0)
        <div class="absolute bg-white p-2 text-black w-full" wire:click.outside='resetUsers()'>
            <ul>
                @foreach ($users as $user)
                <li wire:click="addUser({{ $user->id }})"">
                    {{ $user->name }}
                </li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>

    @if (count($selectedUsers) > 0)
    <div class=" mt-2 flex flex-row w-full">
                    @foreach ($selectedUsers as $selectedUser)
                    <div class="rounded-lg p-2 bg-purple-500 text-white border border-purple-400 flex flex-row align-items-center w-fit mr-2 cursor-pointer"
                        wire:click="removeUser({{ $selectedUser->id }})">
                        <div class=" mr-4">
                            {{ $selectedUser->name }}
                        </div>

                        <div>
                            X
                        </div>
                    </div>
                    @endforeach
        </div>
        @endif
</label>