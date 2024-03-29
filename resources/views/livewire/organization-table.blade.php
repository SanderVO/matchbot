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
                    Players
                </th>

                <th class="text-left p-4">
                    Created at
                </th>
            </tr>
        </thead>

        <tbody>
            @forelse ($organizations as $organization)
            <tr>
                <td class="text-left p-4">
                    {{ $organization->id }}
                </td>

                <td class="text-left p-4">
                    {{ $organization->name }}
                </td>

                <td class="text-left p-4">
                    {{ $organization->users_count }}
                </td>

                <td class="text-left p-4">
                    {{ $organization->created_at }}
                </td>
            </tr>
            @empty
            <tr>
                <td class="p-4">No organizations found</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{ $organizations->links('pagination::tailwind') }}
</div>