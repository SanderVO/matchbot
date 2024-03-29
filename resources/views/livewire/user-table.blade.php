<div class="border-slate-800 rounded-md">
    <table class="table-auto w-full mb-4 bg-slate-900 border-slate-800 border-4 rounded-lg">
        <thead class="border-b-4 border-slate-800">
            <tr>
                <th class="text-left p-4">
                    ID
                </th>

                <th class="text-left p-4">
                    Name
                </th>

                <th class="text-left p-4">
                    Organization
                </th>

                <th class="text-left p-4">
                    Created at
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