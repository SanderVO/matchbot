<div>
    <table class="table-auto w-full mb-4">
        <thead class="bg-evening">
            <tr>
                <th class="text-left p-4">
                    ID
                </th>

                <th class="text-left p-4">
                    Name
                </th>

                <th class="text-left p-4">
                    Organisatie
                </th>

                <th class="text-left p-4">
                    Aangemaakt op
                </th>
            </tr>
        </thead>

        <tbody>
            @forelse ($users as $user)
            <tr>
                <td class="text-left p-4">
                    {{ $user->id }}
                </td>

                <td class="text-left p-4">
                    {{ $user->name }}
                </td>

                <td class="text-left p-4">
                    {{ $user->organization->name }}
                </td>

                <td class="text-left p-4">
                    {{ $user->created_at }}
                </td>
            </tr>
            @empty
            <tr>
                <td class="p-4">No users found</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{ $users->links('pagination::tailwind') }}
</div>